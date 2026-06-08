<?php

class Broadcast extends Model
{
  protected $table = 'broadcasts';
  protected $order_column = 'created_at';
  protected $order_type = 'desc';

  // GET BROADCASTS VISIBLE TO USER
  public function getForUser($user_id, $role)
  {
    return $this->query(
      "SELECT b.*,
                    u.first_name AS sender_first,
                    u.last_name AS sender_last
             FROM broadcasts b
             JOIN users u ON b.sender_id = u.id
             WHERE b.sender_id = :user_id
                OR FIND_IN_SET(:role, b.target_roles) > 0
             ORDER BY b.created_at DESC",
      [
        'user_id' => $user_id,
        'role'    => $role
      ]
    );
  }

  
  // SINGLE BROADCAST
  public function getById($id)
  {
    $result = $this->query(
      "SELECT b.*,
                    u.first_name AS sender_first,
                    u.last_name AS sender_last
             FROM broadcasts b
             JOIN users u ON b.sender_id = u.id
             WHERE b.id = :id
             LIMIT 1",
      ['id' => $id]
    );

    return $result ? $result[0] : null;
  }

  
  // SEND BROADCAST
  public function send($sender_id, $target_roles, $subject, $body)
  {
    $rolesStr = implode(',', $target_roles);

    return $this->insert([
      'sender_id'    => $sender_id,
      'target_roles' => $rolesStr,
      'subject'      => $subject,
      'body'         => $body,
    ]);
  }
}
