<?php
class AttendanceNote extends Model
{
  protected $table        = 'attendance_notes';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';


  public function getForDate($date)
  {
    return $this->query(
      "SELECT student_id, note FROM attendance_notes WHERE note_date = :the_date",
      ['the_date' => $date]
    );
  }


  public function getMapForDate($date)
  {
    $rows = $this->getForDate($date);
    $map  = [];
    foreach ($rows ?: [] as $r) {
      $map[$r->student_id] = $r->note;
    }
    return $map;
  }


  public function saveNote($studentId, $date, $note, $recordedBy)
  {
    return $this->query(
      "INSERT INTO attendance_notes (student_id, note_date, note, recorded_by)
       VALUES (:student_id, :note_date, :note, :recorded_by)
       ON DUPLICATE KEY UPDATE note = VALUES(note), recorded_by = VALUES(recorded_by)",
      [
        'student_id'  => $studentId,
        'note_date'   => $date,
        'note'        => $note,
        'recorded_by' => $recordedBy,
      ]
    );
  }
}
