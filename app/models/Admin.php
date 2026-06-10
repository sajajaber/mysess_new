<?php
class Admin extends User
{
  protected $table = 'users';
  public function getDashboardStats()
  {
    $result = $this->query(
      "SELECT
              (SELECT COUNT(*) FROM users WHERE is_active = 1) AS total_users,
              (SELECT COUNT(*) FROM users WHERE role = 'teacher' AND is_active = 1) AS total_teachers,
              (SELECT COUNT(*) FROM users WHERE role = 'therapist' AND is_active = 1) AS total_therapists,
              (SELECT COUNT(*) FROM users WHERE role = 'nurse' AND is_active = 1) AS total_nurses,
              (SELECT COUNT(*) FROM users WHERE role = 'parent' AND is_active = 1) AS total_parents,
              (SELECT COUNT(*) FROM users WHERE role = 'boarding_staff' AND is_active = 1) AS total_boarding_staff,
              (SELECT COUNT(*) FROM users WHERE role = 'security_guard' AND is_active = 1) AS total_security,
              (SELECT COUNT(*) FROM students WHERE is_active = 1) AS total_students,
              (SELECT COUNT(*) FROM students WHERE is_active = 0) AS archived_students,
              (SELECT COUNT(*) FROM medications WHERE is_active = 1) AS active_medications,
              (SELECT COUNT(DISTINCT student_id) FROM medications WHERE is_active = 1) AS students_on_meds"
    );

    return $result ? $result[0] : null;
  }

  public function getAllUsers($role = null)
  {
    if ($role) {
      return $this->where(['role' => $role, 'is_active' => 1]);
    }
    return $this->query(
      "SELECT * FROM users ORDER BY role ASC, last_name ASC"
    );
  }

  // Password must already be hashed before calling this
  public function createUser($data)
  {
    return $this->insert([
      'first_name' => $data['first_name'],
      'last_name'  => $data['last_name'],
      'email'      => $data['email'],
      'phone'      => $data['phone']    ?? null,
      'password'   => $data['password'],
      'role'       => $data['role'],
      'is_active'  => 1,
    ]);
  }

  public function updateUser($id, $data)
  {
    return $this->update($id, [
      'first_name' => $data['first_name'],
      'last_name'  => $data['last_name'],
      'email'      => $data['email'],
      'phone'      => $data['phone'] ?? null,
      'role'       => $data['role'],
    ]);
  }

  public function updateUserPassword($id, $plain_password)
  {
    return $this->update($id, [
      'password' => password_hash($plain_password, PASSWORD_DEFAULT),
    ]);
  }

  public function deactivateUser($id)
  {
    return $this->update($id, ['is_active' => 0]);
  }

  public function activateUser($id)
  {
    return $this->update($id, ['is_active' => 1]);
  }

  // exclude_id is necessary for checking all users emails EXCEPT the user being edited 
  public function emailExists($email, $exclude_id = null)
  {
    if ($exclude_id) {
      $result = $this->query(
        "SELECT COUNT(*) as count FROM users WHERE email = :email AND id != :id",
        ['email' => $email, 'id' => $exclude_id]
      );
    } else {
      $result = $this->query(
        "SELECT COUNT(*) as count FROM users WHERE email = :email",
        ['email' => $email]
      );
    }
    return $result && $result[0]->count > 0;
  }

  public function getAllStudents($active_only = true)
  {
    $where = $active_only ? "WHERE s.is_active = 1" : "";
    return $this->query(
      "SELECT s.*, u.first_name as guardian_first_name, u.last_name as guardian_last_name
      FROM students s
      LEFT JOIN users u ON s.guardian_id = u.id
      $where
      ORDER BY s.last_name ASC"
    );
  }

  public function getStudentById($id)
  {
    $result = $this->query(
      "SELECT s.*, u.first_name as guardian_first_name, u.last_name as guardian_last_name
      FROM students s
      LEFT JOIN users u ON s.guardian_id = u.id
      WHERE s.id = :id
      LIMIT 1",
      ['id' => $id]
    );
    return $result ? $result[0] : null;
  }

