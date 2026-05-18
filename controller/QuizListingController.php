<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    // for testing, you may include test_login.php externally
    header("Location: ../view/login.php");
    exit();
}

include __DIR__ . "/../Model/QuizModel.php";
include __DIR__ . "/../Model/AttemptModel.php";

$quizModel = new QuizModel();
$attemptModel = new AttemptModel();

$quizzes = $quizModel->getPublishedQuizzes();

include __DIR__ . "/../view/quiz_listing.php";
?>
