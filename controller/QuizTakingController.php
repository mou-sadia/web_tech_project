<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: ../view/login.php");
    exit();
}

include __DIR__ . "/../Model/AttemptModel.php";
include __DIR__ . "/../Model/QuizModel.php";

$studentId = $_SESSION['user_id'];
$quizId = intval($_GET['quiz_id'] ?? 0);

if(!$quizId){
    header("Location: ../view/student_home.php");
    exit();
}

$attemptModel = new AttemptModel();
$quizModel = new QuizModel();

// prevent re-attempt if completed
if($attemptModel->hasAttemptedQuiz($studentId, $quizId)){
    header("Location: ../view/result.php?message=" . urlencode('You already attempted this quiz'));
    exit();
}

$active = $attemptModel->getActiveAttempt($studentId, $quizId);
if($active){
    $attemptId = $active['id'];
} else {
    $attemptId = $attemptModel->createAttempt($studentId, $quizId);
}

if(!$attemptId){
    header("Location: ../view/student_home.php?error=Could not start attempt");
    exit();
}

// load view
$quiz = $quizModel->getQuizWithQuestions($quizId);
include __DIR__ . "/../view/quiz_taking.php";
?>
