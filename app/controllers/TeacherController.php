<?php
class TeacherController extends Controller
{
  private $teacherModel;
  private $studentModel;
  private $sessionModel;
  private $observationModel;
  private $iepGoalModel;
  private $iepProgressModel;
  private $reportModel;
  private $milestoneModel;
  private $progressModel;
  private $goalBankModel;
  private $teacchScheduleModel;
  private $teacchTaskModel;
  private $teacchProgressModel;
  private $taskBankModel;
  private $homeworkModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }

    $this->teacherModel     = new Teacher();
    $this->studentModel     = new Student();
    $this->sessionModel     = new ClassroomSession();
    $this->observationModel = new AcademicObservation();
    $this->iepGoalModel     = new IepGoal();
    $this->iepProgressModel = new IepGoalProgress();
    $this->reportModel      = new ProgressReport();
    $this->milestoneModel   = new IepMilestone();
    $this->progressModel    = new GoalProgress();
    $this->goalBankModel    = new IepGoalBank();
    $this->teacchScheduleModel = new TeacchSchedule();
    $this->teacchTaskModel     = new TeacchTask();
    $this->teacchProgressModel = new TeacchProgress();
    $this->taskBankModel       = new TeacchTaskBank();
    $this->homeworkModel       = new Homework();
  }
  public function index()
  {
    header('Location: ' . ROOT . '/teacher/dashboard');
    exit();
  }
  public function dashboard()
  {
    $teacherId = $_SESSION['user_id'];

    $studentCount = $this->teacherModel->countMyStudents($teacherId);
    $sessionCount = $this->sessionModel->countSessionsForTeacher($teacherId);
    $reportCount  = $this->reportModel->countReportsForTeacher($teacherId);

    $recentSessions = $this->sessionModel->getRecentSessionsForTeacher($teacherId, 5);

    $this->view('teacher/dashboard', [
      'studentCount'   => $studentCount,
      'sessionCount'   => $sessionCount,
      'reportCount'    => $reportCount,
      'recentSessions' => $recentSessions ?: [],
    ]);
  }
  public function students()
  {
    $teacherId = $_SESSION['user_id'];
    $students  = $this->teacherModel->getMyStudents($teacherId);

    $this->view('teacher/students', [
      'students' => $students ?: [],
    ]);
  }
  public function student($studentId = null)
  {
    $studentId = (int)$studentId;

    $student = $this->findStudentIfItIsMine($studentId);
    if (!$student) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }

    $sessions     = $this->sessionModel->getSessionsForStudent($studentId);
    $observations = $this->observationModel->getObservationsForStudent($studentId);
    $iepGoals     = $this->iepGoalModel->getGoalsForStudent($studentId);
    $reports      = $this->reportModel->getReportsForStudent($studentId);
    $schedules    = $this->teacchScheduleModel->getForStudent($studentId);
    $homework     = $this->homeworkModel->getForStudent($studentId);

    $this->view('teacher/display-student', [
      'student'      => $student,
      'sessions'     => $sessions     ?: [],
      'observations' => $observations ?: [],
      'iepGoals'     => $iepGoals     ?: [],
      'reports'      => $reports      ?: [],
      'schedules'    => $schedules   ?: [],
      'homework'     => $homework   ?: [],
    ]);
  }
  public function add_session()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      if (trim($_POST['session_date'] ?? '') === '') {
        $_SESSION['error'] = 'Session date is required.';
        header('Location: ' . ROOT . '/teacher/add-session?student_id=' . $studentId);
        exit();
      }
      if (trim($_POST['subject'] ?? '') === '') {
        $_SESSION['error'] = 'Subject is required.';
        header('Location: ' . ROOT . '/teacher/add-session?student_id=' . $studentId);
        exit();
      }

      $newSessionId = $this->sessionModel->insert([
        'student_id'   => $studentId,
        'teacher_id'   => $_SESSION['user_id'],
        'session_date' => $_POST['session_date'],
        'subject'      => esc($_POST['subject']),
        'notes'        => esc($_POST['notes'] ?? ''),
      ]);
      if (trim($_POST['observation'] ?? '') !== '') {
        $this->observationModel->insert([
          'student_id'  => $studentId,
          'teacher_id'  => $_SESSION['user_id'],
          'session_id'  => $newSessionId,
          'observation' => esc($_POST['observation']),
        ]);
      }

      $_SESSION['success'] = 'Classroom session saved.';
      header('Location: ' . ROOT . '/teacher/student/' . $studentId);
      exit();
    }

    $studentId = (int)($_GET['student_id'] ?? 0);
    $student   = $this->findStudentIfItIsMine($studentId);

    if (!$student) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }

    $this->view('teacher/add-session', ['student' => $student]);
  }
  public function add_observation()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      if (trim($_POST['observation'] ?? '') === '') {
        $_SESSION['error'] = 'Observation is required.';
        header('Location: ' . ROOT . '/teacher/add-observation?student_id=' . $studentId);
        exit();
      }

      $this->observationModel->insert([
        'student_id'  => $studentId,
        'teacher_id'  => $_SESSION['user_id'],
        'session_id'  => null,
        'observation' => esc($_POST['observation']),
      ]);

      $_SESSION['success'] = 'Observation saved.';
      header('Location: ' . ROOT . '/teacher/student/' . $studentId);
      exit();
    }

    $studentId = (int)($_GET['student_id'] ?? 0);
    $student   = $this->findStudentIfItIsMine($studentId);

    if (!$student) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }

    $this->view('teacher/add-observation', ['student' => $student]);
  }
  public function add_iep_goal()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      if (trim($_POST['goal_description'] ?? '') === '') {
        $_SESSION['error'] = 'Goal description is required.';
        header('Location: ' . ROOT . '/teacher/add-iep-goal?student_id=' . $studentId);
        exit();
      }
      if (trim($_POST['target_date'] ?? '') === '') {
        $_SESSION['error'] = 'Target date is required.';
        header('Location: ' . ROOT . '/teacher/add-iep-goal?student_id=' . $studentId);
        exit();
      }

      $this->iepGoalModel->addGoal(
        $studentId,
        esc($_POST['goal_description']),
        $_POST['target_date'],
        $_POST['category'] ?? '',
        $_POST['status'] ?? 'active',
        $_SESSION['user_id']
      );

      $_SESSION['success'] = 'IEP goal added.';
      header('Location: ' . ROOT . '/teacher/student/' . $studentId);
      exit();
    }

    $studentId = (int)($_GET['student_id'] ?? 0);
    $student   = $this->findStudentIfItIsMine($studentId);

    if (!$student) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }
    $bankEntries = $this->goalBankModel->getActive();

    $this->view('teacher/add-iep-goal', [
      'student'     => $student,
      'bankEntries' => $bankEntries ?: [],
    ]);
  }
  public function goal($goalId = null)
  {
    $goal = $this->findGoalIfItIsMine($goalId);
    if (!$goal) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }

    $milestones    = $this->milestoneModel->getForGoal((int)$goal->id);
    $progressList  = $this->progressModel->getForGoal((int)$goal->id);
    $progressChart = $this->progressModel->getForGoalChrono((int)$goal->id);
    $latestScore   = $this->progressModel->getLatestScore((int)$goal->id);

    $this->view('teacher/goal-detail', [
      'goal'          => $goal,
      'milestones'    => $milestones    ?: [],
      'progressList'  => $progressList  ?: [],
      'progressChart' => $progressChart ?: [],
      'latestScore'   => $latestScore,
    ]);
  }
  public function add_milestone()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $goalId = (int)$_POST['goal_id'];
      $goal   = $this->findGoalIfItIsMine($goalId);

      if (!$goal) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      if (trim($_POST['description'] ?? '') === '') {
        $_SESSION['error'] = 'Milestone description is required.';
        header('Location: ' . ROOT . '/teacher/goal/' . $goalId);
        exit();
      }

      $this->milestoneModel->addMilestone($goalId, esc($_POST['description']));

      $_SESSION['success'] = 'Milestone added.';
      header('Location: ' . ROOT . '/teacher/goal/' . $goalId);
      exit();
    }

    header('Location: ' . ROOT . '/teacher/students');
    exit();
  }
  public function toggle_milestone()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $milestoneId = (int)$_POST['milestone_id'];
      $isAchieved  = (int)$_POST['is_achieved'];
      $milestone = $this->milestoneModel->first(['id' => $milestoneId]);
      if (!$milestone || !$this->findGoalIfItIsMine((int)$milestone->goal_id)) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      $this->milestoneModel->setAchieved($milestoneId, $isAchieved);

      header('Location: ' . ROOT . '/teacher/goal/' . (int)$milestone->goal_id);
      exit();
    }

    header('Location: ' . ROOT . '/teacher/students');
    exit();
  }
  public function record_progress()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $goalId = (int)$_POST['goal_id'];
      $goal   = $this->findGoalIfItIsMine($goalId);

      if (!$goal) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }
      $score = $_POST['score'] ?? '';
      if ($score === '' || !is_numeric($score) || $score < 0 || $score > 100) {
        $_SESSION['error'] = 'Score must be a number from 0 to 100.';
        header('Location: ' . ROOT . '/teacher/goal/' . $goalId);
        exit();
      }

      $this->progressModel->addProgress(
        $goalId,
        (int)$score,
        esc($_POST['notes'] ?? ''),
        $_SESSION['user_id']
      );

      $_SESSION['success'] = 'Progress recorded.';
      header('Location: ' . ROOT . '/teacher/goal/' . $goalId);
      exit();
    }

    header('Location: ' . ROOT . '/teacher/students');
    exit();
  }
  public function add_progress_report()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      if (trim($_POST['reporting_period'] ?? '') === '') {
        $_SESSION['error'] = 'Reporting period is required.';
        header('Location: ' . ROOT . '/teacher/add-progress-report?student_id=' . $studentId);
        exit();
      }
      if (trim($_POST['summary'] ?? '') === '') {
        $_SESSION['error'] = 'Summary is required.';
        header('Location: ' . ROOT . '/teacher/add-progress-report?student_id=' . $studentId);
        exit();
      }
      if (trim($_POST['rating'] ?? '') === '') {
        $_SESSION['error'] = 'Rating is required.';
        header('Location: ' . ROOT . '/teacher/add-progress-report?student_id=' . $studentId);
        exit();
      }

      $this->reportModel->insert([
        'student_id'       => $studentId,
        'teacher_id'       => $_SESSION['user_id'],
        'reporting_period' => esc($_POST['reporting_period']),
        'summary'          => esc($_POST['summary']),
        'rating'           => esc($_POST['rating']),
      ]);

      $_SESSION['success'] = 'Progress report submitted.';
      header('Location: ' . ROOT . '/teacher/progress-reports');
      exit();
    }

    $studentId = (int)($_GET['student_id'] ?? 0);
    $student   = $this->findStudentIfItIsMine($studentId);

    if (!$student) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }

    $this->view('teacher/add-progress-report', ['student' => $student]);
  }
  public function progress_reports()
  {
    $teacherId = $_SESSION['user_id'];
    $reports   = $this->reportModel->getRecentReportsForTeacher($teacherId, 50);

    $this->view('teacher/progress-reports', [
      'reports' => $reports ?: [],
    ]);
  }
  private function semesterOptions()
  {
    $options = [];
    $thisYear = (int)date('Y');
    for ($year = $thisYear; $year >= $thisYear - 1; $year--) {
      $options[($year + 1) . '-02-01|' . ($year + 1) . '-06-30'] = 'Spring ' . ($year + 1);
      $options[$year . '-09-01|' . $year . '-12-31']            = 'Fall ' . $year;
    }

    return $options;
  }
  public function semester_report()
  {
    $teacherId = $_SESSION['user_id'];
    $semesters = $this->semesterOptions();

    $studentId = (int)($_GET['student_id'] ?? 0);
    $semester  = $_GET['semester'] ?? '';
    if (!$studentId || $semester === '') {
      $this->view('teacher/semester-report-form', [
        'students'  => $this->teacherModel->getMyStudents($teacherId) ?: [],
        'semesters' => $semesters,
      ]);
      return;
    }
    $student = $this->findStudentIfItIsMine($studentId);
    if (!$student) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }
    if (!isset($semesters[$semester])) {
      $_SESSION['error'] = 'Please choose a valid semester.';
      header('Location: ' . ROOT . '/teacher/semester-report');
      exit();
    }
    list($startDate, $endDate) = explode('|', $semester);
    $goals  = $this->iepGoalModel->getGoalsForReport($studentId) ?: [];
    $report = [];

    foreach ($goals as $goal) {
      $baseline = $this->progressModel->getBaselineScoreInRange((int)$goal->id, $startDate, $endDate);
      $current  = $this->progressModel->getLatestScoreInRange((int)$goal->id, $startDate, $endDate);
      $entries  = $this->progressModel->countInRange((int)$goal->id, $startDate, $endDate);

      $milestones    = $this->milestoneModel->getForGoal((int)$goal->id) ?: [];
      $achievedCount = 0;
      foreach ($milestones as $m) {
        if ($m->is_achieved) {
          $achievedCount++;
        }
      }

      $report[] = [
        'goal'          => $goal,
        'baseline'      => $baseline,
        'current'       => $current,
        'change'        => ($baseline !== null && $current !== null) ? $current - $baseline : null,
        'entries'       => $entries,
        'status'        => $this->goalStatusLabel($goal->status, $current),
        'milestones'    => $milestones,
        'milestoneDone' => $achievedCount,
        'milestoneTotal' => count($milestones),
      ];
    }

    $sessions     = $this->sessionModel->getSessionsForStudentInRange($studentId, $startDate, $endDate) ?: [];
    $observations = $this->observationModel->getObservationsForStudentInRange($studentId, $startDate, $endDate) ?: [];
    $teacch       = $this->compileTeacch($studentId, $startDate, $endDate);

    $this->view('teacher/semester-report', [
      'student'       => $student,
      'semesterLabel' => $semesters[$semester],
      'startDate'     => $startDate,
      'endDate'       => $endDate,
      'report'        => $report,
      'sessions'      => $sessions,
      'observations'  => $observations,
      'teacch'        => $teacch,
      'teacherName'   => $_SESSION['user_name'] ?? '',
    ]);
  }
  private function compileTeacch($studentId, $startDate, $endDate)
  {
    $levelToPercent = [
      'full_prompt'    => 33,
      'partial_prompt' => 66,
      'independent'    => 100,
    ];

    $schedules = $this->teacchScheduleModel->getForStudent($studentId) ?: [];
    $blocks    = [];

    foreach ($schedules as $schedule) {
      $tasks    = $this->teacchTaskModel->getForSchedule((int)$schedule->id) ?: [];
      $taskRows = [];
      $sum      = 0;
      $rated    = 0;

      foreach ($tasks as $task) {
        $latest = $this->teacchProgressModel->getLatestForTaskInRange((int)$task->id, $startDate, $endDate);
        $count  = $this->teacchProgressModel->countForTaskInRange((int)$task->id, $startDate, $endDate);

        $level   = $latest ? $latest->independence_level : null;
        $percent = $level ? $levelToPercent[$level] : 0;

        if ($level) {
          $sum += $percent;
          $rated++;
        }

        $taskRows[] = [
          'title'   => $task->title,
          'order'   => $task->task_order,
          'level'   => $level,
          'percent' => $percent,
          'entries' => $count,
        ];
      }
      $blocks[] = [
        'title'   => $schedule->title,
        'tasks'   => $taskRows,
        'percent' => $rated ? (int)round($sum / $rated) : 0,
        'rated'   => $rated,
        'total'   => count($tasks),
      ];
    }

    return $blocks;
  }
  private function goalStatusLabel($status, $currentScore)
  {
    if ($status === 'achieved') {
      return 'Met';
    }
    if ($currentScore === null) {
      return 'Not Met';
    }
    if ($currentScore >= 80) {
      return 'Met';
    }
    if ($currentScore > 0) {
      return 'In Progress';
    }
    return 'Not Met';
  }
  public function sessions()
  {
    $teacherId = $_SESSION['user_id'];
    $sessions  = $this->sessionModel->getRecentSessionsForTeacher($teacherId, 50);

    $this->view('teacher/sessions', [
      'sessions' => $sessions ?: [],
    ]);
  }
  public function homework()
  {
    $teacherId = $_SESSION['user_id'];
    $homework  = $this->homeworkModel->getForTeacher($teacherId);

    $this->view('teacher/homework', [
      'homework' => $homework ?: [],
    ]);
  }
  public function assign_homework()
  {
    $teacherId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $title       = trim($_POST['title'] ?? '');
      $dueDate     = trim($_POST['due_date'] ?? '');
      $target      = $_POST['target'] ?? '';
      $description = $_POST['description'] ?? '';
      if ($title === '') {
        $_SESSION['error'] = 'Title is required.';
        header('Location: ' . ROOT . '/teacher/assign-homework');
        exit();
      }
      if ($dueDate === '') {
        $_SESSION['error'] = 'Due date is required.';
        header('Location: ' . ROOT . '/teacher/assign-homework');
        exit();
      }
      $safeTitle       = esc($title);
      $safeDescription = esc($description);

      if ($target === 'class') {
        $myStudents = $this->teacherModel->getMyStudents($teacherId);

        if (empty($myStudents)) {
          $_SESSION['error'] = 'You have no assigned students to give homework to.';
          header('Location: ' . ROOT . '/teacher/assign-homework');
          exit();
        }

        foreach ($myStudents as $student) {
          $this->homeworkModel->addHomework(
            (int)$student->id,
            $teacherId,
            $safeTitle,
            $safeDescription,
            $dueDate
          );
        }

        $_SESSION['success'] = 'Homework assigned to the whole class.';
        header('Location: ' . ROOT . '/teacher/homework');
        exit();
      }
      $studentId = (int)$target;
      if (!$studentId) {
        $_SESSION['error'] = 'Please select a student.';
        header('Location: ' . ROOT . '/teacher/assign-homework');
        exit();
      }
      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      $this->homeworkModel->addHomework(
        $studentId,
        $teacherId,
        $safeTitle,
        $safeDescription,
        $dueDate
      );

      $_SESSION['success'] = 'Homework assigned.';
      header('Location: ' . ROOT . '/teacher/homework');
      exit();
    }
    $myStudents = $this->teacherModel->getMyStudents($teacherId);

    $this->view('teacher/assign-homework', [
      'students' => $myStudents ?: [],
    ]);
  }
  public function iep_goals()
  {
    $teacherId = $_SESSION['user_id'];
    $goals     = $this->iepGoalModel->getGoalsForStaff($teacherId, 'teacher');

    $this->view('teacher/iep-goals', [
      'goals' => $goals ?: [],
    ]);
  }
  public function profile()
  {
    $userId    = $_SESSION['user_id'];
    $userModel = new User();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $firstName = trim($_POST['first_name'] ?? '');
      $lastName  = trim($_POST['last_name'] ?? '');
      $email     = trim($_POST['email'] ?? '');
      $phone     = trim($_POST['phone'] ?? '');
      if ($firstName === '' || $lastName === '' || $email === '') {
        $_SESSION['error'] = 'First name, last name, and email are required.';
        header('Location: ' . ROOT . '/teacher/profile');
        exit();
      }
      if ($userModel->emailTakenByOther($email, $userId)) {
        $_SESSION['error'] = 'That email is already used by another account.';
        header('Location: ' . ROOT . '/teacher/profile');
        exit();
      }

      $userModel->updateProfile($userId, esc($firstName), esc($lastName), esc($email), esc($phone));
      $photoError = $this->saveProfilePhoto($userId);
      if ($photoError) {
        $_SESSION['error'] = $photoError;
        header('Location: ' . ROOT . '/teacher/profile');
        exit();
      }
      $_SESSION['user_name'] = trim($firstName . ' ' . $lastName);

      $_SESSION['success'] = 'Profile updated.';
      header('Location: ' . ROOT . '/teacher/profile');
      exit();
    }

    $user = $userModel->getById($userId);
    $this->view('teacher/profile', ['user' => $user]);
  }
  private function saveProfilePhoto($userId)
  {
    if (empty($_FILES['photo']['name'])) {
      return null;
    }
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
      return 'Something went wrong uploading the picture. Please try again.';
    }
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
    $info = getimagesize($_FILES['photo']['tmp_name']);
    if ($info === false) {
      return 'That file is not an image.';
    }

    $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (!array_key_exists($extension, $allowed)) {
      return 'Please upload a JPG, PNG, or GIF image.';
    }
    $fileName = 'profile_' . (int)$userId . '.' . $extension;
    $destination = ROOT_DIR . '/public/assets/uploads/' . $fileName;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
      return 'Could not save the picture. Please try again.';
    }

    $userModel = new User();
    $userModel->updatePhoto($userId, $fileName);

    return null;
  }
  private function findStudentIfItIsMine($studentId)
  {
    $teacherId = $_SESSION['user_id'];

    if (!$studentId) {
      return null;
    }

    if (!$this->teacherModel->isThisStudentMine($studentId, $teacherId)) {
      return null;
    }

    return $this->studentModel->first(['id' => $studentId]);
  }
  private function findGoalIfItIsMine($goalId)
  {
    $goalId = (int)$goalId;
    if (!$goalId) {
      return null;
    }

    $goal = $this->iepGoalModel->getGoalById($goalId);
    if (!$goal) {
      return null;
    }
    if (!$this->findStudentIfItIsMine((int)$goal->student_id)) {
      return null;
    }

    return $goal;
  }
  public function teacch()
  {
    $teacherId = $_SESSION['user_id'];
    $schedules = $this->teacchScheduleModel->getForStaff($teacherId, 'teacher');

    $this->view('teacher/teacch', [
      'schedules' => $schedules ?: [],
    ]);
  }
  private function findScheduleIfItIsMine($scheduleId)
  {
    $scheduleId = (int)$scheduleId;
    if (!$scheduleId) {
      return null;
    }

    $schedule = $this->teacchScheduleModel->getById($scheduleId);
    if (!$schedule) {
      return null;
    }
    if (!$this->findStudentIfItIsMine((int)$schedule->student_id)) {
      return null;
    }

    return $schedule;
  }
  private function findTaskIfItIsMine($taskId)
  {
    $taskId = (int)$taskId;
    if (!$taskId) {
      return null;
    }

    $task = $this->teacchTaskModel->getById($taskId);
    if (!$task) {
      return null;
    }
    $schedule = $this->findScheduleIfItIsMine((int)$task->schedule_id);
    if (!$schedule) {
      return null;
    }
    $task->student_id = (int)$schedule->student_id;
    return $task;
  }
  public function add_schedule()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      if (trim($_POST['title'] ?? '') === '') {
        $_SESSION['error'] = 'Schedule title is required.';
        header('Location: ' . ROOT . '/teacher/student/' . $studentId);
        exit();
      }

      $this->teacchScheduleModel->addSchedule($studentId, esc($_POST['title']), $_SESSION['user_id']);

      $_SESSION['success'] = 'Schedule created.';
      header('Location: ' . ROOT . '/teacher/student/' . $studentId);
      exit();
    }

    header('Location: ' . ROOT . '/teacher/students');
    exit();
  }
  public function schedule($scheduleId = null)
  {
    $schedule = $this->findScheduleIfItIsMine($scheduleId);
    if (!$schedule) {
      header('Location: ' . ROOT . '/teacher/students');
      exit();
    }

    $tasks = $this->teacchTaskModel->getForSchedule((int)$schedule->id);
    foreach ($tasks as $task) {
      $task->latest  = $this->teacchProgressModel->getLatestForTask((int)$task->id);
      $task->history = $this->teacchProgressModel->getForTask((int)$task->id);
    }
    $bankEntries = $this->taskBankModel->getActive();
    $nextOrder   = count($tasks) + 1;

    $this->view('teacher/teacch-schedule', [
      'schedule'    => $schedule,
      'tasks'       => $tasks ?: [],
      'bankEntries' => $bankEntries ?: [],
      'nextOrder'   => $nextOrder,
    ]);
  }
  public function add_task()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $scheduleId = (int)$_POST['schedule_id'];
      $schedule   = $this->findScheduleIfItIsMine($scheduleId);

      if (!$schedule) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }

      if (trim($_POST['title'] ?? '') === '') {
        $_SESSION['error'] = 'Task title is required.';
        header('Location: ' . ROOT . '/teacher/schedule/' . $scheduleId);
        exit();
      }
      if (trim($_POST['task_order'] ?? '') === '') {
        $_SESSION['error'] = 'Task order is required.';
        header('Location: ' . ROOT . '/teacher/schedule/' . $scheduleId);
        exit();
      }

      $this->teacchTaskModel->addTask(
        $scheduleId,
        (int)$_POST['task_order'],
        esc($_POST['title']),
        esc($_POST['visual_cue_url'] ?? '')
      );

      $_SESSION['success'] = 'Task added.';
      header('Location: ' . ROOT . '/teacher/schedule/' . $scheduleId);
      exit();
    }

    header('Location: ' . ROOT . '/teacher/students');
    exit();
  }
  public function rate_independence()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $taskId = (int)$_POST['task_id'];
      $task   = $this->findTaskIfItIsMine($taskId);

      if (!$task) {
        header('Location: ' . ROOT . '/teacher/students');
        exit();
      }
      if (trim($_POST['session_date'] ?? '') === '') {
        $_SESSION['error'] = 'Session date is required.';
        header('Location: ' . ROOT . '/teacher/schedule/' . (int)$task->schedule_id);
        exit();
      }
      $level  = $_POST['independence_level'] ?? '';
      $levels = ['full_prompt' => 1, 'partial_prompt' => 1, 'independent' => 1];
      if (!isset($levels[$level])) {
        $_SESSION['error'] = 'Please choose a valid independence level.';
        header('Location: ' . ROOT . '/teacher/schedule/' . (int)$task->schedule_id);
        exit();
      }

      $this->teacchProgressModel->addRating(
        $taskId,
        (int)$task->student_id,
        $_POST['session_date'],
        $level,
        esc($_POST['notes'] ?? ''),
        $_SESSION['user_id']
      );

      $_SESSION['success'] = 'Independence recorded.';
      header('Location: ' . ROOT . '/teacher/schedule/' . (int)$task->schedule_id);
      exit();
    }

    header('Location: ' . ROOT . '/teacher/students');
    exit();
  }
}
