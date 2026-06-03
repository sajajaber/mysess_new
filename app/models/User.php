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
  public function getFullName($user)
  {
    return $user['first_name'] . ' ' . $user['last_name'];
  }
  public function hasRole($user, $role)
  {
    return $user['role'] === $role;
  }

  public function getUserByEmail($email)
  {
    return $this->first([
      'email' => $email
    ]);}
  public function getUsersByRole($role)
  {
    return $this->where([
      'role' => $role,
      'is_active' => 1
    ]);
  }

  public function updateLastLogin($userId)
  {
    return $this->update($userId, [
      'last_login' => date('Y-m-d H:i:s')
    ]);
  }

  public function getAssignedStudents()
  {
    return [];
  }

  public function getById($id)
  {
    return $this->first(['id' => $id]);
  }

  // Update the basic profile details for one user
  public function updateProfile($id, $firstName, $lastName, $email, $phone)
  {
    return $this->query(
      "UPDATE users
          SET first_name = :first_name,
              last_name  = :last_name,
              email      = :email,
              phone      = :phone
        WHERE id = :id",
      [
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'email'      => $email,
        'phone'      => $phone,
        'id'         => $id,
      ]
    );
  }

  // Save the uploaded profile picture's file name for one user
  public function updatePhoto($id, $photoFileName)
  {
    return $this->query(
      "UPDATE users SET photo_url = :photo WHERE id = :id",
      ['photo' => $photoFileName, 'id' => $id]
    );
  }

  // True if this email is already used by a different user
  public function emailTakenByOther($email, $exceptId)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count FROM users WHERE email = :email AND id != :id",
      ['email' => $email, 'id' => $exceptId]
    );
    return $result && isset($result[0]->count) && $result[0]->count > 0;
  }
}
