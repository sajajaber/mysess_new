<?php
class ProgressReport extends Model
{
  protected $table        = 'progress_reports';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';
  
  public function getReportsForStudent($studentId)
  {
    return $this->query(
      "SELECT *
         FROM progress_reports
        WHERE student_id = :student_id
        ORDER BY created_at DESC",
      ['student_id' => $studentId]
    );
  }
  public function getRecentReportsForTeacher($teacherId, $limit = 50)
  {
    return $this->query(
      "SELECT pr.*,
              s.first_name AS student_first_name,
              s.last_name  AS student_last_name
         FROM progress_reports pr
         JOIN students s ON pr.student_id = s.id
        WHERE pr.teacher_id = :teacher_id
        ORDER BY pr.created_at DESC
        LIMIT :limit",
      [
        'teacher_id' => $teacherId,
        'limit'      => (int)$limit
      ]
    );
  }
  public function countReportsForTeacher($teacherId)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count
         FROM progress_reports
        WHERE teacher_id = :teacher_id",
      ['teacher_id' => $teacherId]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }
}
