<?php

class Message extends Model
{
  protected $table = 'messages';
  protected $order_column = 'created_at';
  protected $order_type = 'desc';

  // INBOX & SENT
  public function getInbox($user_id)
  {
    return $this->query(
      "SELECT m.*,
                    u.first_name AS sender_first,
                    u.last_name AS sender_last,
                    u.role AS sender_role
             FROM messages m
             JOIN users u ON m.sender_id = u.id
             WHERE m.receiver_id = :user_id
             ORDER BY m.created_at DESC",
      ['user_id' => $user_id]
    );
  }

  public function getSent($user_id)
  {
    return $this->query(
      "SELECT m.*,
                    u.first_name AS receiver_first,
                    u.last_name AS receiver_last,
                    u.role AS receiver_role
             FROM messages m
             JOIN users u ON m.receiver_id = u.id
             WHERE m.sender_id = :user_id
             ORDER BY m.created_at DESC",
      ['user_id' => $user_id]
    );
  }

  // SINGLE MESSAGE
  public function getById($id)
  {
    $result = $this->query(
      "SELECT m.*,
                    s.first_name AS sender_first,
                    s.last_name AS sender_last,
                    s.role AS sender_role,
                    r.first_name AS receiver_first,
                    r.last_name AS receiver_last,
                    r.role AS receiver_role
             FROM messages m
             JOIN users s ON m.sender_id = s.id
             JOIN users r ON m.receiver_id = r.id
             WHERE m.id = :id
             LIMIT 1",
      ['id' => $id]
    );

    return $result ? $result[0] : null;
  }

  public function markAsRead($id)
  {
    return $this->update($id, [
      'is_read' => 1
    ]);
  }

  // SEND

  public function send($sender_id, $receiver_id, $subject, $body)
  {
    return $this->insert([
      'sender_id'  => $sender_id,
      'receiver_id' => $receiver_id,
      'subject'    => $subject,
      'body'       => $body,
    ]);
  }

  // UNREAD COUNT
  public function getUnreadCount($user_id)
  {
    $result = $this->query(
      "SELECT COUNT(*) AS count
             FROM messages
             WHERE receiver_id = :user_id
             AND is_read = 0",
      ['user_id' => $user_id]
    );

    return $result ? (int)$result[0]->count : 0;
  }

  // ALLOWED RECIPIENTS
  public function getAllowedRecipients($user_id, $role)
  {
    switch ($role) {

      case 'admin':
        return $this->query(
          "SELECT id, first_name, last_name, role
                     FROM users
                     WHERE id != :user_id
                     AND is_active = 1
                     ORDER BY role ASC, last_name ASC",
          ['user_id' => $user_id]
        );

      case 'nurse':
      case 'teacher':
      case 'therapist':

        return $this->query(
          "SELECT DISTINCT
                            u.id,
                            u.first_name,
                            u.last_name,
                            u.role
                     FROM users u
                     WHERE u.is_active = 1
                     AND u.id != :user_id_a
                     AND (
                            u.role IN ('admin','nurse','teacher','therapist')

                            OR

                            (
                                u.role = 'parent'
                                AND u.id IN (

                                    SELECT s.guardian_id
                                    FROM students s
                                    JOIN nurse_student ns
                                        ON s.id = ns.student_id
                                    WHERE ns.nurse_id = :user_id_b
                                    AND s.guardian_id IS NOT NULL

                                    UNION

                                    SELECT s.guardian_id
                                    FROM students s
                                    JOIN student_assignments sa
                                        ON s.id = sa.student_id
                                    WHERE sa.user_id = :user_id_c
                                    AND sa.end_date IS NULL
                                    AND s.guardian_id IS NOT NULL
                                )
                            )
                        )
                     ORDER BY u.role ASC, u.last_name ASC",
          [
            'user_id_a' => $user_id,
            'user_id_b' => $user_id,
            'user_id_c' => $user_id,
          ]
        );

      case 'parent':

        return $this->query(
          "SELECT DISTINCT
                            u.id,
                            u.first_name,
                            u.last_name,
                            u.role
                     FROM users u
                     WHERE u.is_active = 1
                     AND u.id != :user_id_a
                     AND (
                            u.role = 'admin'

                            OR

                            u.id IN (
                                SELECT ns.nurse_id
                                FROM nurse_student ns
                                JOIN students s
                                    ON ns.student_id = s.id
                                WHERE s.guardian_id = :user_id_b
                            )

                            OR

                            u.id IN (
                                SELECT sa.user_id
                                FROM student_assignments sa
                                JOIN students s
                                    ON sa.student_id = s.id
                                WHERE s.guardian_id = :user_id_c
                                AND sa.end_date IS NULL
                            )
                        )
                     ORDER BY u.role ASC, u.last_name ASC",
          [
            'user_id_a' => $user_id,
            'user_id_b' => $user_id,
            'user_id_c' => $user_id,
          ]
        );

      case 'security_guard':
      case 'boarding_staff':

        return $this->query(
          "SELECT id, first_name, last_name, role
                     FROM users
                     WHERE is_active = 1
                     AND id != :user_id
                     AND role IN ('admin','teacher','therapist','nurse')
                     ORDER BY role ASC, last_name ASC",
          ['user_id' => $user_id]
        );

      default:
        return [];
    }
  }
}
