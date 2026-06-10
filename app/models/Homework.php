<?php
class Homework extends Model
{
  protected $table        = 'homework';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';
  public function getForTeacher($teacherId)
  {
    return $this->query(
      "SELECT h.id, h.student_id, h.title, h.description, h.due_date, h.is_submitted, h.created_at,
              s.first_name AS student_first_name, s.last_name AS student_last_name
         FROM homework h
         JOIN students s ON s.id = h.student_id
        WHERE h.assigned_by = :teacher_id
        ORDER BY h.created_at DESC",
      ['teacher_id' => $teacherId]
    );
  }
  public function getForStudent($studentId)
  {
    return $this->query(
      "SELECT id, student_id, title, description, due_date, is_submitted, created_at
         FROM homework
        WHERE student_id = :student_id
        ORDER BY due_date ASC",
      ['student_id' => $studentId]
    );
  }

  //Homework for all boarding students assigned to one boarding staff member
  public function getForBoardingStaff($staffId)
  {
    return $this->query(
      "SELECT h.id, h.student_id, h.title, h.description, h.due_date, h.is_submitted, h.created_at,
              s.first_name AS student_first_name, s.last_name AS student_last_name,
              t.first_name AS teacher_first_name, t.last_name AS teacher_last_name
         FROM homework h
         JOIN students s ON s.id = h.student_id
         JOIN student_assignments sa ON sa.student_id = h.student_id
         LEFT JOIN users t ON t.id = h.assigned_by
        WHERE sa.user_id = :staff_id
          AND sa.role_type = 'boarding_staff'
          AND sa.end_date IS NULL
          AND s.is_boarding = 1
        ORDER BY h.due_date ASC",
      ['staff_id' => $staffId]
    );
  }
  public function addHomework($studentId, $assignedBy, $title, $description, $dueDate)
  {
    return $this->query(
      "INSERT INTO homework (student_id, assigned_by, title, description, due_date)
       VALUES (:student_id, :assigned_by, :title, :description, :due_date)",
      [
        'student_id'  => $studentId,
        'assigned_by' => $assignedBy,
        'title'       => $title,
        'description' => $description,
        'due_date'    => $dueDate,
      ]
    );
  }
}
