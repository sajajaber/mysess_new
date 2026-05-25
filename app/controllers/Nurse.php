<?php
class Nurse extends Controller
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

    // /nurse or /nurse/index - Shows all students assigned to this nurse
    public function index()
    {
        $nurse_id = $_SESSION['user_id'];
        $students = $this->studentModel->getAssignedStudents($nurse_id);
        $this->view('nurse/index', ['students' => $students ?: []]);
    }

    // /nurse/dashboard - New dashboard with statistics
    public function dashboard()
    {
        $nurse_id = $_SESSION['user_id'];

        // Get all assigned students
        $students = $this->studentModel->getAssignedStudents($nurse_id);
        $totalStudents = count($students);

        // Get statistics
        $allergyCount = $this->healthRecordModel->getAllergyCountForNurse($nurse_id);
        $medicationCount = $this->medicationModel->getActiveMedicationsCountForNurse($nurse_id);

        // Get recent health records (last 10)
        $recentHealthRecords = $this->healthRecordModel->getRecentForNurse($nurse_id, 10);

        // Get recent health events
        $recentHealthEvents = $this->healthEventModel->getRecentForNurse($nurse_id, 5);

        $this->view('nurse/dashboard', [
            'totalStudents' => $totalStudents,
            'allergyCount' => $allergyCount,
            'medicationCount' => $medicationCount,
            'recentHealthRecords' => $recentHealthRecords,
            'recentHealthEvents' => $recentHealthEvents,
            'students' => $students
        ]);
    }

    // /nurse/all_medications - All medications view (replaces old medications.php)
    public function all_medications()
    {
        $nurse_id = $_SESSION['user_id'];

        // Get all medications for nurse's students
        $medications = $this->medicationModel->getMedicationsForNurse($nurse_id);

        // Get students for the add medication dropdown
        $students = $this->studentModel->getAssignedStudents($nurse_id);

        $this->view('nurse/all_medications', [
            'medications' => $medications,
            'students' => $students
        ]);
    }

    // /nurse/student/{id} - Full health profile for one student
    public function student($student_id)
    {
        $student = $this->studentModel->first(['id' => $student_id]);

        if (!$student) {
            header('Location: ' . ROOT . '/nurse');
            exit();
        }

        // Verify this nurse has access to this student
        $nurse_id = $_SESSION['user_id'];
        $assignedStudents = $this->studentModel->getAssignedStudents($nurse_id) ?: [];
        $assignedIds = array_map(function ($s) {
            return $s->id;
        }, $assignedStudents);

        if (!in_array($student_id, $assignedIds)) {
            header('Location: ' . ROOT . '/nurse');
            exit();
        }

        $medications   = $this->medicationModel->where(['student_id' => $student_id, 'is_active' => 1]);
        $medLogs       = $this->medLogModel->getLogsForStudent($student_id);
        $healthEvents  = $this->healthEventModel->where(['student_id' => $student_id]);
        $healthRecords = $this->healthRecordModel->where(['student_id' => $student_id]);

        $this->view('nurse/student', [
            'student'       => $student,
            'medications'   => $medications  ?: [],
            'medLogs'       => $medLogs      ?: [],
            'healthEvents'  => $healthEvents ?: [],
            'healthRecords' => $healthRecords ?: [],
        ]);
    }

    // /nurse/add_medication - Add medication
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

            if ($medicationId) {
                $_SESSION['medication_success'] = 'Medication added successfully';
            } else {
                $_SESSION['medication_error'] = 'Failed to add medication';
            }

            // Redirect back to referring page or all_medications
            $referer = $_SERVER['HTTP_REFERER'] ?? ROOT . '/nurse/all_medications';
            header('Location: ' . $referer);
            exit();
        }

        $student_id = (int)($_GET['student_id'] ?? 0);
        $student = $this->studentModel->first(['id' => $student_id]);
        $this->view('nurse/add_medication', ['student' => $student]);
    }

    // /nurse/log_dose - Log medication administration
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

        $med_id = (int)($_GET['med_id'] ?? 0);
        $medication = $this->medicationModel->first(['id' => $med_id]);
        $this->view('nurse/log_dose', ['medication' => $medication]);
    }

    // /nurse/deactivate_medication - Soft delete medication
    public function deactivate_medication()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $med_id = (int)$_POST['medication_id'];
            $student_id = (int)$_POST['student_id'];
            $this->medicationModel->update($med_id, ['is_active' => 0]);

            $_SESSION['medication_success'] = 'Medication deactivated successfully';
            header('Location: ' . ROOT . '/nurse/student/' . $student_id);
            exit();
        }
        header('Location: ' . ROOT . '/nurse');
        exit();
    }

    // /nurse/log_health_event - Log health event
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
        $student = $this->studentModel->first(['id' => $student_id]);
        $this->view('nurse/log_health_event', ['student' => $student]);
    }

    // /nurse/add_health_record - Add health record
    public function add_health_record()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->healthRecordModel->insert([
                'student_id'  => (int)$_POST['student_id'],
                'record_type' => $_POST['record_type'],
                'title'       => esc($_POST['title']),
                'description' => esc($_POST['description'] ?? ''),
                'recorded_by' => $_SESSION['user_id'],
            ]);
            header('Location: ' . ROOT . '/nurse/student/' . (int)$_POST['student_id']);
            exit();
        }

        $student_id = (int)($_GET['student_id'] ?? 0);
        $student = $this->studentModel->first(['id' => $student_id]);
        $this->view('nurse/add_health_record', ['student' => $student]);
    }
}
