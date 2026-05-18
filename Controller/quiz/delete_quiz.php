<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../../View/login.php");
    exit();
}

include "../../Model/QuizModel.php";

$quizId = $_GET["id"] ?? 0;

$quizModel = new QuizModel();
$instructorId = $_SESSION["user_id"];

$result = $quizModel->deleteQuiz($quizId, $instructorId);

header("Location: ../../View/instructor/dashboard.php");
exit();
?>