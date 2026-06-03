<?php
class Therapist extends User
{
  protected $table = 'users';
  public function getMyStudents($therapistId = null)
  {
    if (!$therapistId) {
      return [];
    }

    return $this->query(
      "SELECT s.*
         FROM students s
         JOIN student_assignments sa ON s.id = sa.student_id
        WHERE sa.user_id   = :therapist_id
          AND sa.role_type = 'therapist'
          AND sa.end_date  IS NULL
          AND s.is_active  = 1
        ORDER BY s.last_name ASC",
      ['therapist_id' => $therapistId]
    );
  }
  public function countMyStudents($therapistId)
  {
    $students = $this->getMyStudents($therapistId);
    return is_array($students) ? count($students) : 0;
  }
  public function isThisStudentMine($studentId, $therapistId)
  {
    if (!$therapistId) {
      return false;
    }

    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM student_assignments
        WHERE user_id   = :therapist_id
          AND student_id = :student_id
          AND role_type  = 'therapist'
          AND end_date   IS NULL",
      [
        'therapist_id' => $therapistId,
        'student_id'   => $studentId
      ]
    );

    return $result && isset($result[0]->count) && $result[0]->count > 0;
  }
}
