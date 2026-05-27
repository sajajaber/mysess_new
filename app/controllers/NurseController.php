<?php
class NurseController extends Controller
{
    private $medicationModel;
    private $medLogModel;
    private $healthEventModel;
    private $healthRecordModel;
    private $studentModel;

    public function __construct()
    {
        // Role guard — redirect anyone who isn't a logged-in nurse
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
            header('Location: ' . ROOT . '/auth/login');
            exit();
        }

        $this->medicationModel   = new Medication();
        $this->medLogModel       = new MedicationLog();
        $this->healthEventModel  = new HealthEvent();
        $this->healthRecordModel = new HealthRecord();
        $this->studentModel      = new Student();
    }

    public function requireLogin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
            header('Location: ' . ROOT . '/auth/login');
            exit();
        }
    }

    // student list
    public function students()
    {
        $nurse_id = $_SESSION['user_id'];
        $students = $this->studentModel->getAssignedStudents($nurse_id);
        $this->view('nurse/students', ['students' => $students ?: []]);
    }


    // /nurse/dashboard
    public function dashboard()
    {
        $nurse_id = $_SESSION['user_id'];

        $students        = $this->studentModel->getAssignedStudents($nurse_id);
        $totalStudents   = count($students ?: []);
        $medicationCount = $this->medicationModel->getActiveMedicationsCountForNurse($nurse_id);
        $studentsOnMeds  = $this->medicationModel->getStudentsWithActiveMedicationsCount($nurse_id);

        // Last 5 health records for the dashboard table
        $recentHealthRecords = $this->healthRecordModel->getRecentForNurse($nurse_id, 5);

        $this->view('nurse/dashboard', [
            'totalStudents'       => $totalStudents,
            'medicationCount'     => $medicationCount,
            'studentsOnMeds'      => $studentsOnMeds,
            'recentHealthRecords' => $recentHealthRecords ?: [],
            'students'            => $students ?: [],
        ]);
    }

    // /nurse/health-records
    public function health_records()
    {
        $nurse_id      = $_SESSION['user_id'];
        $healthRecords = $this->healthRecordModel->getRecentForNurse($nurse_id, 50);

        $this->view('nurse/health-records', [
            'healthRecords' => $healthRecords ?: [],
        ]);
    }

    // /nurse/all_medications
    public function all_medications()
    {
        $nurse_id    = $_SESSION['user_id'];
        $medications = $this->medicationModel->getMedicationsForNurse($nurse_id);
        $students    = $this->studentModel->getAssignedStudents($nurse_id);

        $this->view('nurse/all_medications', [
            'medications' => $medications ?: [],
            'students'    => $students    ?: [],
        ]);
    }

    // /nurse/student/{id}  — full health profile
    public function student($student_id = null)
    {
        if (!$student_id) {
            header('Location: ' . ROOT . '/nurse');
            exit();
        }

        $student = $this->studentModel->first(['id' => $student_id]);

        if (!$student) {
            header('Location: ' . ROOT . '/nurse');
            exit();
        }

        // Verify this nurse has access to this student
        $nurse_id         = $_SESSION['user_id'];
        $assignedStudents = $this->studentModel->getAssignedStudents($nurse_id) ?: [];
        $assignedIds      = array_map(fn($s) => (int)$s->id, $assignedStudents);

        if (!in_array((int)$student_id, $assignedIds)) {
            header('Location: ' . ROOT . '/nurse');
            exit();
        }

        $medications   = $this->medicationModel->where(['student_id' => $student_id, 'is_active' => 1]);
        $medLogs       = $this->medLogModel->getLogsForStudent($student_id);
        $healthEvents  = $this->healthEventModel->where(['student_id' => $student_id]);
        $healthRecords = $this->healthRecordModel->where(['student_id' => $student_id]);

        $this->view('nurse/display-student', [
            'student'       => $student,
            'medications'   => $medications   ?: [],
            'medLogs'       => $medLogs       ?: [],
            'healthEvents'  => $healthEvents  ?: [],
            'healthRecords' => $healthRecords ?: [],
        ]);
    }

    // /nurse/add_medication
    public function add_medication()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicationId = $this->medicationModel->insert([
                'student_id'   => (int)$_POST['student_id'],
                'name'         => esc($_POST['name']),
                'dosage'       => esc($_POST['dosage']),
                'frequency'    => esc($_POST['frequency']),
                'instructions' => esc($_POST['instructions'] ?? $_POST['notes'] ?? ''),
                'added_by'     => $_SESSION['user_id'],
                'is_active'    => 1,
            ]);

            $_SESSION[$medicationId ? 'medication_success' : 'medication_error'] =
                $medicationId ? 'Medication added successfully' : 'Failed to add medication';

            $referer = $_SERVER['HTTP_REFERER'] ?? ROOT . '/nurse/all_medications';
            header('Location: ' . $referer);
            exit();
        }

        $student_id = (int)($_GET['student_id'] ?? 0);
        $student    = $student_id ? $this->studentModel->first(['id' => $student_id]) : null;
        $this->view('nurse/add_medication', ['student' => $student]);
    }

    // /nurse/log_dose
    public function log_dose()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->medLogModel->insert([
                'medication_id'   => (int)$_POST['medication_id'],
                'administered_by' => $_SESSION['user_id'],
                'administered_at' => $_POST['administered_at'],
                'notes'           => esc($_POST['notes'] ?? ''),
            ]);
            header('Location: ' . ROOT . '/nurse/student/' . (int)$_POST['student_id']);
            exit();
        }

        $med_id     = (int)($_GET['med_id'] ?? 0);
        $medication = $this->medicationModel->first(['id' => $med_id]);
        $this->view('nurse/log_dose', ['medication' => $medication]);
    }

    // /nurse/deactivate_medication
    public function deactivate_medication()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $med_id     = (int)$_POST['medication_id'];
            $student_id = (int)$_POST['student_id'];
            $this->medicationModel->update($med_id, ['is_active' => 0]);

            $_SESSION['medication_success'] = 'Medication deactivated successfully';
            header('Location: ' . ROOT . '/nurse/student/' . $student_id);
            exit();
        }
        header('Location: ' . ROOT . '/nurse');
        exit();
    }

    // /nurse/log_health_event
    public function log_health_event()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->healthEventModel->insert([
                'student_id'   => (int)$_POST['student_id'],
                'description'  => esc($_POST['description']),
                'severity'     => $_POST['severity'],
                'action_taken' => esc($_POST['action_taken'] ?? ''),
                'recorded_by'  => $_SESSION['user_id'],
            ]);
            header('Location: ' . ROOT . '/nurse/student/' . (int)$_POST['student_id']);
            exit();
        }

        $student_id = (int)($_GET['student_id'] ?? 0);
        $student    = $this->studentModel->first(['id' => $student_id]);
        $this->view('nurse/log_health_event', ['student' => $student]);
    }

    // /nurse/add_health_record
    public function add_health_record()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->healthRecordModel->insert([
                'student_id'  => (int)$_POST['student_id'],
                'record_type' => $_POST['record_type'],
                'title'       => esc($_POST['title']),
                'description' => esc($_POST['description'] ?? ''),
                'recorded_by' => $_SESSION['user_id'],
                'recorded_at' => $_POST['recorded_at'] ?? date('Y-m-d'),
            ]);
            header('Location: ' . ROOT . '/nurse/student/' . (int)$_POST['student_id']);
            exit();
        }

        $nurse_id   = $_SESSION['user_id'];
        $student_id = (int)($_GET['student_id'] ?? 0);
        $student    = $student_id ? $this->studentModel->first(['id' => $student_id]) : null;
        $students   = $this->studentModel->getAssignedStudents($nurse_id);

        $this->view('nurse/add-health-record', [
            'student'  => $student,
            'students' => $students ?: [],
        ]);
    }
}
