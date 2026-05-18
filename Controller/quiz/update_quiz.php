<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../../View/login.php");
    exit();
}

include "../../Model/QuizModel.php";

$quizId = $_POST["quiz_id"] ?? 0;
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
    header("Location: ../../View/instructor/edit_quiz.php?id=" . $quizId);
    exit();
}

$quizModel = new QuizModel();
$instructorId = $_SESSION["user_id"];

$result = $quizModel->updateQuiz($quizId, $instructorId, $title, $description, $timeLimit, $status);

if($result) {
    header("Location: ../../View/instructor/dashboard.php?success=updated");
} else {
    $_SESSION["generalError"] = "Failed to update quiz";
    header("Location: ../../View/instructor/edit_quiz.php?id=" . $quizId);
}
?>