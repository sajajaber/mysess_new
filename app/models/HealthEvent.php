<?php
class HealthEvent extends Model
{
    protected $table        = 'health_events';
    protected $order_column = 'recorded_at';
    protected $order_type   = 'desc';

    public function getSeverityOptions()
    {
        return ['low', 'medium', 'high'];
    }

    // Get recent health events for nurse's students
    public function getRecentForNurse($nurse_id, $limit = 5)
    {
        return $this->query(
            "SELECT he.*, 
                    s.first_name as student_first_name, 
                    s.last_name as student_last_name
             FROM health_events he
             JOIN students s ON he.student_id = s.id
             JOIN nurse_student ns ON s.id = ns.student_id
             WHERE ns.nurse_id = :nurse_id
             ORDER BY he.recorded_at DESC
             LIMIT :limit",
            ['nurse_id' => $nurse_id, 'limit' => $limit]
        );
    }
}
