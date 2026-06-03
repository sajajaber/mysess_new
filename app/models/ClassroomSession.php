<?php
class ClassroomSession extends Model
{
  protected $table        = 'classroom_sessions';
  protected $order_column = 'session_date';
  protected $order_type   = 'desc';

  public function getSessionsForStudent($studentId)
  {
    return $this->query(
      "SELECT *
         FROM classroom_sessions
        WHERE student_id = :student_id
        ORDER BY session_date DESC, created_at DESC",
      ['student_id' => $studentId]
    );
  }

  public function getRecentSessionsForTeacher($teacherId, $limit = 5)
  {
    return $this->query(
      "SELECT cs.*,
              s.first_name AS student_first_name,
              s.last_name  AS student_last_name
         FROM classroom_sessions cs
         JOIN students s ON cs.student_id = s.id
        WHERE cs.teacher_id = :teacher_id
        ORDER BY cs.session_date DESC, cs.created_at DESC
        LIMIT :limit",
      [
        'teacher_id' => $teacherId,
        'limit'      => (int)$limit
      ]
    );
  }

  public function countSessionsForTeacher($teacherId)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM classroom_sessions
        WHERE teacher_id = :teacher_id",
      ['teacher_id' => $teacherId]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }
  public function getSessionsForStudentInRange($studentId, $startDate, $endDate)
  {
    return $this->query(
      "SELECT *
         FROM classroom_sessions
        WHERE student_id = :student_id
          AND session_date >= :start_date
          AND session_date <= :end_date
        ORDER BY session_date DESC",
      [
        'student_id' => $studentId,
        'start_date' => $startDate,
        'end_date'   => $endDate,
      ]
    );
  }
}