  public function createStudent($data)
  {
    return $this->query(
      "INSERT INTO students
          (first_name, last_name, date_of_birth, gender, diagnosis, is_boarding, enrollment_date, guardian_id, is_active)
        VALUES
          (:first_name, :last_name, :date_of_birth, :gender, :diagnosis, :is_boarding, :enrollment_date, :guardian_id, 1)",
      [
        'first_name'      => $data['first_name'],
        'last_name'       => $data['last_name'],
        'date_of_birth'   => $data['date_of_birth'],
        'gender'          => $data['gender'],
        'diagnosis'       => $data['diagnosis']      ?? null,
        'is_boarding'     => !empty($data['is_boarding']) ? 1 : 0,
        'enrollment_date' => $data['enrollment_date'],
        'guardian_id'     => $data['guardian_id']    ?? null,
      ]
    );
  }

  public function updateStudent($id, $data)
  {
    return $this->query(
      "UPDATE students SET
        first_name      = :first_name,
        last_name       = :last_name,
        date_of_birth   = :date_of_birth,
        gender          = :gender,
        diagnosis       = :diagnosis,
        enrollment_date = :enrollment_date,
        guardian_id     = :guardian_id
      WHERE id = :id",
      [
        'first_name'      => $data['first_name'],
        'last_name'       => $data['last_name'],
        'date_of_birth'   => $data['date_of_birth'],
        'gender'          => $data['gender'],
        'diagnosis'       => $data['diagnosis']      ?? null,
        'enrollment_date' => $data['enrollment_date'],
        'guardian_id'     => $data['guardian_id']    ?? null,
        'id'              => $id,
      ]
    );
  }

  public function archiveStudent($id)
  {
    return $this->query(
      "UPDATE students SET is_active = 0 WHERE id = :id",
      ['id' => $id]
    );
  }

  public function restoreStudent($id)
  {
    return $this->query(
      "UPDATE students SET is_active = 1 WHERE id = :id",
      ['id' => $id]
    );
  }

  //  get all students assigned to a specific staff member
  public function getStudentsForStaff($user_id, $role)
  {
    if ($role === 'nurse') {
      return $this->query(
        "SELECT s.* FROM students s
        JOIN nurse_student ns ON s.id = ns.student_id
        WHERE ns.nurse_id = :user_id AND s.is_active = 1
        ORDER BY s.last_name ASC",
        ['user_id' => $user_id]
      );
    }
    return $this->query(
      "SELECT s.* FROM students s
      JOIN student_assignments sa ON s.id = sa.student_id
      WHERE sa.user_id = :user_id AND sa.role_type = :role AND sa.end_date IS NULL AND s.is_active = 1
      ORDER BY s.last_name ASC",
      ['user_id' => $user_id, 'role' => $role]
    );
  }

  public function getStaffByRole($role)
  {
    return $this->where(['role' => $role, 'is_active' => 1]);
  }

  public function assignStudentToNurse($student_id, $nurse_id)
  {
    $exists = $this->query(
      "SELECT COUNT(*) as count FROM nurse_student WHERE nurse_id = :nurse_id AND student_id = :student_id",
      ['nurse_id' => $nurse_id, 'student_id' => $student_id]
    );
    if ($exists && $exists[0]->count > 0) return false;

    return $this->query(
      "INSERT INTO nurse_student (nurse_id, student_id) VALUES (:nurse_id, :student_id)",
      ['nurse_id' => $nurse_id, 'student_id' => $student_id]
    );
  }

  public function assignStudentToStaff($student_id, $user_id, $role_type)
  {
    $exists = $this->query(
      "SELECT COUNT(*) as count FROM student_assignments
        WHERE user_id = :user_id AND student_id = :student_id AND role_type = :role_type AND end_date IS NULL",
      ['user_id' => $user_id, 'student_id' => $student_id, 'role_type' => $role_type]
    );
    if ($exists && $exists[0]->count > 0) return false;

    return $this->query(
      "INSERT INTO student_assignments (student_id, user_id, role_type, start_date)
        VALUES (:student_id, :user_id, :role_type, CURDATE())",
      ['student_id' => $student_id, 'user_id' => $user_id, 'role_type' => $role_type]
    );
  }

  public function removeNurseAssignment($student_id, $nurse_id)
  {
    return $this->query(
      "DELETE FROM nurse_student WHERE nurse_id = :nurse_id AND student_id = :student_id",
      ['nurse_id' => $nurse_id, 'student_id' => $student_id]
    );
  }

  // Soft-delete: preserves history with end_date
  public function removeStaffAssignment($student_id, $user_id, $role_type)
  {
    return $this->query(
      "UPDATE student_assignments SET end_date = CURDATE()
        WHERE user_id = :user_id AND student_id = :student_id AND role_type = :role_type AND end_date IS NULL",
      ['user_id' => $user_id, 'student_id' => $student_id, 'role_type' => $role_type]
    );
  }
  public function getAllParents()
  {
    return $this->where(['role' => 'parent', 'is_active' => 1]);
  }

