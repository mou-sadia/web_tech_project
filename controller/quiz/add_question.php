<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../../view/login.php");
    exit();
}

include "../../Model/QuestionModel.php";

$quizId = $_POST["quiz_id"] ?? 0;
$questionText = trim($_POST["question_text"] ?? "");
$marks = $_POST["marks"] ?? 1;
$optionA = trim($_POST["option_a"] ?? "");
$optionB = trim($_POST["option_b"] ?? "");
$optionC = trim($_POST["option_c"] ?? "");
$optionD = trim($_POST["option_d"] ?? "");
$correctOption = $_POST["correct_option"] ?? 0;

$hasError = false;

if(empty($questionText)) {
    $error = "Question text is required";
    $hasError = true;
}

if(empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD)) {
    $error = "All four options are required";
    $hasError = true;
}

if($hasError) {
    header("Location: ../../view/instructor/manage_questions.php?quiz_id=" . $quizId . "&error=" . urlencode($error));
    exit();
}

$options = [$optionA, $optionB, $optionC, $optionD];

$questionModel = new QuestionModel();
$result = $questionModel->addQuestion($quizId, $questionText, $marks, $options, $correctOption);

if($result) {
    header("Location: ../../view/instructor/manage_questions.php?quiz_id=" . $quizId . "&success=added");
} else {
    header("Location: ../../view/instructor/manage_questions.php?quiz_id=" . $quizId . "&error=Failed to add question");
}
?>