<?php
class BoardingLog extends Model
{
  protected $table        = 'boarding_logs';
  protected $order_column = 'log_date';
  protected $order_type   = 'desc';


  public function getForStudent($studentId)
  {
    return $this->query(
      "SELECT * FROM boarding_logs
        WHERE student_id = :student_id
        ORDER BY log_date DESC, created_at DESC",
      ['student_id' => $studentId]
    );
  }

  public function getRecent($limit = 10)
  {
    return $this->query(
      "SELECT b.*, s.first_name AS student_first_name, s.last_name AS student_last_name
         FROM boarding_logs b
         JOIN students s ON s.id = b.student_id
        ORDER BY b.log_date DESC, b.created_at DESC
        LIMIT :limit",
      ['limit' => (int)$limit]
    );
  }


  public function countToday()
  {
    $rows = $this->query("SELECT COUNT(*) AS count FROM boarding_logs WHERE log_date = CURDATE()");
    return $rows ? (int)$rows[0]->count : 0;
  }
}
