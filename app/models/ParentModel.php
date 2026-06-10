<?php
class ParentModel extends Model
{
  protected $table = 'students';

  public function getMyChildren($parentId)
  {
    return $this->query(
      "SELECT * FROM students
        WHERE guardian_id = :parent_id
          AND is_active = 1
        ORDER BY first_name ASC",
      ['parent_id' => $parentId]
    );
  }

  public function isThisChildMine($studentId, $parentId)
  {
    $rows = $this->query(
      "SELECT id FROM students
        WHERE id = :student_id
          AND guardian_id = :parent_id
        LIMIT 1",
      ['student_id' => $studentId, 'parent_id' => $parentId]
    );
    return !empty($rows);
  }

  public function getCareTeam($studentId)
  {
    return $this->query(
      "SELECT u.id, u.first_name, u.last_name, u.role, u.email, sa.role_type
         FROM users u
         JOIN student_assignments sa ON sa.user_id = u.id
        WHERE sa.student_id = :student_id
          AND sa.end_date IS NULL
        UNION
       SELECT u.id, u.first_name, u.last_name, u.role, u.email, 'nurse' AS role_type
         FROM users u
         JOIN nurse_student ns ON ns.nurse_id = u.id
        WHERE ns.student_id = :student_id
        ORDER BY role_type ASC",
      ['student_id' => $studentId]
    );
  }

  public function getIepGoals($studentId)
  {
    return $this->query(
      "SELECT id, goal_text, category, status, target_date, created_at
         FROM iep_goals
        WHERE student_id = :student_id
        ORDER BY created_at DESC",
      ['student_id' => $studentId]
    );
  }

  public function getMilestones($studentId)
  {
    return $this->query(
      "SELECT m.*
         FROM iep_milestones m
         JOIN iep_goals ig ON ig.id = m.goal_id
        WHERE ig.student_id = :student_id
        ORDER BY m.created_at ASC",
      ['student_id' => $studentId]
    );
  }

  public function getLatestProgressMap($studentId)
  {
    $rows = $this->query(
      "SELECT gp.goal_id, gp.score
         FROM goal_progress gp
         JOIN iep_goals ig ON ig.id = gp.goal_id
        WHERE ig.student_id = :student_id
        ORDER BY gp.recorded_at DESC",
      ['student_id' => $studentId]
    );

    $map = [];
    foreach ($rows ?: [] as $r) {
      if (!isset($map[$r->goal_id])) {
        $map[$r->goal_id] = (int)$r->score;
      }
    }
    return $map;
  }

  public function getTeacchSchedules($studentId)
  {
    return $this->query(
      "SELECT * FROM teacch_schedules
        WHERE student_id = :student_id
        ORDER BY created_at DESC",
      ['student_id' => $studentId]
    );
  }

  public function getTeacchProgress($studentId)
  {
    return $this->query(
      "SELECT tp.*, tt.title AS task_title
         FROM teacch_progress tp
         JOIN teacch_tasks tt ON tt.id = tp.task_id
        WHERE tp.student_id = :student_id
        ORDER BY tp.session_date DESC, tp.created_at DESC
        LIMIT 30",
      ['student_id' => $studentId]
    );
  }

  public function getTherapySessions($studentId)
  {
    return $this->query(
      "SELECT ts.*, u.first_name AS therapist_first, u.last_name AS therapist_last,
              ig.goal_text AS goal_addressed
         FROM therapy_sessions ts
         LEFT JOIN users u ON ts.therapist_id = u.id
         LEFT JOIN iep_goals ig ON ts.goal_addressed_id = ig.id
        WHERE ts.student_id = :student_id
        ORDER BY ts.session_date DESC, ts.created_at DESC
        LIMIT 30",
      ['student_id' => $studentId]
    );
  }

  public function getClassroomSessions($studentId)
  {
    return $this->query(
      "SELECT cs.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM classroom_sessions cs
         LEFT JOIN users u ON cs.teacher_id = u.id
        WHERE cs.student_id = :student_id
        ORDER BY cs.session_date DESC, cs.created_at DESC
        LIMIT 30",
      ['student_id' => $studentId]
    );
  }

  public function getObservations($studentId)
  {
    return $this->query(
      "SELECT ao.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM academic_observations ao
         LEFT JOIN users u ON ao.teacher_id = u.id
        WHERE ao.student_id = :student_id
        ORDER BY ao.created_at DESC
        LIMIT 20",
      ['student_id' => $studentId]
    );
  }

  public function getProgressReports($studentId)
  {
    return $this->query(
      "SELECT pr.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM progress_reports pr
         LEFT JOIN users u ON pr.teacher_id = u.id
        WHERE pr.student_id = :student_id
        ORDER BY pr.created_at DESC
        LIMIT 10",
      ['student_id' => $studentId]
    );
  }

  public function getHomework($studentId)
  {
    return $this->query(
      "SELECT h.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM homework h
         LEFT JOIN users u ON h.assigned_by = u.id
        WHERE h.student_id = :student_id
        ORDER BY h.due_date DESC, h.created_at DESC
        LIMIT 30",
      ['student_id' => $studentId]
    );
  }

