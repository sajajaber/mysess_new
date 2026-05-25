<?php
class HealthRecord extends Model
{
    protected $table        = 'health_records';
    protected $order_column = 'recorded_at';
    protected $order_type   = 'desc';

    public function getTypeOptions()
    {
        return ['medical_note', 'medication', 'allergy', 'emergency_contact'];
    }

    // Get allergy count for nurse's students
    public function getAllergyCountForNurse($nurse_id)
    {
        $result = $this->query(
            "SELECT COUNT(DISTINCT hr.id) as count
             FROM health_records hr
             JOIN students s ON hr.student_id = s.id
             JOIN nurse_student ns ON s.id = ns.student_id
             WHERE ns.nurse_id = :nurse_id 
             AND hr.record_type = 'allergy'",
            ['nurse_id' => $nurse_id]
        );

        if ($result && isset($result[0]->count)) {
            return $result[0]->count;
        }
        return 0;
    }

    // Get recent health records for nurse's students
    public function getRecentForNurse($nurse_id, $limit = 10)
    {
        return $this->query(
            "SELECT hr.*, 
                    s.first_name as student_first_name, 
                    s.last_name as student_last_name,
                    u.first_name as recorded_by_first_name,
                    u.last_name as recorded_by_last_name
             FROM health_records hr
             JOIN students s ON hr.student_id = s.id
             JOIN nurse_student ns ON s.id = ns.student_id
             LEFT JOIN users u ON hr.recorded_by = u.id
             WHERE ns.nurse_id = :nurse_id
             ORDER BY hr.recorded_at DESC
             LIMIT :limit",
            ['nurse_id' => $nurse_id, 'limit' => $limit]
        );
    }
}
