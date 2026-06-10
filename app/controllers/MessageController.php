<?php

class MessageController extends Controller
{
  private $messageModel;
  private $broadcastModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id'])) {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }

    $this->messageModel = new Message();
    $this->broadcastModel = new Broadcast();
  }

  
  // INBOX
  public function index()
  {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    $inbox = $this->messageModel->getInbox($user_id) ?: [];
    $sent = $this->messageModel->getSent($user_id) ?: [];
    $broadcasts = $this->broadcastModel->getForUser($user_id, $role) ?: [];
    $unread = $this->messageModel->getUnreadCount($user_id);

    $this->view('messages/inbox', [
      'inbox'      => $inbox,
      'sent'       => $sent,
      'broadcasts' => $broadcasts,
      'unread'     => $unread,
    ]);
  }

  
  // COMPOSE MESSAGE
  public function compose()
  {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $errors = [];

      $receiver_id = (int)($_POST['receiver_id'] ?? 0);
      $subject = trim($_POST['subject'] ?? '');
      $body = trim($_POST['body'] ?? '');

      if (!$receiver_id) {
        $errors[] = 'Please select a recipient.';
      }

      if (empty($subject)) {
        $errors[] = 'Subject is required.';
      }

      if (empty($body)) {
        $errors[] = 'Message body is required.';
      }

      if (!$errors) {

        $allowed = $this->messageModel->getAllowedRecipients($user_id, $role);
        $allowedIds = array_map(function ($u) {
          return $u->id;
        }, $allowed);

        if (!in_array($receiver_id, $allowedIds)) {
          $errors[] = 'You are not allowed to message this user.';
        }
      }

      if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;

        header('Location: ' . ROOT . '/messages/compose');
        exit();
      }

      $this->messageModel->send(
        $user_id,
        $receiver_id,
        $subject,
        $body
      );

      $_SESSION['success'] = 'Message sent successfully.';

      header('Location: ' . ROOT . '/messages');
      exit();
    }

    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);

    $recipients = $this->messageModel->getAllowedRecipients($user_id, $role);

    $preselect = (int)($_GET['to'] ?? 0);

    $this->view('messages/compose', [
      'recipients' => $recipients ?: [],
      'old'        => $old,
      'preselect'  => $preselect,
    ]);
  }

  
  // VIEW MESSAGE
  public function view_message($id = null)
  {
    if (!$id) {
      header('Location: ' . ROOT . '/messages');
      exit();
    }

    $user_id = $_SESSION['user_id'];

    $message = $this->messageModel->getById($id);

    if (!$message) {
      header('Location: ' . ROOT . '/messages');
      exit();
    }

    if (
      $message->sender_id != $user_id &&
      $message->receiver_id != $user_id
    ) {
      header('Location: ' . ROOT . '/messages');
      exit();
    }

    if (
      $message->receiver_id == $user_id &&
      !$message->is_read
    ) {
      $this->messageModel->markAsRead($id);
    }

    $this->view('messages/view_message', [
      'message' => $message
    ]);
  }

  
  // BROADCAST (ADMIN ONLY)
  public function broadcast()
  {
    if ($_SESSION['role'] !== 'admin') {
      header('Location: ' . ROOT . '/messages');
      exit();
    }

    $allRoles = [
      'nurse',
      'teacher',
      'therapist',
      'parent',
      'boarding_staff',
      'security_guard'
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $errors = [];

      $targetRoles = $_POST['target_roles'] ?? [];
      $subject = trim($_POST['subject'] ?? '');
      $body = trim($_POST['body'] ?? '');

      if (empty($targetRoles)) {
        $errors[] = 'Please select at least one role.';
      }

      if (empty($subject)) {
        $errors[] = 'Subject is required.';
      }

      if (empty($body)) {
        $errors[] = 'Message body is required.';
      }

      $targetRoles = array_filter(
        $targetRoles,
        function ($role) use ($allRoles) {
          return in_array($role, $allRoles);
        }
      );

      if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;

        header('Location: ' . ROOT . '/messages/broadcast');
        exit();
      }

      $this->broadcastModel->send(
        $_SESSION['user_id'],
        $targetRoles,
        $subject,
        $body
      );

      $_SESSION['success'] =
        'Broadcast sent to: ' . implode(', ', $targetRoles) . '.';

      header('Location: ' . ROOT . '/messages');
      exit();
    }

    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);

    $this->view('messages/broadcast', [
      'allRoles' => $allRoles,
      'old'      => $old,
    ]);
  }

  
  // VIEW BROADCAST
  public function broadcast_view($id = null)
  {
    if (!$id) {
      header('Location: ' . ROOT . '/messages');
      exit();
    }

    $broadcast = $this->broadcastModel->getById($id);

    if (!$broadcast) {
      header('Location: ' . ROOT . '/messages');
      exit();
    }

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    $isTarget = in_array(
      $role,
      explode(',', $broadcast->target_roles)
    );

    $isSender = ($broadcast->sender_id == $user_id);

    if (!$isTarget && !$isSender) {
      header('Location: ' . ROOT . '/messages');
      exit();
    }

    $this->view('messages/broadcast-view', [
      'broadcast' => $broadcast
    ]);
  }
}
