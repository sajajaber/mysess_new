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
