<?php
class AcademicObservation extends Model
{
  protected $table        = 'academic_observations';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';
  public function getObservationsForStudent($studentId)
  {
    return $this->query(
      "SELECT *
         FROM academic_observations
        WHERE student_id = :student_id
        ORDER BY created_at DESC",
      ['student_id' => $studentId]
    );
  }
  public function getObservationsForStudentInRange($studentId, $startDate, $endDate)
  {
    return $this->query(
      "SELECT *
         FROM academic_observations
        WHERE student_id = :student_id
          AND created_at >= :start_date
          AND created_at <  :end_after
        ORDER BY created_at DESC",
      [
        'student_id' => $studentId,
        'start_date' => $startDate . ' 00:00:00',
        'end_after'  => $endDate . ' 23:59:59',
      ]
    );
  }
}
