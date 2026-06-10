<?php
class ParentController extends Controller
{
  public function __construct()
  {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
      header('Location: ' . ROOT . '/auth/login');
      exit();
    }
  }

  public function index()
  {
    header('Location: ' . ROOT . '/parent/dashboard');
    exit();
  }

  public function dashboard()
  {
    $this->view('parent/dashboard', [
      'studentCount' => 1,
      'upcomingChecks' => 2,
      'activeGoals' => 3,
      'recentUpdates' => [
        ['Math', 'Teacher', 'Practice fractions and multiplication facts.'],
        ['Speech', 'Therapist', 'Continue breathing exercises at home.'],
      ],
    ]);
  }

  public function academic_records()
  {
    $this->view('parent/academic-records', [
      'studentName' => 'Student Name',
      'academicNotes' => [
        ['2026-06-02', 'Reading', 'Ms. Carter', 'Improved fluency during guided reading.'],
        ['2026-06-05', 'Math', 'Mr. Lee', 'Completed subtraction review with confidence.'],
      ],
    ]);
  }

  public function health_records()
  {
    $this->view('parent/health-records', [
      'medicationCount' => 1,
      'checkInCount' => 2,
      'supportNotes' => [
        ['2026-06-03', 'Wellness', 'Hydration reminder after PE.'],
        ['2026-06-07', 'Medication', 'Evening dose recorded by school nurse.'],
      ],
    ]);
  }

  public function academicRecords()
  {
    $this->academic_records();
  }

  public function healthRecords()
  {
    $this->health_records();
  }
}