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

  public function getToday()
  {
    return $this->query(
      "SELECT c.*, s.first_name AS student_first_name, s.last_name AS student_last_name
         FROM checkin_logs c
         JOIN students s ON s.id = c.student_id
        WHERE DATE(c.check_time) = CURDATE()
        ORDER BY c.check_time DESC"
    );
  }

  public function getTodayMapForStudents()
  {
    $rows = $this->query(
      "SELECT student_id, check_type, check_time, notes
         FROM checkin_logs
        WHERE DATE(check_time) = CURDATE()"
    );

    $map = [];
    foreach ($rows ?: [] as $r) {
      if (!isset($map[$r->student_id])) {
        $map[$r->student_id] = ['check_in' => null, 'check_out' => null];
      }
      $map[$r->student_id][$r->check_type] = $r->check_time;
    }

    return $map;
  }

  public function hasTodayType($studentId, $checkType)
  {
    $rows = $this->query(
      "SELECT id FROM checkin_logs
        WHERE student_id = :student_id
          AND check_type = :check_type
          AND DATE(check_time) = CURDATE()
        LIMIT 1",
      ['student_id' => $studentId, 'check_type' => $checkType]
    );

    return !empty($rows);
  }


  public function updateTodayTypeTime($studentId, $checkType, $whenString)
  {
    return $this->query(
      "UPDATE checkin_logs
          SET check_time = :when_str
        WHERE student_id = :student_id
          AND check_type = :check_type
          AND DATE(check_time) = CURDATE()",
      [
        'student_id' => $studentId,
        'check_type' => $checkType,
        'when_str'   => $whenString,
      ]
    );
  }

  public function getDailyPivot($date)
  {
    return $this->query(
      "SELECT
          s.id AS student_id,
          s.first_name,
          s.last_name,
          s.diagnosis,
          MAX(CASE WHEN c.check_type = 'check_in'  THEN c.check_time END) AS check_in_time,
          MAX(CASE WHEN c.check_type = 'check_in'  THEN c.notes      END) AS check_in_notes,
          MAX(CASE WHEN c.check_type = 'check_out' THEN c.check_time END) AS check_out_time,
          MAX(CASE WHEN c.check_type = 'check_out' THEN c.notes      END) AS check_out_notes
         FROM students s
         JOIN checkin_logs c ON c.student_id = s.id
        WHERE DATE(c.check_time) = :the_date
        GROUP BY s.id, s.first_name, s.last_name, s.diagnosis
        ORDER BY s.last_name ASC, s.first_name ASC",
      ['the_date' => $date]
    );
  }
}
