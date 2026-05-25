<?php
class MedicationLog extends Model
{
    protected $table        = 'medication_logs';  // plural
    protected $order_column = 'administered_at';
    protected $order_type   = 'desc';

    // JOIN with medications to get medication name + student_id for redirect
    public function getLogsForStudent($student_id)
    {
        return $this->query(
            "SELECT ml.*, m.name AS medication_name, m.dosage, m.student_id
             FROM medication_logs ml
             JOIN medications m ON ml.medication_id = m.id
             WHERE m.student_id = :student_id
             ORDER BY ml.administered_at DESC",
            ['student_id' => $student_id]
        );
    }
}
