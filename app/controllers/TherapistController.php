<?php
class TherapistController extends Controller
{
  private $therapistModel;
  private $studentModel;
  private $sessionModel;
  private $iepGoalModel;
  private $iepProgressModel;
  private $milestoneModel;
  private $progressModel;
  private $goalBankModel;
  private $teacchScheduleModel;
  private $teacchTaskModel;
  private $teacchProgressModel;
  private $taskBankModel;

  public function __construct()
  {
    // Only logged-in therapists are allowed past this point
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'therapist') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }

    $this->therapistModel   = new Therapist();
    $this->studentModel      = new Student();
    $this->sessionModel      = new TherapySession();
    $this->iepGoalModel      = new IepGoal();         // reused from the Teacher Module
    $this->iepProgressModel  = new IepGoalProgress(); // reused from the Teacher Module
    $this->milestoneModel    = new IepMilestone();
    $this->progressModel     = new GoalProgress();
    $this->goalBankModel     = new IepGoalBank();
    $this->teacchScheduleModel = new TeacchSchedule();
    $this->teacchTaskModel     = new TeacchTask();
    $this->teacchProgressModel = new TeacchProgress();
    $this->taskBankModel       = new TeacchTaskBank();
  }

  // /therapist  -> send to the dashboard
  public function index()
  {
    header('Location: ' . ROOT . '/therapist/dashboard');
    exit();
  }

  // /therapist/dashboard
  public function dashboard()
  {
    $therapistId = $_SESSION['user_id'];

    $studentCount   = $this->therapistModel->countMyStudents($therapistId);
    $sessionCount   = $this->sessionModel->countSessionsForTherapist($therapistId);
    $scheduledCount = $this->sessionModel->countScheduledForTherapist($therapistId);

    $recentSessions = $this->sessionModel->getRecentSessionsForTherapist($therapistId, 5);

    $this->view('therapist/dashboard', [
      'studentCount'   => $studentCount,
      'sessionCount'   => $sessionCount,
      'scheduledCount' => $scheduledCount,
      'recentSessions' => $recentSessions ?: [],
    ]);
  }

  // /therapist/students
  public function students()
  {
    $therapistId = $_SESSION['user_id'];
    $students    = $this->therapistModel->getMyStudents($therapistId);

    $this->view('therapist/students', [
      'students' => $students ?: [],
    ]);
  }

  // /therapist/student/{id} — full therapy profile
  public function student($studentId = null)
  {
    $studentId = (int)$studentId;

    $student = $this->findStudentIfItIsMine($studentId);
    if (!$student) {
      header('Location: ' . ROOT . '/therapist/students');
      exit();
    }

    $sessions    = $this->sessionModel->getSessionsForStudent($studentId);
    $iepGoals    = $this->iepGoalModel->getGoalsForStudent($studentId);
    $schedules   = $this->teacchScheduleModel->getForStudent($studentId);

    $this->view('therapist/display-student', [
      'student'     => $student,
      'sessions'    => $sessions    ?: [],
      'iepGoals'    => $iepGoals    ?: [],
      'schedules'   => $schedules   ?: [],
    ]);
  }

  // /therapist/sessions
  public function sessions()
  {
    $therapistId = $_SESSION['user_id'];
    $sessions    = $this->sessionModel->getRecentSessionsForTherapist($therapistId, 50);

    $this->view('therapist/sessions', [
      'sessions' => $sessions ?: [],
    ]);
  }

  // The semesters a therapist can report on (machine key => friendly label)
  // Key is "startDate|endDate" so the report can split it without storing anything
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

  // /therapist/semester-report — pick a student + semester, then auto-build the report
  public function semester_report()
  {
    $therapistId = $_SESSION['user_id'];
    $semesters   = $this->semesterOptions();

    $studentId = (int)($_GET['student_id'] ?? 0);
    $semester  = $_GET['semester'] ?? '';

    // No choices yet, so just show the picker form
    if (!$studentId || $semester === '') {
      $this->view('therapist/semester-report-form', [
        'students'  => $this->therapistModel->getMyStudents($therapistId) ?: [],
        'semesters' => $semesters,
      ]);
      return;
    }

    // The student must be one of mine
    $student = $this->findStudentIfItIsMine($studentId);
    if (!$student) {
      header('Location: ' . ROOT . '/therapist/students');
      exit();
    }

    // The semester must be one we offered
    if (!isset($semesters[$semester])) {
      $_SESSION['error'] = 'Please choose a valid semester.';
      header('Location: ' . ROOT . '/therapist/semester-report');
      exit();
    }

    list($startDate, $endDate) = explode('|', $semester);

    // Build one tidy block of data per goal: baseline, current, change, status, milestones
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
        'goal'           => $goal,
        'baseline'       => $baseline,
        'current'        => $current,
        'change'         => ($baseline !== null && $current !== null) ? $current - $baseline : null,
        'entries'        => $entries,
        'status'         => $this->goalStatusLabel($goal->status, $current),
        'milestones'     => $milestones,
        'milestoneDone'  => $achievedCount,
        'milestoneTotal' => count($milestones),
      ];
    }

    $sessions = $this->sessionModel->getSessionsForStudentInRange($studentId, $startDate, $endDate) ?: [];
    $teacch   = $this->compileTeacch($studentId, $startDate, $endDate);

    $this->view('therapist/semester-report', [
      'student'       => $student,
      'semesterLabel' => $semesters[$semester],
      'startDate'     => $startDate,
      'endDate'       => $endDate,
      'report'        => $report,
      'sessions'      => $sessions,
      'teacch'        => $teacch,
      'therapistName' => $_SESSION['user_name'] ?? '',
    ]);
  }

  // Turn a goal status + latest score into a simple "Met / In Progress / Not Met" label
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

  // Build the TEACCH part of the report: each schedule with its tasks, the latest
  // independence level per task inside the range, and a schedule percentage for the bar.
  private function compileTeacch($studentId, $startDate, $endDate)
  {
    // Turn an independence level into a 0-100 number we can average and chart
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

  // /therapist/schedule-session
  public function schedule_session()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      if (trim($_POST['session_date'] ?? '') === '') {
        $_SESSION['error'] = 'Session date is required.';
        header('Location: ' . ROOT . '/therapist/schedule-session?student_id=' . $studentId);
        exit();
      }
      if (trim($_POST['session_type'] ?? '') === '') {
        $_SESSION['error'] = 'Session type is required.';
        header('Location: ' . ROOT . '/therapist/schedule-session?student_id=' . $studentId);
        exit();
      }

      // A new session starts as "scheduled" with no notes and no goal yet
      $this->sessionModel->insert([
        'student_id'   => $studentId,
        'therapist_id' => $_SESSION['user_id'],
        'session_date' => $_POST['session_date'],
        'session_type' => esc($_POST['session_type']),
        'status'       => 'scheduled',
      ]);

      $_SESSION['success'] = 'Therapy session scheduled.';
      header('Location: ' . ROOT . '/therapist/student/' . $studentId);
      exit();
    }

    $studentId = (int)($_GET['student_id'] ?? 0);
    $student   = $this->findStudentIfItIsMine($studentId);

    if (!$student) {
      header('Location: ' . ROOT . '/therapist/students');
      exit();
    }

    $this->view('therapist/schedule-session', ['student' => $student]);
  }

  // /therapist/document-session
  public function document_session()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $sessionId = (int)$_POST['session_id'];
      $session   = $this->sessionModel->first(['id' => $sessionId]);

      // The session must exist and belong to one of this therapist's students
      if (!$session || !$this->findStudentIfItIsMine((int)$session->student_id)) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      if (trim($_POST['performance_notes'] ?? '') === '') {
        $_SESSION['error'] = 'Performance notes are required.';
        header('Location: ' . ROOT . '/therapist/document-session?session_id=' . $sessionId);
        exit();
      }

      // The goal addressed is optional, so it may be empty (NULL)
      $goalAddressedId = !empty($_POST['goal_addressed_id'])
        ? (int)$_POST['goal_addressed_id']
        : null;

      // Fill in the same session row and mark it completed
      $this->sessionModel->update($sessionId, [
        'performance_notes' => esc($_POST['performance_notes']),
        'status'            => 'completed',
        'goal_addressed_id' => $goalAddressedId,
      ]);

      $_SESSION['success'] = 'Session documented.';
      header('Location: ' . ROOT . '/therapist/student/' . (int)$session->student_id);
      exit();
    }

    $sessionId = (int)($_GET['session_id'] ?? 0);
    $session   = $this->sessionModel->first(['id' => $sessionId]);

    if (!$session || !$this->findStudentIfItIsMine((int)$session->student_id)) {
      header('Location: ' . ROOT . '/therapist/students');
      exit();
    }

    // Load this student's goals so the therapist can pick which one was addressed
    $iepGoals = $this->iepGoalModel->getGoalsForStudent((int)$session->student_id);

    $this->view('therapist/document-session', [
      'session'  => $session,
      'iepGoals' => $iepGoals ?: [],
    ]);
  }

  // /therapist/add-iep-goal  (saves into the existing iep_goals table)
  public function add_iep_goal()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      if (trim($_POST['goal_description'] ?? '') === '') {
        $_SESSION['error'] = 'Goal description is required.';
        header('Location: ' . ROOT . '/therapist/add-iep-goal?student_id=' . $studentId);
        exit();
      }
      if (trim($_POST['target_date'] ?? '') === '') {
        $_SESSION['error'] = 'Target date is required.';
        header('Location: ' . ROOT . '/therapist/add-iep-goal?student_id=' . $studentId);
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
      header('Location: ' . ROOT . '/therapist/student/' . $studentId);
      exit();
    }

    $studentId = (int)($_GET['student_id'] ?? 0);
    $student   = $this->findStudentIfItIsMine($studentId);

    if (!$student) {
      header('Location: ' . ROOT . '/therapist/students');
      exit();
    }

    // Active bank entries for the optional "choose from bank" picker
    $bankEntries = $this->goalBankModel->getActive();

    $this->view('therapist/add-iep-goal', [
      'student'     => $student,
      'bankEntries' => $bankEntries ?: [],
    ]);
  }

  // /therapist/goal/{id} — goal detail page (milestones + progress + charts)
  public function goal($goalId = null)
  {
    $goal = $this->findGoalIfItIsMine($goalId);
    if (!$goal) {
      header('Location: ' . ROOT . '/therapist/students');
      exit();
    }

    $milestones    = $this->milestoneModel->getForGoal((int)$goal->id);
    $progressList  = $this->progressModel->getForGoal((int)$goal->id);
    $progressChart = $this->progressModel->getForGoalChrono((int)$goal->id);
    $latestScore   = $this->progressModel->getLatestScore((int)$goal->id);

    $this->view('therapist/goal-detail', [
      'goal'          => $goal,
      'milestones'    => $milestones    ?: [],
      'progressList'  => $progressList  ?: [],
      'progressChart' => $progressChart ?: [],
      'latestScore'   => $latestScore,
    ]);
  }

  // /therapist/add-milestone
  public function add_milestone()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $goalId = (int)$_POST['goal_id'];
      $goal   = $this->findGoalIfItIsMine($goalId);

      if (!$goal) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      if (trim($_POST['description'] ?? '') === '') {
        $_SESSION['error'] = 'Milestone description is required.';
        header('Location: ' . ROOT . '/therapist/goal/' . $goalId);
        exit();
      }

      $this->milestoneModel->addMilestone($goalId, esc($_POST['description']));

      $_SESSION['success'] = 'Milestone added.';
      header('Location: ' . ROOT . '/therapist/goal/' . $goalId);
      exit();
    }

    header('Location: ' . ROOT . '/therapist/students');
    exit();
  }

  // /therapist/toggle-milestone
  public function toggle_milestone()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $milestoneId = (int)$_POST['milestone_id'];
      $isAchieved  = (int)$_POST['is_achieved'];

      $milestone = $this->milestoneModel->first(['id' => $milestoneId]);
      if (!$milestone || !$this->findGoalIfItIsMine((int)$milestone->goal_id)) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      $this->milestoneModel->setAchieved($milestoneId, $isAchieved);

      header('Location: ' . ROOT . '/therapist/goal/' . (int)$milestone->goal_id);
      exit();
    }

    header('Location: ' . ROOT . '/therapist/students');
    exit();
  }

  // /therapist/record-progress — save a numeric score (0-100) for a goal
  public function record_progress()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $goalId = (int)$_POST['goal_id'];
      $goal   = $this->findGoalIfItIsMine($goalId);

      if (!$goal) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      // The score must be a number from 0 to 100
      $score = $_POST['score'] ?? '';
      if ($score === '' || !is_numeric($score) || $score < 0 || $score > 100) {
        $_SESSION['error'] = 'Score must be a number from 0 to 100.';
        header('Location: ' . ROOT . '/therapist/goal/' . $goalId);
        exit();
      }

      $this->progressModel->addProgress(
        $goalId,
        (int)$score,
        esc($_POST['notes'] ?? ''),
        $_SESSION['user_id']
      );

      $_SESSION['success'] = 'Progress recorded.';
      header('Location: ' . ROOT . '/therapist/goal/' . $goalId);
      exit();
    }

    header('Location: ' . ROOT . '/therapist/students');
    exit();
  }

  // /therapist/iep-goals — every IEP goal across all my students
  public function iep_goals()
  {
    $therapistId = $_SESSION['user_id'];
    $goals       = $this->iepGoalModel->getGoalsForStaff($therapistId, 'therapist');

    $this->view('therapist/iep-goals', [
      'goals' => $goals ?: [],
    ]);
  }

  // /therapist/profile — view and edit my own details + profile picture
  public function profile()
  {
    $userId    = $_SESSION['user_id'];
    $userModel = new User();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $firstName = trim($_POST['first_name'] ?? '');
      $lastName  = trim($_POST['last_name'] ?? '');
      $email     = trim($_POST['email'] ?? '');
      $phone     = trim($_POST['phone'] ?? '');

      // Name and email are required
      if ($firstName === '' || $lastName === '' || $email === '') {
        $_SESSION['error'] = 'First name, last name, and email are required.';
        header('Location: ' . ROOT . '/therapist/profile');
        exit();
      }

      // Make sure the email is not already used by someone else
      if ($userModel->emailTakenByOther($email, $userId)) {
        $_SESSION['error'] = 'That email is already used by another account.';
        header('Location: ' . ROOT . '/therapist/profile');
        exit();
      }

      $userModel->updateProfile($userId, esc($firstName), esc($lastName), esc($email), esc($phone));

      // If a picture was uploaded, save it
      $photoError = $this->saveProfilePhoto($userId);
      if ($photoError) {
        $_SESSION['error'] = $photoError;
        header('Location: ' . ROOT . '/therapist/profile');
        exit();
      }

      // Keep the name shown in the sidebar fresh
      $_SESSION['user_name'] = trim($firstName . ' ' . $lastName);

      $_SESSION['success'] = 'Profile updated.';
      header('Location: ' . ROOT . '/therapist/profile');
      exit();
    }

    $user = $userModel->getById($userId);
    $this->view('therapist/profile', ['user' => $user]);
  }

  // Saves an uploaded profile picture, if one was chosen.
  // Returns an error message string on failure, or null when all is fine.
  private function saveProfilePhoto($userId)
  {
    // No file chosen is perfectly fine
    if (empty($_FILES['photo']['name'])) {
      return null;
    }

    // Catch upload problems other than "no file"
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
      return 'Something went wrong uploading the picture. Please try again.';
    }

    // Only allow real image files
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
    $info = getimagesize($_FILES['photo']['tmp_name']);
    if ($info === false) {
      return 'That file is not an image.';
    }

    $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (!array_key_exists($extension, $allowed)) {
      return 'Please upload a JPG, PNG, or GIF image.';
    }

    // Save it as profile_{userId}.{ext} so each user has one picture
    $fileName = 'profile_' . (int)$userId . '.' . $extension;
    $destination = ROOT_DIR . '/public/assets/uploads/' . $fileName;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
      return 'Could not save the picture. Please try again.';
    }

    $userModel = new User();
    $userModel->updatePhoto($userId, $fileName);

    return null;
  }

  // Returns the student only if they belong to the current therapist, else null
  private function findStudentIfItIsMine($studentId)
  {
    $therapistId = $_SESSION['user_id'];

    if (!$studentId) {
      return null;
    }

    if (!$this->therapistModel->isThisStudentMine($studentId, $therapistId)) {
      return null;
    }

    return $this->studentModel->first(['id' => $studentId]);
  }

  // Returns the goal only if its student belongs to the current therapist, else null
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

    // Reuse the student ownership check
    if (!$this->findStudentIfItIsMine((int)$goal->student_id)) {
      return null;
    }

    return $goal;
  }

  // /therapist/teacch — every TEACCH schedule across all my students
  public function teacch()
  {
    $therapistId = $_SESSION['user_id'];
    $schedules   = $this->teacchScheduleModel->getForStaff($therapistId, 'therapist');

    $this->view('therapist/teacch', [
      'schedules' => $schedules ?: [],
    ]);
  }

  // Returns the schedule only if its student belongs to the current therapist, else null
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

    // Reuse the student ownership check
    if (!$this->findStudentIfItIsMine((int)$schedule->student_id)) {
      return null;
    }

    return $schedule;
  }

  // Returns the task only if its schedule's student belongs to me, else null.
  // The returned task gets a student_id added on, ready for the rating insert.
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

    // The task belongs to a schedule; make sure that schedule is mine
    $schedule = $this->findScheduleIfItIsMine((int)$task->schedule_id);
    if (!$schedule) {
      return null;
    }

    // Hand back the task with its student id so we can save a rating for it
    $task->student_id = (int)$schedule->student_id;
    return $task;
  }

  // /therapist/add-schedule — create a visual schedule for a student
  public function add_schedule()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $studentId = (int)$_POST['student_id'];

      if (!$this->findStudentIfItIsMine($studentId)) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      if (trim($_POST['title'] ?? '') === '') {
        $_SESSION['error'] = 'Schedule title is required.';
        header('Location: ' . ROOT . '/therapist/student/' . $studentId);
        exit();
      }

      $this->teacchScheduleModel->addSchedule($studentId, esc($_POST['title']), $_SESSION['user_id']);

      $_SESSION['success'] = 'Schedule created.';
      header('Location: ' . ROOT . '/therapist/student/' . $studentId);
      exit();
    }

    header('Location: ' . ROOT . '/therapist/students');
    exit();
  }

  // /therapist/schedule/{id} — the schedule detail page (tasks + ratings)
  public function schedule($scheduleId = null)
  {
    $schedule = $this->findScheduleIfItIsMine($scheduleId);
    if (!$schedule) {
      header('Location: ' . ROOT . '/therapist/students');
      exit();
    }

    $tasks = $this->teacchTaskModel->getForSchedule((int)$schedule->id);

    // For each task, attach its latest level and its rating history
    foreach ($tasks as $task) {
      $task->latest  = $this->teacchProgressModel->getLatestForTask((int)$task->id);
      $task->history = $this->teacchProgressModel->getForTask((int)$task->id);
    }

    // Active bank tasks for the picker, and a sensible default order number
    $bankEntries = $this->taskBankModel->getActive();
    $nextOrder   = count($tasks) + 1;

    $this->view('therapist/teacch-schedule', [
      'schedule'    => $schedule,
      'tasks'       => $tasks ?: [],
      'bankEntries' => $bankEntries ?: [],
      'nextOrder'   => $nextOrder,
    ]);
  }

  // /therapist/add-task — add an ordered task to a schedule
  public function add_task()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $scheduleId = (int)$_POST['schedule_id'];
      $schedule   = $this->findScheduleIfItIsMine($scheduleId);

      if (!$schedule) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      if (trim($_POST['title'] ?? '') === '') {
        $_SESSION['error'] = 'Task title is required.';
        header('Location: ' . ROOT . '/therapist/schedule/' . $scheduleId);
        exit();
      }
      if (trim($_POST['task_order'] ?? '') === '') {
        $_SESSION['error'] = 'Task order is required.';
        header('Location: ' . ROOT . '/therapist/schedule/' . $scheduleId);
        exit();
      }

      $this->teacchTaskModel->addTask(
        $scheduleId,
        (int)$_POST['task_order'],
        esc($_POST['title']),
        esc($_POST['visual_cue_url'] ?? '')
      );

      $_SESSION['success'] = 'Task added.';
      header('Location: ' . ROOT . '/therapist/schedule/' . $scheduleId);
      exit();
    }

    header('Location: ' . ROOT . '/therapist/students');
    exit();
  }

  // /therapist/rate-independence — record an independence rating for a task
  public function rate_independence()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $taskId = (int)$_POST['task_id'];
      $task   = $this->findTaskIfItIsMine($taskId);

      if (!$task) {
        header('Location: ' . ROOT . '/therapist/students');
        exit();
      }

      // session date and level are both required
      if (trim($_POST['session_date'] ?? '') === '') {
        $_SESSION['error'] = 'Session date is required.';
        header('Location: ' . ROOT . '/therapist/schedule/' . (int)$task->schedule_id);
        exit();
      }

      // The level must be one of our three allowed values
      $level  = $_POST['independence_level'] ?? '';
      $levels = ['full_prompt' => 1, 'partial_prompt' => 1, 'independent' => 1];
      if (!isset($levels[$level])) {
        $_SESSION['error'] = 'Please choose a valid independence level.';
        header('Location: ' . ROOT . '/therapist/schedule/' . (int)$task->schedule_id);
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
      header('Location: ' . ROOT . '/therapist/schedule/' . (int)$task->schedule_id);
      exit();
    }

    header('Location: ' . ROOT . '/therapist/students');
    exit();
  }
}