  public function getMedications($studentId)
  {
    return $this->query(
      "SELECT * FROM medications
        WHERE student_id = :student_id
          AND is_active = 1
        ORDER BY created_at DESC",
      ['student_id' => $studentId]
    );
  }

  public function getMedicationLogs($studentId)
  {
    return $this->query(
      "SELECT ml.*, m.name AS med_name, u.first_name AS by_first, u.last_name AS by_last
         FROM medication_logs ml
         JOIN medications m ON m.id = ml.medication_id
         LEFT JOIN users u ON ml.administered_by = u.id
        WHERE m.student_id = :student_id
        ORDER BY ml.administered_at DESC
        LIMIT 20",
      ['student_id' => $studentId]
    );
  }

  public function getHealthEvents($studentId)
  {
    return $this->query(
      "SELECT * FROM health_events
        WHERE student_id = :student_id
        ORDER BY recorded_at DESC
        LIMIT 20",
      ['student_id' => $studentId]
    );
  }

  public function getHealthRecords($studentId)
  {
    return $this->query(
      "SELECT * FROM health_records
        WHERE student_id = :student_id
        ORDER BY recorded_at DESC
        LIMIT 30",
      ['student_id' => $studentId]
    );
  }

  public function getBoardingLogs($studentId)
  {
    return $this->query(
      "SELECT * FROM boarding_logs
        WHERE student_id = :student_id
        ORDER BY log_date DESC, created_at DESC
        LIMIT 50",
      ['student_id' => $studentId]
    );
  }

  public function getCheckins($studentId)
  {
    return $this->query(
      "SELECT * FROM checkin_logs
        WHERE student_id = :student_id
        ORDER BY check_time DESC
        LIMIT 30",
      ['student_id' => $studentId]
    );
  }

  // Boarding stats: averages and breakdowns (mirrors the admin report)
  public function getBoardingStats($studentId)
  {
    $stats = [
      'sleep_count'     => 0,
      'avg_sleep_hours' => null,
      'avg_bedtime'     => null,
      'avg_wakeup'      => null,
      'sleep_quality'   => [],
      'mood'            => [],
      'appetite'        => [],
      'total_logs'      => 0,
    ];

    $sleepRows = $this->query(
      "SELECT bedtime, wakeup_time
         FROM boarding_logs
        WHERE student_id = :sid AND log_type = 'sleep'
          AND bedtime IS NOT NULL AND wakeup_time IS NOT NULL",
      ['sid' => $studentId]
    ) ?: [];

    if (!empty($sleepRows)) {
      $totalMins = 0; $bedMins = 0; $wakeMins = 0; $n = 0;
      foreach ($sleepRows as $r) {
        $bed  = strtotime($r->bedtime);
        $wake = strtotime($r->wakeup_time);
        if ($bed === false || $wake === false) continue;

        $bedM  = (int)date('G', $bed) * 60 + (int)date('i', $bed);
        $wakeM = (int)date('G', $wake) * 60 + (int)date('i', $wake);

        $duration = $wakeM - $bedM;
        if ($duration <= 0) { $duration += 24 * 60; }

        $totalMins += $duration;
        $bedMins   += $bedM;
        $wakeMins  += $wakeM;
        $n++;
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

    $stats['sleep_quality'] = $this->countBoardingBy($studentId, 'sleep', 'sleep_quality');
    $stats['mood']          = $this->countBoardingBy($studentId, 'behavior', 'mood_indicator');
    $stats['appetite']      = $this->countBoardingBy($studentId, 'meal', 'appetite_level');

    $totalRows = $this->query(
      "SELECT COUNT(*) AS c FROM boarding_logs WHERE student_id = :sid",
      ['sid' => $studentId]
    );
    $stats['total_logs'] = $totalRows ? (int)$totalRows[0]->c : 0;

    return $stats;
  }

  private function countBoardingBy($studentId, $logType, $column)
  {
    $rows = $this->query(
      "SELECT $column AS val, COUNT(*) AS c
         FROM boarding_logs
        WHERE student_id = :sid AND log_type = :lt AND $column IS NOT NULL
        GROUP BY $column",
      ['sid' => $studentId, 'lt' => $logType]
    ) ?: [];

    $out = [];
    foreach ($rows as $r) {
      $out[$r->val] = (int)$r->c;
    }
    return $out;
  }

  public function getSharedReports($parentId)
  {
    return $this->query(
      "SELECT sr.id, sr.student_id, sr.created_at,
              s.first_name, s.last_name
         FROM shared_reports sr
         JOIN students s ON s.id = sr.student_id
        WHERE s.guardian_id = :pid
        ORDER BY sr.created_at DESC",
      ['pid' => $parentId]
    );
  }

  public function isReportShared($studentId, $parentId)
  {
    $rows = $this->query(
      "SELECT sr.id FROM shared_reports sr
         JOIN students s ON s.id = sr.student_id
        WHERE sr.student_id = :sid AND s.guardian_id = :pid
        LIMIT 1",
      ['sid' => $studentId, 'pid' => $parentId]
    );
    return !empty($rows);
  }
}
