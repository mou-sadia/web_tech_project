<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor'){
    header("Location: ../view/login.php");
    exit();
}

include __DIR__ . "/../Model/QuizModel.php";
include __DIR__ . "/../Model/AttemptModel.php";

$instructorId = $_SESSION['user_id'];
$quizModel = new QuizModel();
$attemptModel = new AttemptModel();

$quizzes = $quizModel->getQuizzesByInstructor($instructorId);
$quizzes = mysqli_fetch_all($quizzes, MYSQLI_ASSOC);

$quizId = intval($_GET['quiz_id'] ?? 0);
$attempts = [];
$analytics = [];

if($quizId){
    // verify ownership
    $result = $quizModel->getQuizById($quizId, $instructorId);
    if(mysqli_num_rows($result) > 0){
        $attempts = $attemptModel->getQuizAttempts($quizId);
        $analytics = $attemptModel->getQuizAnalytics($quizId);
    }
}

include __DIR__ . "/../view/instructor_analytics.php";
?>
