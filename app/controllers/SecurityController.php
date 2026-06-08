<?php
class SecurityController extends Controller
{
  private $studentModel;
  private $checkinModel;
  private $noteModel;

  const CHECK_IN_LOCK_HOUR  = 9;
  const CHECK_OUT_LOCK_HOUR = 16;

  public function __construct()
  {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'security_guard') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }

    $this->studentModel = new Student();
    $this->checkinModel = new CheckinLog();
    $this->noteModel    = new AttendanceNote();
  }


  public function index()
  {
    header('Location: ' . ROOT . '/security/dashboard');
    exit();
  }


  public function dashboard()
  {
    $students = $this->studentModel->getAllActive() ?: [];
    $todayMap = $this->checkinModel->getTodayMapForStudents();
    $noteMap  = $this->noteModel->getMapForDate(date('Y-m-d'));

    $checkedIn  = 0;
    $checkedOut = 0;
    foreach ($todayMap as $row) {
      if ($row['check_in'])  { $checkedIn++; }
      if ($row['check_out']) { $checkedOut++; }
    }

    $diagnoses = [];
    foreach ($students as $s) {
      $d = trim($s->diagnosis ?? '');
      if ($d !== '' && !in_array($d, $diagnoses, true)) {
        $diagnoses[] = $d;
      }
    }
    sort($diagnoses);

    $now            = (int)date('G');
    $checkInLocked  = $now >= self::CHECK_IN_LOCK_HOUR;
    $checkOutLocked = $now >= self::CHECK_OUT_LOCK_HOUR;

    $this->view('security/dashboard', [
      'students'       => $students,
      'todayMap'       => $todayMap,
      'noteMap'        => $noteMap,
      'totalCount'     => count($students),
      'inCount'        => $checkedIn,
      'outCount'       => $checkedOut,
      'diagnoses'      => $diagnoses,
      'checkInLocked'  => $checkInLocked,
      'checkOutLocked' => $checkOutLocked,
      'inLockHour'     => self::CHECK_IN_LOCK_HOUR,
      'outLockHour'    => self::CHECK_OUT_LOCK_HOUR,
    ]);
  }


  public function toggle()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }

    $studentId = (int)($_POST['student_id'] ?? 0);
    $checkType = $_POST['check_type'] ?? '';

    $allowed = ['check_in' => 1, 'check_out' => 1];
    if (!$studentId || !isset($allowed[$checkType])) {
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }

    $now = (int)date('G');
    if ($checkType === 'check_in' && $now >= self::CHECK_IN_LOCK_HOUR) {
      $_SESSION['error'] = 'Check-in is locked after '
        . str_pad((string)self::CHECK_IN_LOCK_HOUR, 2, '0', STR_PAD_LEFT) . ':00.';
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }
    if ($checkType === 'check_out' && $now >= self::CHECK_OUT_LOCK_HOUR) {
      $_SESSION['error'] = 'Check-out is locked after '
        . str_pad((string)self::CHECK_OUT_LOCK_HOUR, 2, '0', STR_PAD_LEFT) . ':00.';
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }

    $student = $this->studentModel->first(['id' => $studentId, 'is_active' => 1]);
    if (!$student) {
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }

    $whenString = date('Y-m-d H:i:s');

    if ($this->checkinModel->hasTodayType($studentId, $checkType)) {
      $this->checkinModel->updateTodayTypeTime($studentId, $checkType, $whenString);
      $_SESSION['success'] = ($checkType === 'check_in' ? 'Check-in' : 'Check-out')
        . ' time updated for ' . trim($student->first_name . ' ' . $student->last_name) . '.';
    } else {
      $this->checkinModel->insert([
        'student_id' => $studentId,
        'check_type' => $checkType,
        'check_time' => $whenString,
        'notes'      => '',
        'logged_by'  => $_SESSION['user_id'],
      ]);
      $_SESSION['success'] = ($checkType === 'check_in' ? 'Check-in' : 'Check-out')
        . ' recorded for ' . trim($student->first_name . ' ' . $student->last_name) . '.';
    }

    header('Location: ' . ROOT . '/security/dashboard');
    exit();
  }


  public function save_note()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }

    $studentId = (int)($_POST['student_id'] ?? 0);
    $note      = trim($_POST['note'] ?? '');

    if (!$studentId) {
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }

    $student = $this->studentModel->first(['id' => $studentId, 'is_active' => 1]);
    if (!$student) {
      header('Location: ' . ROOT . '/security/dashboard');
      exit();
    }

    $this->noteModel->saveNote($studentId, date('Y-m-d'), esc($note), $_SESSION['user_id']);

    $_SESSION['success'] = 'Note saved and sent to admin for '
      . trim($student->first_name . ' ' . $student->last_name) . '.';
    header('Location: ' . ROOT . '/security/dashboard');
    exit();
  }


  public function checkins()
  {
    $date    = $_GET['date'] ?? date('Y-m-d');
    $records = $this->checkinModel->getDailyPivot($date);
    $noteMap = $this->noteModel->getMapForDate($date);

    $this->view('security/checkins', [
      'date'    => $date,
      'records' => $records ?: [],
      'noteMap' => $noteMap,
    ]);
  }
}