  // Distinct non-empty diagnoses across active students (for the filter dropdown)
  public function getDistinctDiagnoses()
  {
    return $this->query(
      "SELECT DISTINCT diagnosis
         FROM students
        WHERE is_active = 1
          AND diagnosis IS NOT NULL
          AND diagnosis != ''
        ORDER BY diagnosis ASC"
    );
  }

  // Active students with an exact diagnosis, optionally only boarding ones
  public function getStudentsByDiagnosisFilter($diagnosis = '', $onlyBoarding = false)
  {
    $where = "WHERE is_active = 1";
    $params = [];
    if ($diagnosis !== '') {
      $where .= " AND diagnosis = :diagnosis";
      $params['diagnosis'] = $diagnosis;
    }
    if ($onlyBoarding) {
      $where .= " AND is_boarding = 1";
    }
    return $this->query(
      "SELECT * FROM students $where ORDER BY last_name ASC",
      $params
    );
  }

  // All active boarding students
  public function getBoardingStudentsList()
  {
    return $this->query(
      "SELECT * FROM students
        WHERE is_active = 1 AND is_boarding = 1
        ORDER BY last_name ASC"
    );
  }

  // Active students filtered by name search and/or exact diagnosis
  public function searchStudents($name = '', $diagnosis = '')
  {
    $where  = "WHERE is_active = 1";
    $params = [];
    if (trim($name) !== '') {
      $where .= " AND (first_name LIKE :name OR last_name LIKE :name2)";
      $params['name']  = '%' . $name . '%';
      $params['name2'] = '%' . $name . '%';
    }
    if (trim($diagnosis) !== '') {
      $where .= " AND diagnosis = :diagnosis";
      $params['diagnosis'] = $diagnosis;
    }
    return $this->query(
      "SELECT * FROM students $where ORDER BY last_name ASC",
      $params
    );
  }

  // Share a student's report with the parent (one row is enough)
  public function shareReport($studentId, $adminId)
  {
    $exists = $this->query(
      "SELECT id FROM shared_reports WHERE student_id = :sid LIMIT 1",
      ['sid' => $studentId]
    );
    if ($exists) {
      return true;
    }
    return $this->query(
      "INSERT INTO shared_reports (student_id, shared_by) VALUES (:sid, :aid)",
      ['sid' => $studentId, 'aid' => $adminId]
    );
  }

