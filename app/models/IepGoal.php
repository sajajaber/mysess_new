<?php
class IepGoal extends Model
{
  protected $table        = 'iep_goals';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';

  public function getGoalsForStudent($studentId)
  {
    return $this->query(
      "SELECT id, student_id, goal_text AS goal_description, status, created_at
         FROM iep_goals
        WHERE student_id = :student_id
        ORDER BY created_at DESC",
      ['student_id' => $studentId]
    );
  }
  public function addGoal($studentId, $goalText, $targetDate, $category, $status, $createdBy)
  {
    return $this->query(
      "INSERT INTO iep_goals (student_id, goal_text, target_date, category, status, created_by)
       VALUES (:student_id, :goal_text, :target_date, :category, :status, :created_by)",
      [
        'student_id'  => $studentId,
        'goal_text'   => $goalText,
        'target_date' => $targetDate,
        'category'    => $category,
        'status'      => $status,
        'created_by'  => $createdBy,
      ]
    );
  }
  public function getGoalById($goalId)
  {
    $result = $this->query(
      "SELECT id, student_id, goal_text AS goal_description, target_date, status, category, created_by, created_at
         FROM iep_goals
        WHERE id = :goal_id
        LIMIT 1",
      ['goal_id' => $goalId]
    );

    if (is_array($result) && count($result)) {
      return $result[0];
    }
    return false;
  }
  public function getGoalsForReport($studentId)
  {
    return $this->query(
      "SELECT id, student_id, goal_text AS goal_description, category, status, target_date, created_at
         FROM iep_goals
        WHERE student_id = :student_id
        ORDER BY category ASC, created_at ASC",
      ['student_id' => $studentId]
    );
  }
  public function getGoalsForStaff($userId, $roleType)
  {
    return $this->query(
      "SELECT g.id, g.student_id, g.goal_text AS goal_description, g.category, g.status, g.target_date, g.created_at,
              s.first_name AS student_first_name, s.last_name AS student_last_name
         FROM iep_goals g
         JOIN students s ON s.id = g.student_id
         JOIN student_assignments sa ON sa.student_id = g.student_id
        WHERE sa.user_id   = :user_id
          AND sa.role_type = :role_type
          AND sa.end_date  IS NULL
          AND s.is_active  = 1
        ORDER BY g.created_at DESC",
      [
        'user_id'   => $userId,
        'role_type' => $roleType,
      ]
    );
  }
}
