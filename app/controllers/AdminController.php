<?php
class AdminController extends Controller
{
  private $adminModel;
  private $medicationModel;
  private $medLogModel;
  private $healthEventModel;
  private $healthRecordModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }
    $this->adminModel        = new Admin();
    $this->medicationModel   = new Medication();
    $this->medLogModel       = new MedicationLog();
    $this->healthEventModel  = new HealthEvent();
    $this->healthRecordModel = new HealthRecord();
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
        'diagnosis'       => esc($_POST['diagnosis'] ?? ''),
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
        'diagnosis'       => esc($_POST['diagnosis'] ?? ''),
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
    $nurses     = $this->adminModel->getStaffByRole('nurse')     ?: [];
    $teachers   = $this->adminModel->getStaffByRole('teacher')   ?: [];
    $therapists = $this->adminModel->getStaffByRole('therapist') ?: [];
    $students   = $this->adminModel->getAllStudents()             ?: [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $action     = $_POST['action']     ?? '';
      $student_id = (int)$_POST['student_id'];
      $user_id    = (int)$_POST['user_id'];
      $role       = $_POST['role'] ?? '';

      if ($action === 'assign') {
        if ($role === 'nurse') {
          $this->adminModel->assignStudentToNurse($student_id, $user_id);
        } else {
          $this->adminModel->assignStudentToStaff($student_id, $user_id, $role);
        }
        $_SESSION['success'] = 'Student assigned successfully.';
      } elseif ($action === 'remove') {
        if ($role === 'nurse') {
          $this->adminModel->removeNurseAssignment($student_id, $user_id);
        } else {
          $this->adminModel->removeStaffAssignment($student_id, $user_id, $role);
        }
        $_SESSION['success'] = 'Assignment removed successfully.';
      }

      header('Location: ' . ROOT . '/admin/assign_students');
      exit();
    }

    // Build assignment map: staff_id => [student, student, ...]
    // so the view can loop staff and show their students under them
    $assignments = [];
    foreach (array_merge($nurses, $teachers, $therapists) as $staff) {
      $assignments[$staff->id] = $this->adminModel->getStudentsForStaff($staff->id, $staff->role);
    }

    $this->view('admin/assign-students', [
      'nurses'      => $nurses,
      'teachers'    => $teachers,
      'therapists'  => $therapists,
      'students'    => $students,
      'assignments' => $assignments,
    ]);
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
}
