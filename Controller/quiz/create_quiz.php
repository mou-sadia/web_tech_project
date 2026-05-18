<?php 
session_start();

// Check if logged in and is instructor
if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../../View/login.php");
    exit();
}

include "../../Model/QuizModel.php";

$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$timeLimit = trim($_POST["time_limit"] ?? "");
$status = $_POST["status"] ?? "draft";

$hasError = false;

if(empty($title)) {
    $_SESSION["titleError"] = "Title is required";
    $hasError = true;
}

if(empty($description)) {
    $_SESSION["descError"] = "Description is required";
    $hasError = true;
}

if(empty($timeLimit)) {
    $_SESSION["timeError"] = "Time limit is required";
    $hasError = true;
} elseif(!is_numeric($timeLimit) || $timeLimit < 1) {
    $_SESSION["timeError"] = "Time limit must be a positive number";
    $hasError = true;
}

if($hasError) {
    $_SESSION["old_title"] = $title;
    $_SESSION["old_desc"] = $description;
    $_SESSION["old_time"] = $timeLimit;
    header("Location: ../../View/instructor/create_quiz.php");
    exit();
}

$quizModel = new QuizModel();
$instructorId = $_SESSION["user_id"];

$quizId = $quizModel->createQuiz($instructorId, $title, $description, $timeLimit, $status);

if($quizId) {
    header("Location: ../../View/instructor/manage_questions.php?quiz_id=" . $quizId . "&success=created");
} else {
    $_SESSION["generalError"] = "Failed to create quiz";
    $_SESSION["old_title"] = $title;
    $_SESSION["old_desc"] = $description;
    $_SESSION["old_time"] = $timeLimit;
    header("Location: ../../View/instructor/create_quiz.php");
}
?>