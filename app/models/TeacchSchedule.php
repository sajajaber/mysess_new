<?php
class TeacchSchedule extends Model
{
  protected $table        = 'teacch_schedules';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';
  public function getForStudent($studentId)
  {
    return $this->query(
      "SELECT id, student_id, title, created_by, is_active, created_at
         FROM teacch_schedules
        WHERE student_id = :student_id
        ORDER BY created_at DESC",
      ['student_id' => $studentId]
    );
  }
  public function getForStaff($userId, $roleType)
  {
    return $this->query(
      "SELECT t.id, t.student_id, t.title, t.is_active, t.created_at,
              s.first_name AS student_first_name, s.last_name AS student_last_name
         FROM teacch_schedules t
         JOIN students s ON s.id = t.student_id
         JOIN student_assignments sa ON sa.student_id = t.student_id
        WHERE sa.user_id   = :user_id
          AND sa.role_type = :role_type
          AND sa.end_date  IS NULL
          AND s.is_active  = 1
        ORDER BY t.created_at DESC",
      [
        'user_id'   => $userId,
        'role_type' => $roleType,
      ]
    );
  }
  public function getById($scheduleId)
  {
    $result = $this->query(
      "SELECT id, student_id, title, created_by, is_active, created_at
         FROM teacch_schedules
        WHERE id = :schedule_id
        LIMIT 1",
      ['schedule_id' => $scheduleId]
    );

    if (is_array($result) && count($result)) {
      return $result[0];
    }
    return false;
  }
  public function addSchedule($studentId, $title, $createdBy)
  {
    return $this->query(
      "INSERT INTO teacch_schedules (student_id, title, created_by, is_active)
       VALUES (:student_id, :title, :created_by, 1)",
      [
        'student_id' => $studentId,
        'title'      => $title,
        'created_by' => $createdBy,
      ]
    );
  }
}
