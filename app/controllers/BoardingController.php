<?php
class BoardingController extends Controller
{
  private $studentModel;
  private $logModel;
  private $checkinModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'boarding_staff') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }

    $this->studentModel = new Student();
    $this->logModel     = new BoardingLog();
    $this->checkinModel = new CheckinLog();
  }


  public function index()
  {
    header('Location: ' . ROOT . '/boarding/dashboard');
    exit();
  }

  public function dashboard()
  {
    $students = $this->studentModel->getAllActive();

    $this->view('boarding/dashboard', [
      'studentCount' => count($students ?: []),
      'logsToday'    => $this->logModel->countToday(),
      'recentLogs'   => $this->logModel->getRecent(5)     ?: [],
      'recentCheck'  => $this->checkinModel->getRecent(5) ?: [],
    ]);
  }


  public function students()
  {
    $students = $this->studentModel->getAllActive();
    $this->view('boarding/students', ['students' => $students ?: []]);
  }

  public function student($studentId = null)
  {
    $student = $this->studentModel->first(['id' => (int)$studentId]);
    if (!$student) {
      header('Location: ' . ROOT . '/boarding/students');
      exit();
    }

    $this->view('boarding/display-student', [
      'student'  => $student,
      'logs'     => $this->logModel->getForStudent((int)$student->id)     ?: [],
      'checkins' => $this->checkinModel->getForStudent((int)$student->id) ?: [],
    ]);
  }


  public function sleep_logs()
  {
    $logs = $this->logModel->getByTypeRecent('sleep', 50);
    $this->view('boarding/sleep-logs', ['logs' => $logs ?: []]);
  }

  public function add_sleep()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (trim($_POST['log_date'] ?? '') === '' || trim($_POST['description'] ?? '') === '') {
        $_SESSION['error'] = 'Date and description are required.';
        header('Location: ' . ROOT . '/boarding/add-sleep?student_id=' . $studentId);
        exit();
      }

      $this->logModel->insert([
        'student_id'    => $studentId,
        'log_date'      => $_POST['log_date'],
        'log_type'      => 'sleep',
        'description'   => esc($_POST['description']),
        'logged_by'     => $_SESSION['user_id'],
        'sleep_quality' => $_POST['sleep_quality'] ?: null,
        'bedtime'       => $_POST['bedtime']     ?: null,
        'wakeup_time'   => $_POST['wakeup_time'] ?: null,
      ]);

      $_SESSION['success'] = 'Sleep log saved.';
      header('Location: ' . ROOT . '/boarding/student/' . $studentId);
      exit();
    }

    $student = $this->studentModel->first(['id' => (int)($_GET['student_id'] ?? 0)]);
    if (!$student) {
      header('Location: ' . ROOT . '/boarding/students');
      exit();
    }

    $this->view('boarding/add-sleep', ['student' => $student]);
  }


  public function nutrition_logs()
  {
    $logs = $this->logModel->getByTypeRecent('meal', 50);
    $this->view('boarding/nutrition-logs', ['logs' => $logs ?: []]);
  }

  public function add_nutrition()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (trim($_POST['log_date'] ?? '') === '' || trim($_POST['description'] ?? '') === '') {
        $_SESSION['error'] = 'Date and description are required.';
        header('Location: ' . ROOT . '/boarding/add-nutrition?student_id=' . $studentId);
        exit();
      }

      $this->logModel->insert([
        'student_id'     => $studentId,
        'log_date'       => $_POST['log_date'],
        'log_type'       => 'meal',
        'description'    => esc($_POST['description']),
        'logged_by'      => $_SESSION['user_id'],
        'appetite_level' => $_POST['appetite_level'] ?: null,
      ]);

      $_SESSION['success'] = 'Nutrition log saved.';
      header('Location: ' . ROOT . '/boarding/student/' . $studentId);
      exit();
    }

    $student = $this->studentModel->first(['id' => (int)($_GET['student_id'] ?? 0)]);
    if (!$student) {
      header('Location: ' . ROOT . '/boarding/students');
      exit();
    }

    $this->view('boarding/add-nutrition', ['student' => $student]);
  }


  public function mood_logs()
  {
    $logs = $this->logModel->getByTypeRecent('behavior', 50);
    $this->view('boarding/mood-logs', ['logs' => $logs ?: []]);
  }

  public function add_mood()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (trim($_POST['log_date'] ?? '') === '' || trim($_POST['description'] ?? '') === '') {
        $_SESSION['error'] = 'Date and description are required.';
        header('Location: ' . ROOT . '/boarding/add-mood?student_id=' . $studentId);
        exit();
      }

      $this->logModel->insert([
        'student_id'     => $studentId,
        'log_date'       => $_POST['log_date'],
        'log_type'       => 'behavior',
        'description'    => esc($_POST['description']),
        'logged_by'      => $_SESSION['user_id'],
        'mood_indicator' => $_POST['mood_indicator'] ?: null,
      ]);

      $_SESSION['success'] = 'Mood log saved.';
      header('Location: ' . ROOT . '/boarding/student/' . $studentId);
      exit();
    }

    $student = $this->studentModel->first(['id' => (int)($_GET['student_id'] ?? 0)]);
    if (!$student) {
      header('Location: ' . ROOT . '/boarding/students');
      exit();
    }

    $this->view('boarding/add-mood', ['student' => $student]);
  }


  public function activity_logs()
  {
    $logs = $this->logModel->getByTypeRecent('daily_activity', 50);
    $this->view('boarding/activity-logs', ['logs' => $logs ?: []]);
  }

  public function add_activity()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (trim($_POST['log_date'] ?? '') === '' || trim($_POST['description'] ?? '') === '') {
        $_SESSION['error'] = 'Date and description are required.';
        header('Location: ' . ROOT . '/boarding/add-activity?student_id=' . $studentId);
        exit();
      }

      $this->logModel->insert([
        'student_id'  => $studentId,
        'log_date'    => $_POST['log_date'],
        'log_type'    => 'daily_activity',
        'description' => esc($_POST['description']),
        'logged_by'   => $_SESSION['user_id'],
      ]);

      $_SESSION['success'] = 'Activity log saved.';
      header('Location: ' . ROOT . '/boarding/student/' . $studentId);
      exit();
    }

    $student = $this->studentModel->first(['id' => (int)($_GET['student_id'] ?? 0)]);
    if (!$student) {
      header('Location: ' . ROOT . '/boarding/students');
      exit();
    }

    $this->view('boarding/add-activity', ['student' => $student]);
  }


  public function add_checkin()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (trim($_POST['check_time'] ?? '') === '') {
        $_SESSION['error'] = 'Check time is required.';
        header('Location: ' . ROOT . '/boarding/add-checkin?student_id=' . $studentId);
        exit();
      }

      $this->checkinModel->insert([
        'student_id' => $studentId,
        'check_type' => $_POST['check_type'],
        'check_time' => $_POST['check_time'],
        'notes'      => esc($_POST['notes'] ?? ''),
        'logged_by'  => $_SESSION['user_id'],
      ]);

      $_SESSION['success'] = 'Check recorded.';
      header('Location: ' . ROOT . '/boarding/student/' . $studentId);
      exit();
    }

    $student = $this->studentModel->first(['id' => (int)($_GET['student_id'] ?? 0)]);
    if (!$student) {
      header('Location: ' . ROOT . '/boarding/students');
      exit();
    }

    $this->view('boarding/add-checkin', ['student' => $student]);
  }
}
