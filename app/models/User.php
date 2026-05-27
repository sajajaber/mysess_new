<?php

class User extends Model
{
  protected $table = 'users';
  protected $allowedColumns = [
    'email',
    'phone',
    'password',
    'first_name',
    'last_name',
    'role',
    'is_active',
    'last_login'
  ];

  // Get full name from user array
  public function getFullName($user)
  {
    return $user['first_name'] . ' ' . $user['last_name'];
  }

  // Check if user has a specific role
  public function hasRole($user, $role)
  {
    return $user['role'] === $role;
  }

  // Get one user by email
  public function getUserByEmail($email)
  {
    return $this->first([
      'email' => $email
    ]);
  }

  // Get all users with a specific role
  public function getUsersByRole($role)
  {
    return $this->where([
      'role' => $role,
      'is_active' => 1
    ]);
  }

  // Update user's last login
  public function updateLastLogin($userId)
  {
    return $this->update($userId, [
      'last_login' => date('Y-m-d H:i:s')
    ]);
  }

  // Base method for assigned students
  // Child classes can override this
  public function getAssignedStudents()
  {
    return [];
  }

  public function getById($id)
  {
    return $this->first(['id' => $id]);
  }

  
}
