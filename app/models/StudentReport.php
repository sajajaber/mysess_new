<?php
class StudentReport extends Model
{
  protected $table = 'students';

  // Build the full normalized report for one student.
  // Pass a date range to scope it (semester), or null for all-time.
  public function build($studentId, $startDate = null, $endDate = null)
  {
    return [
      'student'      => $this->first(['id' => $studentId]),
      'goals'        => $this->buildGoals($studentId, $startDate, $endDate),
      'teacch'       => $this->buildTeacch($studentId, $startDate, $endDate),
      'sessions'     => $this->getSessions($studentId, $startDate, $endDate),
      'observations' => $this->getObservations($studentId, $startDate, $endDate),
      'therapies'    => $this->getTherapies($studentId, $startDate, $endDate),
      'homework'     => $this->getHomework($studentId, $startDate, $endDate),
      'staff'        => $this->getStaff($studentId),
      'medications'  => $this->getMedications($studentId),
      'healthRecords' => $this->getHealthRecords($studentId),
      'healthEvents' => $this->getHealthEvents($studentId),
      'boarding'     => null,
    ];
  }

  private function getMedications($studentId)
  {
    return $this->query(
      "SELECT * FROM medications
        WHERE student_id = :sid AND is_active = 1
        ORDER BY created_at DESC",
      ['sid' => $studentId]
    ) ?: [];
  }

  private function getHealthRecords($studentId)
  {
    return $this->query(
      "SELECT * FROM health_records
        WHERE student_id = :sid
        ORDER BY recorded_at DESC",
      ['sid' => $studentId]
    ) ?: [];
  }

  private function getHealthEvents($studentId)
  {
    return $this->query(
      "SELECT * FROM health_events
        WHERE student_id = :sid
        ORDER BY recorded_at DESC",
      ['sid' => $studentId]
    ) ?: [];
  }

  private function rangeSql($column, $startDate, $endDate, &$params)
  {
    if ($startDate && $endDate) {
      $params['start'] = $startDate;
      $params['end']   = $endDate;
      return " AND $column BETWEEN :start AND :end";
    }
    return '';
  }

  private function buildGoals($studentId, $startDate, $endDate)
  {
    $goals = $this->query(
      "SELECT id, goal_text, category, status, target_date
         FROM iep_goals
        WHERE student_id = :sid
        ORDER BY created_at DESC",
      ['sid' => $studentId]
    ) ?: [];

    $out = [];
    foreach ($goals as $g) {
      $baseline = $this->scoreInRange($g->id, $startDate, $endDate, 'ASC');
      $current  = $this->scoreInRange($g->id, $startDate, $endDate, 'DESC');
      $entries  = $this->countScores($g->id, $startDate, $endDate);

      $milestones = $this->query(
        "SELECT description, is_achieved FROM iep_milestones
          WHERE goal_id = :gid ORDER BY created_at ASC",
        ['gid' => $g->id]
      ) ?: [];

      $done = 0;
      foreach ($milestones as $m) {
        if ($m->is_achieved) $done++;
      }

      $out[] = [
        'goal'           => $g,
        'baseline'       => $baseline,
        'current'        => $current,
        'change'         => ($baseline !== null && $current !== null) ? $current - $baseline : null,
        'entries'        => $entries,
        'status'         => $this->statusLabel($g->status, $current),
        'milestones'     => $milestones,
        'milestoneDone'  => $done,
        'milestoneTotal' => count($milestones),
      ];
    }
    return $out;
  }

  private function scoreInRange($goalId, $startDate, $endDate, $dir)
  {
    $params = ['gid' => $goalId];
    $range  = $this->rangeSql('recorded_at', $startDate, $endDate, $params);
    $rows = $this->query(
      "SELECT score FROM goal_progress
        WHERE goal_id = :gid $range
        ORDER BY recorded_at $dir
        LIMIT 1",
      $params
    );
    return $rows ? (int)$rows[0]->score : null;
  }

  private function countScores($goalId, $startDate, $endDate)
  {
    $params = ['gid' => $goalId];
    $range  = $this->rangeSql('recorded_at', $startDate, $endDate, $params);
    $rows = $this->query(
      "SELECT COUNT(*) AS c FROM goal_progress WHERE goal_id = :gid $range",
      $params
    );
    return $rows ? (int)$rows[0]->c : 0;
  }

