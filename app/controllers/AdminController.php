<?php
class AdminController extends Controller
{
  private $adminModel;
  private $goalBankModel;
  private $taskBankModel;
  private $medicationModel;
  private $medLogModel;
  private $healthEventModel;
  private $healthRecordModel;
  private $classroomSessionModel;
  private $therapySessionModel;

  private $checkinModel;
  private $noteModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }
    $this->adminModel        = new Admin();
    $this->goalBankModel     = new IepGoalBank();
    $this->taskBankModel     = new TeacchTaskBank();
    $this->medicationModel     = new Medication();
    $this->medLogModel         = new MedicationLog();
    $this->healthEventModel    = new HealthEvent();
    $this->healthRecordModel   = new HealthRecord();
    $this->classroomSessionModel = new ClassroomSession();
    $this->therapySessionModel   = new TherapySession();
    $this->checkinModel          = new CheckinLog();
    $this->noteModel             = new AttendanceNote();
  }

  public function dashboard()
  {
    $stats          = $this->adminModel->getDashboardStats();
    $recentUsers    = $this->adminModel->getRecentUsers(5);
    $recentStudents = $this->adminModel->getRecentStudents(5);

    $this->view('admin/dashboard', [
      'stats'          => $stats,
      'recentUsers'    => $recentUsers    ?: [],
      'recentStudents' => $recentStudents ?: [],
    ]);
  }

  public function users()
  {
    $users = $this->adminModel->getAllUsers();

    $this->view(
      'admin/users',
      ['users' => $users,]
    );
  }

  public function add_user()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $errors = [];

      if (empty($_POST['first_name']))
        $errors[] = 'First name is required.';

      if (empty($_POST['last_name']))
        $errors[] = 'Last name is required.';

      if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'A valid email is required.';
      elseif (!str_ends_with(strtolower($_POST['email']), '@mysess.com'))
        $errors[] = 'Email must end with @mysess.com';
      elseif ($this->adminModel->emailExists($_POST['email']))
        $errors[] = 'Email is already taken.';

      if (empty($_POST['role']) || !in_array($_POST['role'], $this->adminModel->getRoles()))
        $errors[] = 'Please select a valid role.';

      if (empty($_POST['password']) || strlen($_POST['password']) < 6)
        $errors[] = 'Password must be at least 6 characters.';
      elseif ($_POST['password'] !== $_POST['password_confirm'])
        $errors[] = 'Passwords do not match.';

      if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = $_POST;
        header('Location: ' . ROOT . '/admin/add_user');
        exit();
      }

      $this->adminModel->createUser([
        'first_name' => esc($_POST['first_name']),
        'last_name'  => esc($_POST['last_name']),
        'email'      => esc($_POST['email']),
        'phone'      => esc($_POST['phone'] ?? ''),
        'password'   => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'role'       => $_POST['role'],
      ]);

      $_SESSION['success'] = 'User created successfully.';
      header('Location: ' . ROOT . '/admin/users');
      exit();
    }

    $this->view('admin/add-user', [
      'roles' => $this->adminModel->getRoles(),
    ]);
  }

  public function edit_user($id)
  {
    $user = $this->adminModel->getUserById($id);

    if (!$user) {
      header('Location: ' . ROOT . '/admin/users');
      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $errors = [];

      if (empty($_POST['first_name']))
        $errors[] = 'First name is required.';

      if (empty($_POST['last_name']))
        $errors[] = 'Last name is required.';

      if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'A valid email is required.';
      elseif (!str_ends_with(strtolower($_POST['email']), '@mysess.com'))
        $errors[] = 'Email must end with @mysess.com';
      elseif ($this->adminModel->emailExists($_POST['email'], $id))
        $errors[] = 'Email is already taken by another user.';

      if (empty($_POST['role']) || !in_array($_POST['role'], $this->adminModel->getRoles()))
        $errors[] = 'Please select a valid role.';

      if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6)
          $errors[] = 'New password must be at least 6 characters.';
        elseif ($_POST['password'] !== $_POST['password_confirm'])
          $errors[] = 'Passwords do not match.';
      }

      if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = $_POST;
        header('Location: ' . ROOT . '/admin/edit_user/' . $id);
        exit();
      }

      $this->adminModel->updateUser($id, [
        'first_name' => esc($_POST['first_name']),
        'last_name'  => esc($_POST['last_name']),
        'email'      => esc($_POST['email']),
        'phone'      => esc($_POST['phone'] ?? ''),
        'role'       => $_POST['role'],
      ]);

      if (!empty($_POST['password'])) {
        $this->adminModel->updateUserPassword($id, $_POST['password']);
      }

      $_SESSION['success'] = 'User updated successfully.';
      header('Location: ' . ROOT . '/admin/view_user/' . $id);
      exit();
    }

    $this->view('admin/edit-user', [
      'user'  => $user,
      'roles' => $this->adminModel->getRoles(),
    ]);
  }

  public function deactivate_user()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = (int)$_POST['user_id'];

      // Prevent admin from deactivating themselves
      if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['error'] = 'You cannot deactivate your own account.';
        header('Location: ' . ROOT . '/admin/users');
        exit();
      }

      $this->adminModel->deactivateUser($id);
      $_SESSION['success'] = 'User deactivated successfully.';
    }

    header('Location: ' . ROOT . '/admin/users');
    exit();
  }

  public function activate_user()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = (int)$_POST['user_id'];
      $this->adminModel->activateUser($id);
      $_SESSION['success'] = 'User activated successfully.';
    }

    header('Location: ' . ROOT . '/admin/users');
    exit();
  }

  public function sessions()
  {
    $classroomSessions = $this->classroomSessionModel->query(
      "SELECT cs.*, s.first_name AS student_first_name, s.last_name AS student_last_name,
              u.first_name AS teacher_first_name, u.last_name AS teacher_last_name
         FROM classroom_sessions cs
         JOIN students s ON cs.student_id = s.id
         LEFT JOIN users u ON cs.teacher_id = u.id
        ORDER BY cs.session_date DESC, cs.created_at DESC
        LIMIT 100",
      []
    ) ?: [];

    $therapySessions = $this->therapySessionModel->query(
      "SELECT ts.*, s.first_name AS student_first_name, s.last_name AS student_last_name,
              u.first_name AS therapist_first_name, u.last_name AS therapist_last_name,
              ig.goal_text AS goal_addressed
         FROM therapy_sessions ts
         JOIN students s ON ts.student_id = s.id
         LEFT JOIN users u ON ts.therapist_id = u.id
         LEFT JOIN iep_goals ig ON ts.goal_addressed_id = ig.id
        ORDER BY ts.session_date DESC, ts.created_at DESC
        LIMIT 100",
      []
    ) ?: [];

    $this->view('admin/sessions', [
      'classroomSessions' => $classroomSessions,
      'therapySessions'   => $therapySessions,
    ]);
  }

  public function students()
  {
    $showArchived = isset($_GET['archived']);
    $students     = $this->adminModel->getAllStudents(!$showArchived);

    $this->view('admin/students', [
      'students' => $students ?: [],
    ]);
  }

  public function add_student()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->adminModel->createStudent([
        'first_name'      => esc($_POST['first_name']),
        'last_name'       => esc($_POST['last_name']),
        'date_of_birth'   => $_POST['date_of_birth'],
        'gender'          => $_POST['gender'],
        'diagnosis'       => $this->resolveDiagnosis(),
        'is_boarding'     => !empty($_POST['is_boarding']) ? 1 : 0,
        'enrollment_date' => $_POST['enrollment_date'],
        'guardian_id'     => !empty($_POST['guardian_id']) ? (int)$_POST['guardian_id'] : null,
      ]);

      $_SESSION['success'] = 'Student created successfully.';
      header('Location: ' . ROOT . '/admin/students');
      exit();
    }

    $this->view('admin/add-student', [
      'parents' => $this->adminModel->getAllParents() ?: [],
    ]);
  }

  // Combine the diagnosis dropdown and the "other" text box into one value
  private function resolveDiagnosis()
  {
    $select = $_POST['diagnosis_select'] ?? '';
    if ($select === '__other__') {
      return esc(trim($_POST['diagnosis_other'] ?? ''));
    }
    return esc(trim($select));
  }

  public function edit_student($id)
  {
    $student = $this->adminModel->getStudentById($id);

    if (!$student) {
      header('Location: ' . ROOT . '/admin/students');
      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->adminModel->updateStudent($id, [
        'first_name'      => esc($_POST['first_name']),
        'last_name'       => esc($_POST['last_name']),
        'date_of_birth'   => $_POST['date_of_birth'],
        'gender'          => $_POST['gender'],
        'diagnosis'       => $this->resolveDiagnosis(),
        'enrollment_date' => $_POST['enrollment_date'],
        'guardian_id'     => !empty($_POST['guardian_id']) ? (int)$_POST['guardian_id'] : null,
      ]);

      $_SESSION['success'] = 'Student updated successfully.';
      header('Location: ' . ROOT . '/admin/students');
      exit();
    }

    $this->view('admin/edit-student', [
      'student' => $student,
      'parents' => $this->adminModel->getAllParents() ?: [],
    ]);
  }

  public function archive_student()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->adminModel->archiveStudent((int)$_POST['student_id']);
      $_SESSION['success'] = 'Student archived successfully.';
    }

    header('Location: ' . ROOT . '/admin/students');
    exit();
  }

  public function restore_student()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->adminModel->restoreStudent((int)$_POST['student_id']);
      $_SESSION['success'] = 'Student restored successfully.';
    }

    header('Location: ' . ROOT . '/admin/students');
    exit();
  }

  public function assign_students()
  {
    $nurses        = $this->adminModel->getStaffByRole('nurse')          ?: [];
    $teachers      = $this->adminModel->getStaffByRole('teacher')        ?: [];
    $therapists    = $this->adminModel->getStaffByRole('therapist')      ?: [];
    $boardingStaff = $this->adminModel->getStaffByRole('boarding_staff') ?: [];
    $students         = $this->adminModel->getAllStudents()         ?: [];
    $boardingStudents = $this->adminModel->getBoardingStudentsList() ?: [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $action     = $_POST['action']     ?? '';
      $student_id = (int)($_POST['student_id'] ?? 0);
      $user_id    = (int)($_POST['user_id'] ?? 0);
      $role       = $_POST['role'] ?? '';

      if ($action === 'assign') {
        $this->assignOne($student_id, $user_id, $role);
        $_SESSION['success'] = 'Student assigned successfully.';

      } elseif ($action === 'remove') {
        if ($role === 'nurse') {
          $this->adminModel->removeNurseAssignment($student_id, $user_id);
        } else {
          $this->adminModel->removeStaffAssignment($student_id, $user_id, $role);
        }
        $_SESSION['success'] = 'Assignment removed successfully.';

      } elseif ($action === 'assign_by_diagnosis') {
        $diagnosis = $_POST['diagnosis'] ?? '';
        $onlyBoarding = ($role === 'boarding_staff');
        $matches = $this->adminModel->getStudentsByDiagnosisFilter($diagnosis, $onlyBoarding) ?: [];
        $count = 0;
        foreach ($matches as $m) {
          $this->assignOne((int)$m->id, $user_id, $role);
          $count++;
        }
        $_SESSION['success'] = $count . ' student' . ($count !== 1 ? 's' : '') . ' assigned.';
      }

      header('Location: ' . ROOT . '/admin/assign_students');
      exit();
    }

    $assignments = [];
    foreach (array_merge($nurses, $teachers, $therapists, $boardingStaff) as $staff) {
      $assignments[$staff->id] = $this->adminModel->getStudentsForStaff($staff->id, $staff->role);
    }

    $this->view('admin/assign-students', [
      'nurses'           => $nurses,
      'teachers'         => $teachers,
      'therapists'       => $therapists,
      'boardingStaff'    => $boardingStaff,
      'students'         => $students,
      'boardingStudents' => $boardingStudents,
      'assignments'      => $assignments,
      'diagnoses'        => $this->adminModel->getDistinctDiagnoses() ?: [],
    ]);
  }

  // Assign one student to one staff member, routing nurses to nurse_student
  private function assignOne($studentId, $userId, $role)
  {
    if (!$studentId || !$userId) {
      return;
    }
    if ($role === 'nurse') {
      $this->adminModel->assignStudentToNurse($studentId, $userId);
    } else {
      $this->adminModel->assignStudentToStaff($studentId, $userId, $role);
    }
  }

  // The fixed set of categories a goal (bank entry or student goal) may use
  private function goalCategories()
  {
    return [
      'Communication' => 'Communication',
      'Social'        => 'Social',
      'Motor'         => 'Motor',
      'Academic'      => 'Academic',
      'Behavioral'    => 'Behavioral',
      'Daily Living'  => 'Daily Living',
    ];
  }

  //list every bank entry
  public function goal_bank()
  {
    $entries = $this->goalBankModel->getAll();

    $this->view('admin/goal-bank', [
      'entries' => $entries ?: [],
    ]);
  }

  public function add_goal_bank()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $category = $_POST['category'] ?? '';
      $goalText = trim($_POST['goal_text'] ?? '');

      // Category must be one of our fixed values
      if (!array_key_exists($category, $this->goalCategories())) {
        $_SESSION['error'] = 'Please choose a valid category.';
        header('Location: ' . ROOT . '/admin/add-goal-bank');
        exit();
      }
      if ($goalText === '') {
        $_SESSION['error'] = 'Goal text is required.';
        header('Location: ' . ROOT . '/admin/add-goal-bank');
        exit();
      }

      $this->goalBankModel->addEntry($category, esc($goalText), $_SESSION['user_id']);

      $_SESSION['success'] = 'Goal bank entry added.';
      header('Location: ' . ROOT . '/admin/goal-bank');
      exit();
    }

    $this->view('admin/add-goal-bank', [
      'categories' => $this->goalCategories(),
    ]);
  }

  public function edit_goal_bank($id)
  {
    $entry = $this->goalBankModel->first(['id' => (int)$id]);

    if (!$entry) {
      header('Location: ' . ROOT . '/admin/goal-bank');
      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $category = $_POST['category'] ?? '';
      $goalText = trim($_POST['goal_text'] ?? '');

      if (!array_key_exists($category, $this->goalCategories())) {
        $_SESSION['error'] = 'Please choose a valid category.';
        header('Location: ' . ROOT . '/admin/edit-goal-bank/' . (int)$id);
        exit();
      }
      if ($goalText === '') {
        $_SESSION['error'] = 'Goal text is required.';
        header('Location: ' . ROOT . '/admin/edit-goal-bank/' . (int)$id);
        exit();
      }

      $this->goalBankModel->updateEntry((int)$id, $category, esc($goalText));

      $_SESSION['success'] = 'Goal bank entry updated.';
      header('Location: ' . ROOT . '/admin/goal-bank');
      exit();
    }

    $this->view('admin/edit-goal-bank', [
      'entry'      => $entry,
      'categories' => $this->goalCategories(),
    ]);
  }

  public function toggle_goal_bank()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id       = (int)$_POST['id'];
      $isActive = (int)$_POST['is_active'];
      $this->goalBankModel->setActive($id, $isActive);
      $_SESSION['success'] = 'Goal bank entry updated.';
    }

    header('Location: ' . ROOT . '/admin/goal-bank');
    exit();
  }

  // The fixed set of categories a TEACCH task (bank entry) may use
  private function teacchCategories()
  {
    return [
      'Self-Care'         => 'Self-Care',
      'Daily Living'      => 'Daily Living',
      'Classroom Routine' => 'Classroom Routine',
      'Play/Leisure'      => 'Play/Leisure',
      'Vocational'        => 'Vocational',
      'Communication'     => 'Communication',
    ];
  }

  //list every TEACCH task bank entry
  public function teacch_tasks()
  {
    $entries = $this->taskBankModel->getAll();

    $this->view('admin/teacch-tasks', [
      'entries' => $entries ?: [],
    ]);
  }

  public function add_teacch_task()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $category = $_POST['category'] ?? '';
      $title    = trim($_POST['title'] ?? '');

      // Category must be one of our fixed values
      if (!array_key_exists($category, $this->teacchCategories())) {
        $_SESSION['error'] = 'Please choose a valid category.';
        header('Location: ' . ROOT . '/admin/add-teacch-task');
        exit();
      }
      if ($title === '') {
        $_SESSION['error'] = 'Task title is required.';
        header('Location: ' . ROOT . '/admin/add-teacch-task');
        exit();
      }

      $this->taskBankModel->addEntry($category, esc($title), $_SESSION['user_id']);

      $_SESSION['success'] = 'TEACCH task added.';
      header('Location: ' . ROOT . '/admin/teacch-tasks');
      exit();
    }

    $this->view('admin/add-teacch-task', [
      'categories' => $this->teacchCategories(),
    ]);
  }

  public function edit_teacch_task($id)
  {
    $entry = $this->taskBankModel->first(['id' => (int)$id]);

    if (!$entry) {
      header('Location: ' . ROOT . '/admin/teacch-tasks');
      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $category = $_POST['category'] ?? '';
      $title    = trim($_POST['title'] ?? '');

      if (!array_key_exists($category, $this->teacchCategories())) {
        $_SESSION['error'] = 'Please choose a valid category.';
        header('Location: ' . ROOT . '/admin/edit-teacch-task/' . (int)$id);
        exit();
      }
      if ($title === '') {
        $_SESSION['error'] = 'Task title is required.';
        header('Location: ' . ROOT . '/admin/edit-teacch-task/' . (int)$id);
        exit();
      }

      $this->taskBankModel->updateEntry((int)$id, $category, esc($title));

      $_SESSION['success'] = 'TEACCH task updated.';
      header('Location: ' . ROOT . '/admin/teacch-tasks');
      exit();
    }

    $this->view('admin/edit-teacch-task', [
      'entry'      => $entry,
      'categories' => $this->teacchCategories(),
    ]);
  }

  //turn an entry on or off
  public function toggle_teacch_task()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id       = (int)$_POST['id'];
      $isActive = (int)$_POST['is_active'];
      $this->taskBankModel->setActive($id, $isActive);
      $_SESSION['success'] = 'TEACCH task updated.';
    }

    header('Location: ' . ROOT . '/admin/teacch-tasks');
    exit();
  }

  public function view_user($id = null)
  {
    if (!$id) {
      header('Location: ' . ROOT . '/admin/users');
      exit();
    }

    $user = $this->adminModel->getUserById($id);

    if (!$user) {
      header('Location: ' . ROOT . '/admin/users');
      exit();
    }

    $assignedStudents = $this->adminModel->getAssignedStudentsForUser($id, $user->role);

    $this->view('admin/view-user', [
      'user'             => $user,
      'assignedStudents' => $assignedStudents ?: [],
    ]);
  }

  public function view_student($student_id = null)
  {
    if (!$student_id) {
      header('Location: ' . ROOT . '/admin/students');
      exit();
    }

    $student = $this->adminModel->getStudentById($student_id);

    if (!$student) {
      header('Location: ' . ROOT . '/admin/students');
      exit();
    }

    $this->view('admin/view-student', [
      'student'        => $student,
      'medications'    => $this->medicationModel->where(['student_id' => $student_id, 'is_active' => 1])   ?: [],
      'medLogs'        => $this->medLogModel->getLogsForStudent($student_id)                                ?: [],
      'healthEvents'   => $this->healthEventModel->where(['student_id' => $student_id])                    ?: [],
      'healthRecords'  => $this->healthRecordModel->where(['student_id' => $student_id])                   ?: [],
      'iepGoals'       => $this->adminModel->getIepGoalsForStudent($student_id)                            ?: [],
      'goalProgress'   => $this->adminModel->getGoalProgressForStudent($student_id)                        ?: [],
      'sessions'       => $this->adminModel->getSessionsForStudent($student_id)                            ?: [],
      'teacchProgress' => $this->adminModel->getTeacchProgressForStudent($student_id)                      ?: [],
      'assignedStaff'  => $this->adminModel->getAssignedStaffForStudent($student_id)                       ?: [],
    ]);
  }

  public function add_health_record()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $student_id = (int)$_POST['student_id'];

      $this->healthRecordModel->insert([
        'student_id'  => $student_id,
        'record_type' => $_POST['record_type'],
        'title'       => esc($_POST['title']),
        'description' => esc($_POST['description'] ?? ''),
        'recorded_by' => $_SESSION['user_id'],
        'recorded_at' => $_POST['recorded_at'] ?? date('Y-m-d'),
      ]);

      $_SESSION['success'] = 'Health record added successfully.';
      header('Location: ' . ROOT . '/admin/view_student/' . $student_id);
      exit();
    }

    $student_id = (int)($_GET['student_id'] ?? 0);
    $student    = $student_id ? $this->adminModel->getStudentById($student_id) : null;

    if (!$student) {
      header('Location: ' . ROOT . '/admin/students');
      exit();
    }

    $this->view('admin/add_health_record', ['student' => $student]);
  }


  public function attendance()
  {
    $date    = $_GET['date'] ?? date('Y-m-d');
    $records = $this->checkinModel->getDailyPivot($date);
    $noteMap = $this->noteModel->getMapForDate($date);

    $this->view('admin/attendance', [
      'date'    => $date,
      'records' => $records ?: [],
      'noteMap' => $noteMap,
    ]);
  }


  public function reports()
  {
    $summary    = $this->adminModel->getReportSummary();
    $byDiag     = $this->adminModel->getStudentsByDiagnosis();
    $byGender   = $this->adminModel->getStudentsByGender();
    $goalsByCat = $this->adminModel->getGoalsByCategory();
    $recentProg = $this->adminModel->getRecentProgressScores(10);
    $topStaff   = $this->adminModel->getTopStaffBySessions(5);
    $monthly    = $this->adminModel->getMonthlySessions(6);

    $this->view('admin/reports', [
      'summary'    => $summary,
      'byDiag'     => $byDiag     ?: [],
      'byGender'   => $byGender   ?: [],
      'goalsByCat' => $goalsByCat ?: [],
      'recentProg' => $recentProg ?: [],
      'topStaff'   => $topStaff   ?: [],
      'monthly'    => $monthly    ?: [],
    ]);
  }


  public function student_report($studentId = null)
  {
    $studentId = (int)$studentId;
    $student   = $studentId ? $this->adminModel->getStudentById($studentId) : null;

    if (!$student) {
      header('Location: ' . ROOT . '/admin/students');
      exit();
    }

    $reportModel = new StudentReport();
    $reportData  = $reportModel->build($studentId);

    $this->view('admin/student-report', [
      'reportData'    => $reportData,
      'boardingStats' => !empty($student->is_boarding) ? $reportModel->boardingStats($studentId) : null,
      'preparedBy'    => $_SESSION['user_name'] ?? '',
    ]);
  }

  public function share_report()
  {
    $studentId = (int)($_POST['student_id'] ?? 0);
    if ($studentId) {
      $this->adminModel->shareReport($studentId, $_SESSION['user_id']);
      $_SESSION['success'] = 'Report shared with the parent.';
    }
    header('Location: ' . ROOT . '/admin/student_report/' . $studentId);
    exit();
  }

  public function student_reports()
  {
    $name      = $_GET['name'] ?? '';
    $diagnosis = $_GET['diagnosis'] ?? '';

    $students = $this->adminModel->searchStudents($name, $diagnosis) ?: [];

    $this->view('admin/student-reports', [
      'students'  => $students,
      'diagnoses' => $this->adminModel->getDistinctDiagnoses() ?: [],
      'name'      => $name,
      'diagnosis' => $diagnosis,
    ]);
  }

  public function share_all()
  {
    $ids = $_POST['student_ids'] ?? [];
    $count = 0;
    foreach ($ids as $sid) {
      $sid = (int)$sid;
      if ($sid) {
        $this->adminModel->shareReport($sid, $_SESSION['user_id']);
        $count++;
      }
    }
    $_SESSION['success'] = $count . ' report' . ($count !== 1 ? 's' : '') . ' shared with parents.';
    header('Location: ' . ROOT . '/admin/student_reports');
    exit();
  }

  public function print_reports()
  {
    $name      = $_GET['name'] ?? '';
    $diagnosis = $_GET['diagnosis'] ?? '';

    $students    = $this->adminModel->searchStudents($name, $diagnosis) ?: [];
    $reportModel = new StudentReport();

    $bundle = [];
    foreach ($students as $s) {
      $bundle[] = [
        'reportData'    => $reportModel->build((int)$s->id),
        'boardingStats' => !empty($s->is_boarding) ? $reportModel->boardingStats((int)$s->id) : null,
      ];
    }

    $this->view('admin/print-reports', [
      'bundle'     => $bundle,
      'preparedBy' => $_SESSION['user_name'] ?? '',
    ]);
  }
}
