<?php
class TeacchTask extends Model
{
  protected $table        = 'teacch_tasks';
  protected $order_column = 'task_order';
  protected $order_type   = 'asc';

  // All tasks for one schedule, in step order (lowest task_order first)
  public function getForSchedule($scheduleId)
  {
    return $this->query(
      "SELECT id, schedule_id, task_order, title, visual_cue_url, created_at
         FROM teacch_tasks
        WHERE schedule_id = :schedule_id
        ORDER BY task_order ASC",
      ['schedule_id' => $scheduleId]
    );
  }

  // One task by id, including schedule_id (needed for the ownership chain)
  public function getById($taskId)
  {
    $result = $this->query(
      "SELECT id, schedule_id, task_order, title, visual_cue_url, created_at
         FROM teacch_tasks
        WHERE id = :task_id
        LIMIT 1",
      ['task_id' => $taskId]
    );

    if (is_array($result) && count($result)) {
      return $result[0];
    }
    return false;
  }

  // Add a task to a schedule (visual cue is optional)
  public function addTask($scheduleId, $taskOrder, $title, $visualCueUrl)
  {
    return $this->query(
      "INSERT INTO teacch_tasks (schedule_id, task_order, title, visual_cue_url)
       VALUES (:schedule_id, :task_order, :title, :visual_cue_url)",
      [
        'schedule_id'    => $scheduleId,
        'task_order'     => $taskOrder,
        'title'          => $title,
        'visual_cue_url' => $visualCueUrl,
      ]
    );
  }
}
