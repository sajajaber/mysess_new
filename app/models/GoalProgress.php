<?php
class GoalProgress extends Model
{
  protected $table        = 'goal_progress';
  protected $order_column = 'recorded_at';
  protected $order_type   = 'desc';

  public function getForGoal($goalId)
  {
    return $this->query(
      "SELECT id, goal_id, score, notes, recorded_by, recorded_at
         FROM goal_progress
        WHERE goal_id = :goal_id
        ORDER BY recorded_at DESC",
      ['goal_id' => $goalId]
    );
  }

  public function getForGoalChrono($goalId)
  {
    return $this->query(
      "SELECT id, goal_id, score, notes, recorded_by, recorded_at
         FROM goal_progress
        WHERE goal_id = :goal_id
        ORDER BY recorded_at ASC",
      ['goal_id' => $goalId]
    );
  }
  public function addProgress($goalId, $score, $notes, $recordedBy)
  {
    return $this->query(
      "INSERT INTO goal_progress (goal_id, score, notes, recorded_by)
       VALUES (:goal_id, :score, :notes, :recorded_by)",
      [
        'goal_id'     => $goalId,
        'score'       => $score,
        'notes'       => $notes,
        'recorded_by' => $recordedBy,
      ]
    );
  }
  public function getLatestScore($goalId)
  {
    $result = $this->query(
      "SELECT score
         FROM goal_progress
        WHERE goal_id = :goal_id
        ORDER BY recorded_at DESC
        LIMIT 1",
      ['goal_id' => $goalId]
    );

    if ($result && isset($result[0]->score)) {
      return (int)$result[0]->score;
    }
    return null;
  }
  public function getBaselineScoreInRange($goalId, $startDate, $endDate)
  {
    $result = $this->query(
      "SELECT score
         FROM goal_progress
        WHERE goal_id = :goal_id
          AND recorded_at >= :start_date
          AND recorded_at <  :end_after
        ORDER BY recorded_at ASC
        LIMIT 1",
      [
        'goal_id'    => $goalId,
        'start_date' => $startDate . ' 00:00:00',
        'end_after'  => $endDate . ' 23:59:59',
      ]
    );

    if ($result && isset($result[0]->score)) {
      return (int)$result[0]->score;
    }
    return null;
  }
  public function getLatestScoreInRange($goalId, $startDate, $endDate)
  {
    $result = $this->query(
      "SELECT score
         FROM goal_progress
        WHERE goal_id = :goal_id
          AND recorded_at >= :start_date
          AND recorded_at <  :end_after
        ORDER BY recorded_at DESC
        LIMIT 1",
      [
        'goal_id'    => $goalId,
        'start_date' => $startDate . ' 00:00:00',
        'end_after'  => $endDate . ' 23:59:59',
      ]
    );

    if ($result && isset($result[0]->score)) {
      return (int)$result[0]->score;
    }
    return null;
  }
  public function countInRange($goalId, $startDate, $endDate)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM goal_progress
        WHERE goal_id = :goal_id
          AND recorded_at >= :start_date
          AND recorded_at <  :end_after",
      [
        'goal_id'    => $goalId,
        'start_date' => $startDate . ' 00:00:00',
        'end_after'  => $endDate . ' 23:59:59',
      ]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }
}
