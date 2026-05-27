<?php

class Auth extends Controller
{
  private $userModel;

  public function __construct()
  {
    $this->userModel = new User();
  }

  public function login()
  {
    if (isset($_SESSION['user_id'])) {
      header('Location: ' . ROOT . '/nurse/dashboard');
      exit();
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = trim($_POST['email'] ?? '');
      $password = $_POST['password'] ?? '';

      if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
      } else {
        $user = $this->userModel->getUserByEmail($email);

        if ($user && $user->is_active) {
          $validPassword = false;

          if (password_verify($password, $user->password)) {
            $validPassword = true;
          } elseif ($password === $user->password) {
            $validPassword = true;
          }

          if ($validPassword) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role'] = $user->role;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_name'] = trim($user->first_name . ' ' . $user->last_name);

            $this->userModel->updateLastLogin($user->id);

            if ($user->role === 'therapist') {
              header('Location: ' . ROOT . '/therapist');
            } else {
              header('Location: ' . ROOT . '/nurse/dashboard');
            }
            exit();
          }
        }

        $errors[] = 'Invalid email or password.';
      }
    }

    $this->view('auth/login', ['errors' => $errors]);
  }

  public function logout()
  {
    session_unset();
    session_destroy();
    header('Location: ' . ROOT . '/auth/login');
    exit();
  }
}