  // Boarding stats for a student's report: averages and breakdowns
  public function getBoardingStatsForStudent($studentId)
  {
    $stats = [
      'sleep_count'      => 0,
      'avg_sleep_hours'  => null,
      'avg_bedtime'      => null,
      'avg_wakeup'       => null,
      'sleep_quality'    => [],
      'mood'             => [],
      'appetite'         => [],
      'total_logs'       => 0,
    ];

    // Sleep: average duration (handles overnight), average bedtime & wakeup
    $sleepRows = $this->query(
      "SELECT bedtime, wakeup_time
         FROM boarding_logs
        WHERE student_id = :sid AND log_type = 'sleep'
          AND bedtime IS NOT NULL AND wakeup_time IS NOT NULL",
      ['sid' => $studentId]
    ) ?: [];

    if (!empty($sleepRows)) {
      $totalMins = 0;
      $bedMins   = 0;
      $wakeMins  = 0;
      $n = 0;
      foreach ($sleepRows as $r) {
        $bed  = strtotime($r->bedtime);
        $wake = strtotime($r->wakeup_time);
        if ($bed === false || $wake === false) continue;

        $bedM  = (int)date('G', $bed) * 60 + (int)date('i', $bed);
        $wakeM = (int)date('G', $wake) * 60 + (int)date('i', $wake);

        // If wakeup is "earlier" than bedtime, it's the next morning
        $duration = $wakeM - $bedM;
        if ($duration <= 0) {
          $duration += 24 * 60;
        }

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

    // Sleep quality breakdown
    $stats['sleep_quality'] = $this->countBy($studentId, 'sleep', 'sleep_quality');
    // Mood breakdown (behavior logs)
    $stats['mood']          = $this->countBy($studentId, 'behavior', 'mood_indicator');
    // Appetite breakdown (meal logs)
    $stats['appetite']      = $this->countBy($studentId, 'meal', 'appetite_level');

    $totalRows = $this->query(
      "SELECT COUNT(*) AS c FROM boarding_logs WHERE student_id = :sid",
      ['sid' => $studentId]
    );
    $stats['total_logs'] = $totalRows ? (int)$totalRows[0]->c : 0;

    return $stats;
  }

  // Helper: count rows grouped by an enum column, returned as [value => count]
  private function countBy($studentId, $logType, $column)
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

  public function getRoles()
  {
    return ['admin', 'teacher', 'therapist', 'nurse', 'parent', 'boarding_staff', 'security_guard'];
  }

  public function getUserById($id)
  {
    return $this->first(['id' => $id]);
  }

  public function getRecentUsers($limit = 5)
  {
    return $this->query(
      "SELECT * FROM users ORDER BY id DESC LIMIT :limit",
      ['limit' => $limit]
    );
  }

  public function getRecentStudents($limit = 5)
  {
    return $this->query(
      "SELECT * FROM students ORDER BY id DESC LIMIT :limit",
      ['limit' => $limit]
    );
  }

  // Get assigned students for any staff member (nurse, teacher, therapist)
  public function getAssignedStudentsForUser($user_id, $role)
  {
    if ($role === 'nurse') {
      return $this->query(
        "SELECT s.id, s.first_name, s.last_name, s.diagnosis
          FROM students s
          JOIN nurse_student ns ON s.id = ns.student_id
          WHERE ns.nurse_id = :user_id AND s.is_active = 1
          ORDER BY s.last_name ASC",
        ['user_id' => $user_id]
      );
    }
    if (in_array($role, ['teacher', 'therapist'])) {
      return $this->query(
        "SELECT s.id, s.first_name, s.last_name, s.diagnosis
          FROM students s
          JOIN student_assignments sa ON s.id = sa.student_id
          WHERE sa.user_id = :user_id AND sa.role_type = :role AND sa.end_date IS NULL AND s.is_active = 1
          ORDER BY s.last_name ASC",
        ['user_id' => $user_id, 'role' => $role]
      );
    }
    if ($role === 'parent') {
      return $this->query(
        "SELECT s.id, s.first_name, s.last_name, s.diagnosis
          FROM students s
          WHERE s.guardian_id = :user_id AND s.is_active = 1
          ORDER BY s.last_name ASC",
        ['user_id' => $user_id]
      );
    }
    return [];
  }

  // Get IEP goals for a student
  public function getIepGoalsForStudent($student_id)
  {
    return $this->query(
      "SELECT ig.*, u.first_name as created_by_first, u.last_name as created_by_last
        FROM iep_goals ig
        LEFT JOIN users u ON ig.created_by = u.id
        WHERE ig.student_id = :student_id
        ORDER BY ig.created_at DESC",
      ['student_id' => $student_id]
    );
  }

  // Get goal progress for a student
  public function getGoalProgressForStudent($student_id)
  {
    return $this->query(
      "SELECT gp.*, ig.goal_text, ig.category, u.first_name as recorded_by_first, u.last_name as recorded_by_last
        FROM goal_progress gp
        JOIN iep_goals ig ON gp.goal_id = ig.id
        LEFT JOIN users u ON gp.recorded_by = u.id
        WHERE ig.student_id = :student_id
        ORDER BY gp.recorded_at DESC",
      ['student_id' => $student_id]
    );
  }

  // Get sessions a student attended
  public function getSessionsForStudent($student_id)
  {
    return $this->query(
      "SELECT s.*, u.first_name as created_by_first, u.last_name as created_by_last
        FROM sessions s
        JOIN session_students ss ON s.id = ss.session_id
        LEFT JOIN users u ON s.created_by = u.id
        WHERE ss.student_id = :student_id
        ORDER BY s.scheduled_at DESC",
      ['student_id' => $student_id]
    );
  }

  public function getTeacchProgressForStudent($student_id)
  {
    return $this->query(
      "SELECT tp.*, tt.title as task_title,
                u.first_name as recorded_by_first, u.last_name as recorded_by_last
         FROM teacch_progress tp
         JOIN teacch_tasks tt ON tp.task_id = tt.id
         LEFT JOIN users u ON tp.recorded_by = u.id
         WHERE tp.student_id = :student_id
         ORDER BY tp.session_date DESC, tp.created_at DESC",
      ['student_id' => $student_id]
    );
  }

  public function getAssignedStaffForStudent($student_id)
  {
    return $this->query(
      "SELECT u.id, u.first_name, u.last_name, u.role, u.email, sa.start_date, sa.role_type
        FROM users u
        JOIN student_assignments sa ON u.id = sa.user_id
        WHERE sa.student_id = :student_id AND sa.end_date IS NULL
        UNION
          SELECT u.id, u.first_name, u.last_name, u.role, u.email, NULL as start_date, 'nurse' as role_type FROM users u
        JOIN nurse_student ns ON u.id = ns.nurse_id
        WHERE ns.student_id = :student_id
        ORDER BY role_type ASC",
      ['student_id' => $student_id]
    );
  }


  public function getReportSummary()
  {
    $row = $this->query(
      "SELECT
          (SELECT COUNT(*) FROM students)                                                AS total_students,
          (SELECT COUNT(*) FROM students WHERE is_active = 1)                            AS active_students,
          (SELECT COUNT(*) FROM students WHERE is_active = 0)                            AS archived_students,
          (SELECT COUNT(*) FROM users WHERE is_active = 1)                               AS total_staff,
          (SELECT COUNT(*) FROM iep_goals)                                               AS total_goals,
          (SELECT COUNT(*) FROM iep_goals WHERE status = 'achieved')                     AS goals_met,
          (SELECT COUNT(*) FROM iep_goals WHERE status = 'active')                       AS goals_active,
          (SELECT COUNT(*) FROM iep_goals WHERE status = 'discontinued')                 AS goals_discontinued,
          (SELECT COUNT(*) FROM iep_milestones)                                          AS total_milestones,
          (SELECT COUNT(*) FROM iep_milestones WHERE is_achieved = 1)                    AS milestones_achieved,
          (SELECT COUNT(*) FROM teacch_schedules)                                        AS total_schedules,
          (SELECT COUNT(*) FROM teacch_tasks)                                            AS total_teacch_tasks,
          (SELECT COUNT(*) FROM classroom_sessions)                                      AS total_classroom_sessions,
          (SELECT COUNT(*) FROM therapy_sessions)                                        AS total_therapy_sessions,
          (SELECT COUNT(*) FROM therapy_sessions WHERE status = 'completed')             AS therapy_completed,
          (SELECT COUNT(*) FROM therapy_sessions WHERE status = 'scheduled')             AS therapy_scheduled,
          (SELECT COUNT(*) FROM homework)                                                AS total_homework,
          (SELECT COUNT(*) FROM medications WHERE is_active = 1)                         AS active_medications,
          (SELECT COUNT(DISTINCT student_id) FROM medications WHERE is_active = 1)       AS students_on_meds,
          (SELECT COUNT(*) FROM health_events)                                           AS total_health_events,
          (SELECT COUNT(*) FROM health_events WHERE severity = 'high')                   AS high_severity_events,
          (SELECT COUNT(*) FROM checkin_logs WHERE DATE(check_time) = CURDATE() AND check_type = 'check_in')  AS checked_in_today,
          (SELECT COUNT(*) FROM checkin_logs WHERE DATE(check_time) = CURDATE() AND check_type = 'check_out') AS checked_out_today
      "
    );

    return $row ? $row[0] : null;
  }


  public function getStudentsByDiagnosis()
  {
    return $this->query(
      "SELECT COALESCE(NULLIF(TRIM(diagnosis), ''), 'Unspecified') AS diagnosis,
              COUNT(*) AS total
         FROM students
        WHERE is_active = 1
        GROUP BY COALESCE(NULLIF(TRIM(diagnosis), ''), 'Unspecified')
        ORDER BY total DESC, diagnosis ASC"
    );
  }


  public function getStudentsByGender()
  {
    return $this->query(
      "SELECT gender, COUNT(*) AS total
         FROM students
        WHERE is_active = 1
        GROUP BY gender"
    );
  }


  public function getGoalsByCategory()
  {
    return $this->query(
      "SELECT COALESCE(NULLIF(TRIM(category), ''), 'Uncategorized') AS category,
              COUNT(*) AS total,
              SUM(CASE WHEN status = 'achieved' THEN 1 ELSE 0 END) AS achieved
         FROM iep_goals
        GROUP BY COALESCE(NULLIF(TRIM(category), ''), 'Uncategorized')
        ORDER BY total DESC"
    );
  }


  public function getRecentProgressScores($limit = 10)
  {
    return $this->query(
      "SELECT gp.score, gp.recorded_at,
              ig.goal_text, ig.category,
              s.first_name AS student_first_name, s.last_name AS student_last_name
         FROM goal_progress gp
         JOIN iep_goals ig ON gp.goal_id = ig.id
         JOIN students   s ON ig.student_id = s.id
        ORDER BY gp.recorded_at DESC
        LIMIT :limit",
      ['limit' => (int)$limit]
    );
  }


  public function getTopStaffBySessions($limit = 5)
  {
    return $this->query(
      "SELECT u.id, u.first_name, u.last_name, u.role, COUNT(*) AS total
         FROM (
           SELECT teacher_id   AS user_id FROM classroom_sessions
           UNION ALL
           SELECT therapist_id AS user_id FROM therapy_sessions WHERE status = 'completed'
         ) t
         JOIN users u ON u.id = t.user_id
        GROUP BY u.id, u.first_name, u.last_name, u.role
        ORDER BY total DESC
        LIMIT :limit",
      ['limit' => (int)$limit]
    );
  }


  public function getMonthlySessions($monthsBack = 6)
  {
    return $this->query(
      "SELECT DATE_FORMAT(d, '%Y-%m') AS ym,
              SUM(kind = 'class')   AS class_count,
              SUM(kind = 'therapy') AS therapy_count
         FROM (
           SELECT session_date AS d, 'class'   AS kind FROM classroom_sessions
           UNION ALL
           SELECT session_date AS d, 'therapy' AS kind FROM therapy_sessions
         ) all_sessions
        WHERE d >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
        GROUP BY ym
        ORDER BY ym ASC",
      ['months' => (int)$monthsBack]
    );
  }


  public function getMilestonesForStudent($student_id)
  {
    return $this->query(
      "SELECT m.*, ig.goal_text, ig.category
         FROM iep_milestones m
         JOIN iep_goals ig ON ig.id = m.goal_id
        WHERE ig.student_id = :student_id
        ORDER BY ig.category ASC, m.created_at ASC",
      ['student_id' => $student_id]
    );
  }


  public function getClassroomSessionsForStudent($student_id, $limit = 30)
  {
    return $this->query(
      "SELECT cs.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM classroom_sessions cs
         LEFT JOIN users u ON cs.teacher_id = u.id
        WHERE cs.student_id = :student_id
        ORDER BY cs.session_date DESC, cs.created_at DESC
        LIMIT :the_limit",
      ['student_id' => $student_id, 'the_limit' => (int)$limit]
    );
  }


  public function getTherapySessionsForStudent($student_id, $limit = 30)
  {
    return $this->query(
      "SELECT ts.*, u.first_name AS therapist_first, u.last_name AS therapist_last,
              ig.goal_text AS goal_addressed
         FROM therapy_sessions ts
         LEFT JOIN users u ON ts.therapist_id = u.id
         LEFT JOIN iep_goals ig ON ts.goal_addressed_id = ig.id
        WHERE ts.student_id = :student_id
        ORDER BY ts.session_date DESC, ts.created_at DESC
        LIMIT :the_limit",
      ['student_id' => $student_id, 'the_limit' => (int)$limit]
    );
  }


  public function getObservationsForStudent($student_id, $limit = 20)
  {
    return $this->query(
      "SELECT ao.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM academic_observations ao
         LEFT JOIN users u ON ao.teacher_id = u.id
        WHERE ao.student_id = :student_id
        ORDER BY ao.created_at DESC
        LIMIT :the_limit",
      ['student_id' => $student_id, 'the_limit' => (int)$limit]
    );
  }


  public function getProgressReportsForStudent($student_id, $limit = 10)
  {
    return $this->query(
      "SELECT pr.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM progress_reports pr
         LEFT JOIN users u ON pr.teacher_id = u.id
        WHERE pr.student_id = :student_id
        ORDER BY pr.created_at DESC
        LIMIT :the_limit",
      ['student_id' => $student_id, 'the_limit' => (int)$limit]
    );
  }


  public function getHomeworkForStudent($student_id, $limit = 30)
  {
    return $this->query(
      "SELECT h.*, u.first_name AS teacher_first, u.last_name AS teacher_last
         FROM homework h
         LEFT JOIN users u ON h.assigned_by = u.id
        WHERE h.student_id = :student_id
        ORDER BY h.due_date DESC, h.created_at DESC
        LIMIT :the_limit",
      ['student_id' => $student_id, 'the_limit' => (int)$limit]
    );
  }
}
