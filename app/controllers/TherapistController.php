<?php

class Therapist extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'therapist') {
            header('Location: ' . ROOT . '/auth/login');
            exit();
        }
    }

    public function index(...$params)
    {
        $this->view('therapist');
    }
}
