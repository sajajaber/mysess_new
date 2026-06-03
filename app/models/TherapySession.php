<?php
class TherapySession extends Model
{
  protected $table        = 'therapy_sessions';
  protected $order_column = 'session_date';
  protected $order_type   = 'desc';

  // All sessions for one student, newest first.
  // LEFT JOIN iep_goals because goal_addressed_id may be empty
  public function getSessionsForStudent($studentId)
  {
    return $this->query(
      "SELECT ts.*, ig.goal_text AS goal_addressed
         FROM therapy_sessions ts
         LEFT JOIN iep_goals ig ON ts.goal_addressed_id = ig.id
        WHERE ts.student_id = :student_id
        ORDER BY ts.session_date DESC, ts.created_at DESC",
      ['student_id' => $studentId]
    );
  }
  public function getRecentSessionsForTherapist($therapistId, $limit = 5)
  {
    return $this->query(
      "SELECT ts.*,
              s.first_name AS student_first_name,
              s.last_name  AS student_last_name,
              ig.goal_text AS goal_addressed
         FROM therapy_sessions ts
         JOIN students s ON ts.student_id = s.id
         LEFT JOIN iep_goals ig ON ts.goal_addressed_id = ig.id
        WHERE ts.therapist_id = :therapist_id
        ORDER BY ts.session_date DESC, ts.created_at DESC
        LIMIT :limit",
      [
        'therapist_id' => $therapistId,
        'limit'        => (int)$limit
      ]
    );
  }
  public function countSessionsForTherapist($therapistId)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM therapy_sessions
        WHERE therapist_id = :therapist_id",
      ['therapist_id' => $therapistId]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }
  public function countScheduledForTherapist($therapistId)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM therapy_sessions
        WHERE therapist_id = :therapist_id
          AND status = 'scheduled'",
      ['therapist_id' => $therapistId]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }

  // Sessions for one student inside a date range, newest first (for the semester report)
  public function getSessionsForStudentInRange($studentId, $startDate, $endDate)
  {
    return $this->query(
      "SELECT ts.*, ig.goal_text AS goal_addressed
         FROM therapy_sessions ts
         LEFT JOIN iep_goals ig ON ts.goal_addressed_id = ig.id
        WHERE ts.student_id = :student_id
          AND ts.session_date >= :start_date
          AND ts.session_date <= :end_date
        ORDER BY ts.session_date DESC, ts.created_at DESC",
      [
        'student_id' => $studentId,
        'start_date' => $startDate,
        'end_date'   => $endDate,
      ]
    );
  }
}