  private function statusLabel($status, $current)
  {
    if ($status === 'achieved') return 'Met';
    if ($current === null)      return 'Not Met';
    if ($current >= 80)         return 'Met';
    if ($current > 0)           return 'In Progress';
    return 'Not Met';
  }

  private function buildTeacch($studentId, $startDate, $endDate)
  {
    $levelToPercent = ['full_prompt' => 33, 'partial_prompt' => 66, 'independent' => 100];

    $schedules = $this->query(
      "SELECT id, title FROM teacch_schedules
        WHERE student_id = :sid ORDER BY created_at DESC",
      ['sid' => $studentId]
    ) ?: [];

    $blocks = [];
    foreach ($schedules as $sched) {
      $tasks = $this->query(
        "SELECT id, title, task_order FROM teacch_tasks
          WHERE schedule_id = :scid ORDER BY task_order ASC",
        ['scid' => $sched->id]
      ) ?: [];

      $taskRows = [];
      $sum = 0; $rated = 0;
      foreach ($tasks as $task) {
        $params = ['tid' => $task->id];
        $range  = $this->rangeSql('session_date', $startDate, $endDate, $params);
        $latest = $this->query(
          "SELECT independence_level FROM teacch_progress
            WHERE task_id = :tid $range
            ORDER BY session_date DESC, created_at DESC LIMIT 1",
          $params
        );
        $cntParams = ['tid' => $task->id];
        $cntRange  = $this->rangeSql('session_date', $startDate, $endDate, $cntParams);
        $cntRows = $this->query(
          "SELECT COUNT(*) AS c FROM teacch_progress WHERE task_id = :tid $cntRange",
          $cntParams
        );

        $level   = $latest ? $latest[0]->independence_level : null;
        $percent = $level ? $levelToPercent[$level] : 0;
        if ($level) { $sum += $percent; $rated++; }

        $taskRows[] = [
          'title'   => $task->title,
          'order'   => $task->task_order,
          'level'   => $level,
          'percent' => $percent,
          'entries' => $cntRows ? (int)$cntRows[0]->c : 0,
        ];
      }

      $blocks[] = [
        'title'   => $sched->title,
        'tasks'   => $taskRows,
        'percent' => $rated ? (int)round($sum / $rated) : 0,
        'rated'   => $rated,
        'total'   => count($tasks),
      ];
    }
    return $blocks;
  }

  private function getSessions($studentId, $startDate, $endDate)
  {
    $params = ['sid' => $studentId];
    $range  = $this->rangeSql('cs.session_date', $startDate, $endDate, $params);
    return $this->query(
      "SELECT cs.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM classroom_sessions cs
         LEFT JOIN users u ON cs.teacher_id = u.id
        WHERE cs.student_id = :sid $range
        ORDER BY cs.session_date DESC",
      $params
    ) ?: [];
  }

  private function getObservations($studentId, $startDate, $endDate)
  {
    $params = ['sid' => $studentId];
    $range  = $this->rangeSql('ao.created_at', $startDate, $endDate, $params);
    return $this->query(
      "SELECT ao.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM academic_observations ao
         LEFT JOIN users u ON ao.teacher_id = u.id
        WHERE ao.student_id = :sid $range
        ORDER BY ao.created_at DESC",
      $params
    ) ?: [];
  }

  private function getTherapies($studentId, $startDate, $endDate)
  {
    $params = ['sid' => $studentId];
    $range  = $this->rangeSql('ts.session_date', $startDate, $endDate, $params);
    return $this->query(
      "SELECT ts.*, u.first_name AS therapist_first, u.last_name AS therapist_last,
              ig.goal_text AS goal_addressed
         FROM therapy_sessions ts
         LEFT JOIN users u ON ts.therapist_id = u.id
         LEFT JOIN iep_goals ig ON ts.goal_addressed_id = ig.id
        WHERE ts.student_id = :sid $range
        ORDER BY ts.session_date DESC",
      $params
    ) ?: [];
  }

  private function getHomework($studentId, $startDate, $endDate)
  {
    $params = ['sid' => $studentId];
    $range  = $this->rangeSql('h.due_date', $startDate, $endDate, $params);
    return $this->query(
      "SELECT h.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM homework h
         LEFT JOIN users u ON h.assigned_by = u.id
        WHERE h.student_id = :sid $range
        ORDER BY h.due_date DESC",
      $params
    ) ?: [];
  }

