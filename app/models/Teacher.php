<?php
class Teacher extends User
{
  protected $table = 'users';
  public function getMyStudents($teacherId = null)
  {
    if (!$teacherId) {
      return [];
    }

    return $this->query(
      "SELECT s.*
         FROM students s
         JOIN student_assignments sa ON s.id = sa.student_id
        WHERE sa.user_id   = :teacher_id
          AND sa.role_type = 'teacher'
          AND sa.end_date  IS NULL
          AND s.is_active  = 1
        ORDER BY s.last_name ASC",
      ['teacher_id' => $teacherId]
    );
  }
  public function countMyStudents($teacherId)
  {
    $students = $this->getMyStudents($teacherId);
    return is_array($students) ? count($students) : 0;
  }
  public function isThisStudentMine($studentId, $teacherId)
  {
    if (!$teacherId) {
      return false;
    }

    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM student_assignments
        WHERE user_id   = :teacher_id
          AND student_id = :student_id
          AND role_type  = 'teacher'
          AND end_date   IS NULL",
      [
        'teacher_id' => $teacherId,
        'student_id' => $studentId
      ]
    );

    return $result && isset($result[0]->count) && $result[0]->count > 0;
  }
}
