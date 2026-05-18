<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: ../view/login.php");
    exit();
}

include __DIR__ . "/../Model/AttemptModel.php";

$studentId = $_SESSION['user_id'];
$attemptModel = new AttemptModel();
$attempts = $attemptModel->getStudentAttempts($studentId);

include __DIR__ . "/../view/student_results.php";
?>
