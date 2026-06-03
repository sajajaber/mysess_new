<?php
class IepGoalProgress extends Model
{
  protected $table        = 'iep_goal_progress';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';

  // All progress updates for one student's goals, newest first.
  // Joins iep_goals so we can filter by student and show the goal text.
  public function getProgressForStudent($studentId)
  {
    return $this->query(
      "SELECT igp.*,
              ig.goal_text AS goal_description,
              ig.status AS goal_status
         FROM iep_goal_progress igp
         JOIN iep_goals ig ON igp.goal_id = ig.id
        WHERE ig.student_id = :student_id
        ORDER BY igp.created_at DESC",
      ['student_id' => $studentId]
    );
  }
}
