<?php
class Admin extends User
{
  protected $table = 'users';
  public function getDashboardStats()
  {
    $result = $this->query(
      "SELECT
              (SELECT COUNT(*) FROM users WHERE is_active = 1) AS total_users,
              (SELECT COUNT(*) FROM users WHERE role = 'teacher' AND is_active = 1) AS total_teachers,
              (SELECT COUNT(*) FROM users WHERE role = 'therapist' AND is_active = 1) AS total_therapists,
              (SELECT COUNT(*) FROM users WHERE role = 'nurse' AND is_active = 1) AS total_nurses,
              (SELECT COUNT(*) FROM users WHERE role = 'parent' AND is_active = 1) AS total_parents,
              (SELECT COUNT(*) FROM users WHERE role = 'boarding_staff' AND is_active = 1) AS total_boarding_staff,
              (SELECT COUNT(*) FROM users WHERE role = 'security_guard' AND is_active = 1) AS total_security,
              (SELECT COUNT(*) FROM students WHERE is_active = 1) AS total_students,
              (SELECT COUNT(*) FROM students WHERE is_active = 0) AS archived_students,
              (SELECT COUNT(*) FROM medications WHERE is_active = 1) AS active_medications,
              (SELECT COUNT(DISTINCT student_id) FROM medications WHERE is_active = 1) AS students_on_meds"
    );

    return $result ? $result[0] : null;
  }

  public function getAllUsers($role = null)
  {
    if ($role) {
      return $this->where(['role' => $role, 'is_active' => 1]);
    }
    return $this->query(
      "SELECT * FROM users ORDER BY role ASC, last_name ASC"
    );
  }

  // Password must already be hashed before calling this
  public function createUser($data)
  {
    return $this->insert([
      'first_name' => $data['first_name'],
      'last_name'  => $data['last_name'],
      'email'      => $data['email'],
      'phone'      => $data['phone']    ?? null,
      'password'   => $data['password'],
      'role'       => $data['role'],
      'is_active'  => 1,
    ]);
  }

  public function updateUser($id, $data)
  {
    return $this->update($id, [
      'first_name' => $data['first_name'],
      'last_name'  => $data['last_name'],
      'email'      => $data['email'],
      'phone'      => $data['phone'] ?? null,
      'role'       => $data['role'],
    ]);
  }

  public function updateUserPassword($id, $plain_password)
  {
    return $this->update($id, [
      'password' => password_hash($plain_password, PASSWORD_DEFAULT),
    ]);
  }

  public function deactivateUser($id)
  {
    return $this->update($id, ['is_active' => 0]);
  }

  public function activateUser($id)
  {
    return $this->update($id, ['is_active' => 1]);
  }

  // exclude_id is necessary for checking all users emails EXCEPT the user being edited 
  public function emailExists($email, $exclude_id = null)
  {
    if ($exclude_id) {
      $result = $this->query(
        "SELECT COUNT(*) as count FROM users WHERE email = :email AND id != :id",
        ['email' => $email, 'id' => $exclude_id]
      );
    } else {
      $result = $this->query(
        "SELECT COUNT(*) as count FROM users WHERE email = :email",
        ['email' => $email]
      );
    }
    return $result && $result[0]->count > 0;
  }

  public function getAllStudents($active_only = true)
  {
    $where = $active_only ? "WHERE s.is_active = 1" : "";
    return $this->query(
      "SELECT s.*, u.first_name as guardian_first_name, u.last_name as guardian_last_name
      FROM students s
      LEFT JOIN users u ON s.guardian_id = u.id
      $where
      ORDER BY s.last_name ASC"
    );
  }

  public function getStudentById($id)
  {
    $result = $this->query(
      "SELECT s.*, u.first_name as guardian_first_name, u.last_name as guardian_last_name
      FROM students s
      LEFT JOIN users u ON s.guardian_id = u.id
      WHERE s.id = :id
      LIMIT 1",
      ['id' => $id]
    );
    return $result ? $result[0] : null;
  }

  public function createStudent($data)
  {
    return $this->query(
      "INSERT INTO students
          (first_name, last_name, date_of_birth, gender, diagnosis, enrollment_date, guardian_id, is_active)
        VALUES
          (:first_name, :last_name, :date_of_birth, :gender, :diagnosis, :enrollment_date, :guardian_id, 1)",
      [
        'first_name'      => $data['first_name'],
        'last_name'       => $data['last_name'],
        'date_of_birth'   => $data['date_of_birth'],
        'gender'          => $data['gender'],
        'diagnosis'       => $data['diagnosis']      ?? null,
        'enrollment_date' => $data['enrollment_date'],
        'guardian_id'     => $data['guardian_id']    ?? null,
      ]
    );
  }