  private function getStaff($studentId)
  {
    return $this->query(
      "SELECT u.first_name, u.last_name, u.role, u.email, sa.role_type
         FROM users u
         JOIN student_assignments sa ON sa.user_id = u.id
        WHERE sa.student_id = :sid AND sa.end_date IS NULL
        UNION
       SELECT u.first_name, u.last_name, u.role, u.email, 'nurse' AS role_type
         FROM users u
         JOIN nurse_student ns ON ns.nurse_id = u.id
        WHERE ns.student_id = :sid
        ORDER BY role_type ASC",
      ['sid' => $studentId]
    ) ?: [];
  }

  // Boarding stats (averages + breakdowns). Call only for boarding students.
  public function boardingStats($studentId)
  {
    $stats = [
      'sleep_count' => 0, 'avg_sleep_hours' => null, 'avg_bedtime' => null,
      'avg_wakeup' => null, 'sleep_quality' => [], 'mood' => [], 'appetite' => [], 'total_logs' => 0,
    ];

    $sleepRows = $this->query(
      "SELECT bedtime, wakeup_time FROM boarding_logs
        WHERE student_id = :sid AND log_type = 'sleep'
          AND bedtime IS NOT NULL AND wakeup_time IS NOT NULL",
      ['sid' => $studentId]
    ) ?: [];

    if (!empty($sleepRows)) {
      $totalMins = 0; $bedMins = 0; $wakeMins = 0; $n = 0;
      foreach ($sleepRows as $r) {
        $bed = strtotime($r->bedtime); $wake = strtotime($r->wakeup_time);
        if ($bed === false || $wake === false) continue;
        $bedM  = (int)date('G', $bed) * 60 + (int)date('i', $bed);
        $wakeM = (int)date('G', $wake) * 60 + (int)date('i', $wake);
        $duration = $wakeM - $bedM;
        if ($duration <= 0) $duration += 24 * 60;
        $totalMins += $duration; $bedMins += $bedM; $wakeMins += $wakeM; $n++;
      }
      if ($n > 0) {
        $stats['sleep_count']     = $n;
        $stats['avg_sleep_hours'] = round($totalMins / $n / 60, 1);
        $avgBed  = (int)round($bedMins / $n);
        $avgWake = (int)round($wakeMins / $n);
        $stats['avg_bedtime'] = sprintf('%02d:%02d', intdiv($avgBed, 60) % 24, $avgBed % 60);
        $stats['avg_wakeup']  = sprintf('%02d:%02d', intdiv($avgWake, 60) % 24, $avgWake % 60);
      }
    }

    $stats['sleep_quality'] = $this->boardingCountBy($studentId, 'sleep', 'sleep_quality');
    $stats['mood']          = $this->boardingCountBy($studentId, 'behavior', 'mood_indicator');
    $stats['appetite']      = $this->boardingCountBy($studentId, 'meal', 'appetite_level');

    $totalRows = $this->query(
      "SELECT COUNT(*) AS c FROM boarding_logs WHERE student_id = :sid",
      ['sid' => $studentId]
    );
    $stats['total_logs'] = $totalRows ? (int)$totalRows[0]->c : 0;
    return $stats;
  }

  private function boardingCountBy($studentId, $logType, $column)
  {
    $rows = $this->query(
      "SELECT $column AS val, COUNT(*) AS c FROM boarding_logs
        WHERE student_id = :sid AND log_type = :lt AND $column IS NOT NULL
        GROUP BY $column",
      ['sid' => $studentId, 'lt' => $logType]
    ) ?: [];
    $out = [];
    foreach ($rows as $r) { $out[$r->val] = (int)$r->c; }
    return $out;
  }

  // Standard semester options for the date filter
  public function semesterOptions()
  {
    $year = (int)date('Y');
    $opts = [];
    for ($y = $year; $y >= $year - 2; $y--) {
      $opts[($y) . '-09-01|' . ($y + 1) . '-01-31'] = 'Semester 1 (' . $y . '–' . ($y + 1) . ')';
      $opts[($y + 1) . '-02-01|' . ($y + 1) . '-06-30'] = 'Semester 2 (' . $y . '–' . ($y + 1) . ')';
    }
    return $opts;
  }
}
