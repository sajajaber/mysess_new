<?php
class Nurse extends User
{
  protected $table = 'users';

  public function getTotalMedicationsByNurseId($nurse_id = null)
  {
    if (!$nurse_id) {
      return 0;
    }

    $result = $this->query(
      "SELECT COUNT(*) as count
             FROM medications m
             JOIN students s ON m.student_id = s.id
             JOIN nurse_student ns ON s.id = ns.student_id
             WHERE ns.nurse_id = :nurse_id
               AND m.is_active = 1",
      ['nurse_id' => $nurse_id]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }
  // Override: Get students assigned to this nurse
  public function getAssignedStudents($nurse_id = null)
  {
    if (!$nurse_id) {
      return [];
    }

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

  // Get assigned students count
  public function getStudentCountByNurseId($nurse_id = null)
  {
    $students = $this->getAssignedStudents($nurse_id);
    return is_array($students) ? count($students) : 0;
  }

  // Get students with allergies count
  public function getStudentWithAllergiesCountByNurseId($nurse_id = null)
  {
    if (!$nurse_id) {
      return 0;
    }

    $result = $this->query(
      "SELECT COUNT(DISTINCT s.id) as count
             FROM students s
             JOIN nurse_student ns ON s.id = ns.student_id
             JOIN health_records hr ON s.id = hr.student_id
             WHERE ns.nurse_id = :nurse_id
               AND hr.record_type = 'allergy'",
      ['nurse_id' => $nurse_id]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }

  // Get students with active medications count
  public function getStudentWithActiveMedicationsCountByNurseId($nurse_id = null)
  {
    if (!$nurse_id) {
      return 0;
    }

    $result = $this->query(
      "SELECT COUNT(DISTINCT s.id) as count
             FROM students s
             JOIN nurse_student ns ON s.id = ns.student_id
             JOIN medications m ON s.id = m.student_id
             WHERE ns.nurse_id = :nurse_id
               AND m.is_active = 1",
      ['nurse_id' => $nurse_id]
    );

    if ($result && isset($result[0]->count)) {
      return (int)$result[0]->count;
    }
    return 0;
  }

  // Check if a student is assigned to this nurse
  public function isStudentAssigned($student_id, $nurse_id = null)
  {
    if (!$nurse_id) {
      return false;
    }

    $result = $this->query(
      "SELECT COUNT(*) as count
             FROM nurse_student
             WHERE nurse_id = :nurse_id
               AND student_id = :student_id",
      [
        'nurse_id' => $nurse_id,
        'student_id' => $student_id
      ]
    );

    return $result && isset($result[0]->count) && $result[0]->count > 0;
  }

  public function getRecentHealthRecordsByNurseId($nurse_id = null, $limit = 5)
  {
    if (!$nurse_id) {
      return [];
    }

    return $this->query(
      "SELECT hr.*, s.first_name as student_first_name, s.last_name as student_last_name, u.first_name as recorded_by_first_name, u.last_name as recorded_by_last_name
             FROM health_records hr
             JOIN students s ON hr.student_id = s.id
             JOIN users u ON hr.recorded_by = u.id
             JOIN nurse_student ns ON s.id = ns.student_id
             WHERE ns.nurse_id = :nurse_id
             ORDER BY hr.recorded_at DESC
             LIMIT :limit",
      [
        'nurse_id' => $nurse_id,
        'limit' => (int)$limit
      ]
    );
  }
}
