<?php
class CheckinLog extends Model
{
  protected $table        = 'checkin_logs';
  protected $order_column = 'check_time';
  protected $order_type   = 'desc';


  public function getForStudent($studentId)
  {
    return $this->query(
      "SELECT * FROM checkin_logs
        WHERE student_id = :student_id
        ORDER BY check_time DESC",
      ['student_id' => $studentId]
    );
  }

  public function getRecent($limit = 10)
  {
    return $this->query(
      "SELECT c.*, s.first_name AS student_first_name, s.last_name AS student_last_name
         FROM checkin_logs c
         JOIN students s ON s.id = c.student_id
        ORDER BY c.check_time DESC
        LIMIT :limit",
      ['limit' => (int)$limit]
    );
  }
}
