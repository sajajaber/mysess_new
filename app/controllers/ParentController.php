<?php
class ParentController extends Controller
{
  private $parentModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }
    $this->parentModel = new ParentModel();
  }

  public function index()
  {
    header('Location: ' . ROOT . '/parent/dashboard');
    exit();
  }

  public function dashboard()
  {
    $parentId = $_SESSION['user_id'];
    $children = $this->parentModel->getMyChildren($parentId) ?: [];

    $this->view('parent/dashboard', [
      'children' => $children,
    ]);
  }

  public function children()
  {
    $parentId = $_SESSION['user_id'];
    $children = $this->parentModel->getMyChildren($parentId) ?: [];

    $this->view('parent/children', [
      'children' => $children,
    ]);
  }

  public function child($studentId = null)
  {
    $studentId = (int)$studentId;
    $parentId  = $_SESSION['user_id'];

    if (!$studentId || !$this->parentModel->isThisChildMine($studentId, $parentId)) {
      header('Location: ' . ROOT . '/parent/dashboard');
      exit();
    }

    $student = $this->parentModel->first(['id' => $studentId]);
    if (!$student) {
      header('Location: ' . ROOT . '/parent/dashboard');
      exit();
    }

    $this->view('parent/child', [
      'student'    => $student,
      'team'       => $this->parentModel->getCareTeam($studentId)         ?: [],
      'goals'      => $this->parentModel->getIepGoals($studentId)         ?: [],
      'milestones' => $this->parentModel->getMilestones($studentId)       ?: [],
      'progress'   => $this->parentModel->getLatestProgressMap($studentId),
      'schedules'  => $this->parentModel->getTeacchSchedules($studentId)  ?: [],
      'teacch'     => $this->parentModel->getTeacchProgress($studentId)   ?: [],
      'therapy'    => $this->parentModel->getTherapySessions($studentId)  ?: [],
      'sessions'   => $this->parentModel->getClassroomSessions($studentId)?: [],
      'observ'     => $this->parentModel->getObservations($studentId)     ?: [],
      'reports'    => $this->parentModel->getProgressReports($studentId)  ?: [],
      'homework'   => $this->parentModel->getHomework($studentId)         ?: [],
      'meds'       => $this->parentModel->getMedications($studentId)      ?: [],
      'medLogs'    => $this->parentModel->getMedicationLogs($studentId)   ?: [],
      'events'     => $this->parentModel->getHealthEvents($studentId)     ?: [],
      'records'    => $this->parentModel->getHealthRecords($studentId)    ?: [],
      'boarding'   => $this->parentModel->getBoardingLogs($studentId)     ?: [],
      'checkins'   => $this->parentModel->getCheckins($studentId)         ?: [],
    ]);
  }

  public function reports()
  {
    $parentId = $_SESSION['user_id'];
    $shared   = $this->parentModel->getSharedReports($parentId) ?: [];

    $this->view('parent/reports', [
      'shared' => $shared,
    ]);
  }

  public function report($studentId = null)
  {
    $studentId = (int)$studentId;
    $parentId  = $_SESSION['user_id'];

    if (!$studentId || !$this->parentModel->isThisChildMine($studentId, $parentId)) {
      header('Location: ' . ROOT . '/parent/dashboard');
      exit();
    }

    if (!$this->parentModel->isReportShared($studentId, $parentId)) {
      $_SESSION['error'] = 'No report has been shared for this child yet.';
      header('Location: ' . ROOT . '/parent/reports');
      exit();
    }

    $student = $this->parentModel->first(['id' => $studentId]);
    if (!$student) {
      header('Location: ' . ROOT . '/parent/dashboard');
      exit();
    }

    $reportModel = new StudentReport();

    $this->view('parent/report', [
      'reportData'    => $reportModel->build($studentId),
      'boardingStats' => !empty($student->is_boarding) ? $reportModel->boardingStats($studentId) : null,
    ]);
  }
}
