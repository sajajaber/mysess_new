<?php
class Student extends Model
{
    protected $table        = 'students';
    protected $order_column = 'last_name';
    protected $order_type   = 'asc';

    // Get only students assigned to this nurse via nurse_student bridge table
    public function getAssignedStudents($nurse_id)
    {
        return $this->query(
            "SELECT s.*
             FROM students s
             JOIN nurse_student ns ON s.id = ns.student_id
             WHERE ns.nurse_id = :nurse_id
               AND s.is_active = 1
             ORDER BY s.last_name ASC",
            ['nurse_id' => $nurse_id]
        );
    }
}