  public function updateStudent($id, $data)
  {
    return $this->query(
      "UPDATE students SET
        first_name      = :first_name,
        last_name       = :last_name,
        date_of_birth   = :date_of_birth,
        gender          = :gender,
        diagnosis       = :diagnosis,
        enrollment_date = :enrollment_date,
        guardian_id     = :guardian_id
      WHERE id = :id",
      [
        'first_name'      => $data['first_name'],
        'last_name'       => $data['last_name'],
        'date_of_birth'   => $data['date_of_birth'],
        'gender'          => $data['gender'],
        'diagnosis'       => $data['diagnosis']      ?? null,
        'enrollment_date' => $data['enrollment_date'],
        'guardian_id'     => $data['guardian_id']    ?? null,
        'id'              => $id,
      ]
    );
  }

  public function archiveStudent($id)
  {
    return $this->query(
      "UPDATE students SET is_active = 0 WHERE id = :id",
      ['id' => $id]
    );
  }

  public function restoreStudent($id)
  {
    return $this->query(
      "UPDATE students SET is_active = 1 WHERE id = :id",
      ['id' => $id]
    );
  }

  //  get all students assigned to a specific staff member
  public function getStudentsForStaff($user_id, $role)
  {
    if ($role === 'nurse') {
      return $this->query(
        "SELECT s.* FROM students s
        JOIN nurse_student ns ON s.id = ns.student_id
        WHERE ns.nurse_id = :user_id AND s.is_active = 1
        ORDER BY s.last_name ASC",
        ['user_id' => $user_id]
      );
    }
    return $this->query(
      "SELECT s.* FROM students s
      JOIN student_assignments sa ON s.id = sa.student_id
      WHERE sa.user_id = :user_id AND sa.role_type = :role AND sa.end_date IS NULL AND s.is_active = 1
      ORDER BY s.last_name ASC",
      ['user_id' => $user_id, 'role' => $role]
    );
  }

  public function getStaffByRole($role)
  {
    return $this->where(['role' => $role, 'is_active' => 1]);
  }

  public function assignStudentToNurse($student_id, $nurse_id)
  {
    $exists = $this->query(
      "SELECT COUNT(*) as count FROM nurse_student WHERE nurse_id = :nurse_id AND student_id = :student_id",
      ['nurse_id' => $nurse_id, 'student_id' => $student_id]
    );
    if ($exists && $exists[0]->count > 0) return false;

    return $this->query(
      "INSERT INTO nurse_student (nurse_id, student_id) VALUES (:nurse_id, :student_id)",
      ['nurse_id' => $nurse_id, 'student_id' => $student_id]
    );
  }

  public function assignStudentToStaff($student_id, $user_id, $role_type)
  {
    $exists = $this->query(
      "SELECT COUNT(*) as count FROM student_assignments
        WHERE user_id = :user_id AND student_id = :student_id AND role_type = :role_type AND end_date IS NULL",
      ['user_id' => $user_id, 'student_id' => $student_id, 'role_type' => $role_type]
    );
    if ($exists && $exists[0]->count > 0) return false;

    return $this->query(
      "INSERT INTO student_assignments (student_id, user_id, role_type, start_date)
        VALUES (:student_id, :user_id, :role_type, CURDATE())",
      ['student_id' => $student_id, 'user_id' => $user_id, 'role_type' => $role_type]
    );
  }

  public function removeNurseAssignment($student_id, $nurse_id)
  {
    return $this->query(
      "DELETE FROM nurse_student WHERE nurse_id = :nurse_id AND student_id = :student_id",
      ['nurse_id' => $nurse_id, 'student_id' => $student_id]
    );
  }

  // Soft-delete: preserves history with end_date
  public function removeStaffAssignment($student_id, $user_id, $role_type)
  {
    return $this->query(
      "UPDATE student_assignments SET end_date = CURDATE()
        WHERE user_id = :user_id AND student_id = :student_id AND role_type = :role_type AND end_date IS NULL",
      ['user_id' => $user_id, 'student_id' => $student_id, 'role_type' => $role_type]
    );
  }
  public function getAllParents()
  {
    return $this->where(['role' => 'parent', 'is_active' => 1]);
  }

  public function getRoles()
  {
    return ['admin', 'teacher', 'therapist', 'nurse', 'parent', 'boarding_staff', 'security_guard'];
  }
}
