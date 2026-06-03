<?php
class TeacchProgress extends Model
{
  protected $table        = 'teacch_progress';
  protected $order_column = 'session_date';
  protected $order_type   = 'desc';

  // Full rating history for one task, newest first
  public function getForTask($taskId)
  {
    return $this->query(
      "SELECT id, task_id, student_id, session_date, independence_level, recorded_by, notes, created_at
         FROM teacch_progress
        WHERE task_id = :task_id
        ORDER BY session_date DESC, created_at DESC",
      ['task_id' => $taskId]
    );
  }

  // The most recent rating for a task, or null if it has none yet
  public function getLatestForTask($taskId)
  {
    $result = $this->query(
      "SELECT independence_level, session_date
         FROM teacch_progress
        WHERE task_id = :task_id
        ORDER BY session_date DESC, created_at DESC
        LIMIT 1",
      ['task_id' => $taskId]
    );

    if (is_array($result) && count($result)) {
      return $result[0];
    }
    return null;
  }

  // The most recent rating for a task inside a date range (for the semester report)
  public function getLatestForTaskInRange($taskId, $startDate, $endDate)
  {
    $result = $this->query(
      "SELECT independence_level, session_date
         FROM teacch_progress
        WHERE task_id = :task_id
          AND session_date >= :start_date
          AND session_date <= :end_date
        ORDER BY session_date DESC, created_at DESC
        LIMIT 1",
      [
        'task_id'    => $taskId,
        'start_date' => $startDate,
        'end_date'   => $endDate,
      ]
    );

    if (is_array($result) && count($result)) {
      return $result[0];
    }
    return null;
  }

  // How many ratings a task got inside a date range
  public function countForTaskInRange($taskId, $startDate, $endDate)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM teacch_progress
        WHERE task_id = :task_id
          AND session_date >= :start_date
          AND session_date <= :end_date",
      [
        'task_id'    => $taskId,
        'start_date' => $startDate,
        'end_date'   => $endDate,
      ]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }

  // Save one independence rating (the database fills created_at automatically)
  public function addRating($taskId, $studentId, $sessionDate, $independenceLevel, $notes, $recordedBy)
  {
    return $this->query(
      "INSERT INTO teacch_progress
          (task_id, student_id, session_date, independence_level, notes, recorded_by)
       VALUES
          (:task_id, :student_id, :session_date, :independence_level, :notes, :recorded_by)",
      [
        'task_id'            => $taskId,
        'student_id'         => $studentId,
        'session_date'       => $sessionDate,
        'independence_level' => $independenceLevel,
        'notes'              => $notes,
        'recorded_by'        => $recordedBy,
      ]
    );
  }
}
