<?php
class Medication extends Model
{
    protected $table        = 'medications';
    protected $order_column = 'created_at';
    protected $order_type   = 'desc';

    // Get all medications for students assigned to a nurse
    public function getMedicationsForNurse($nurse_id)
    {
        return $this->query(
            "SELECT m.*, s.first_name, s.last_name, s.id as student_id
             FROM medications m
             JOIN students s ON m.student_id = s.id
             JOIN nurse_student ns ON s.id = ns.student_id
             WHERE ns.nurse_id = :nurse_id
             ORDER BY s.last_name ASC, m.created_at DESC",
            ['nurse_id' => $nurse_id]
        );
    }

    // Count of active medication records for nurse's students
    public function getActiveMedicationsCountForNurse($nurse_id)
    {
        $result = $this->query(
            "SELECT COUNT(DISTINCT m.id) as count
             FROM medications m
             JOIN students s ON m.student_id = s.id
             JOIN nurse_student ns ON s.id = ns.student_id
             WHERE ns.nurse_id = :nurse_id AND m.is_active = 1",
            ['nurse_id' => $nurse_id]
        );

        return ($result && isset($result[0]->count)) ? (int)$result[0]->count : 0;
    }

    // Count of distinct students who have at least one active medication
    public function getStudentsWithActiveMedicationsCount($nurse_id)
    {
        $result = $this->query(
            "SELECT COUNT(DISTINCT s.id) as count
             FROM students s
             JOIN nurse_student ns ON s.id = ns.student_id
             JOIN medications m ON s.id = m.student_id
             WHERE ns.nurse_id = :nurse_id AND m.is_active = 1",
            ['nurse_id' => $nurse_id]
        );

        return ($result && isset($result[0]->count)) ? (int)$result[0]->count : 0;
    }
}