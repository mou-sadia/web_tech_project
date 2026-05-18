<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../../view/login.php");
    exit();
}

include "../../config/db.php";

$quizId = $_GET["id"] ?? 0;

if($quizId == 0) {
    header("Location: ../../view/instructor/dashboard.php?error=Invalid quiz ID");
    exit();
}

$instructorId = $_SESSION["user_id"];

mysqli_begin_transaction($conn);

$deleteAnswersSql = "DELETE a FROM answers a INNER JOIN attempts at ON a.attempt_id = at.id WHERE at.quiz_id = ?";
$stmt1 = mysqli_prepare($conn, $deleteAnswersSql);
mysqli_stmt_bind_param($stmt1, "i", $quizId);
$answersDeleted = mysqli_stmt_execute($stmt1);

$deleteAttemptsSql = "DELETE FROM attempts WHERE quiz_id = ?";
$stmt2 = mysqli_prepare($conn, $deleteAttemptsSql);
mysqli_stmt_bind_param($stmt2, "i", $quizId);
$attemptsDeleted = mysqli_stmt_execute($stmt2);

$deleteOptionsSql = "DELETE o FROM options o INNER JOIN questions q ON o.question_id = q.id WHERE q.quiz_id = ?";
$stmt3 = mysqli_prepare($conn, $deleteOptionsSql);
mysqli_stmt_bind_param($stmt3, "i", $quizId);
$optionsDeleted = mysqli_stmt_execute($stmt3);

$deleteQuestionsSql = "DELETE FROM questions WHERE quiz_id = ?";
$stmt4 = mysqli_prepare($conn, $deleteQuestionsSql);
mysqli_stmt_bind_param($stmt4, "i", $quizId);
$questionsDeleted = mysqli_stmt_execute($stmt4);

$deleteQuizSql = "DELETE FROM quizzes WHERE id = ? AND instructor_id = ?";
$stmt5 = mysqli_prepare($conn, $deleteQuizSql);
mysqli_stmt_bind_param($stmt5, "ii", $quizId, $instructorId);
$quizDeleted = mysqli_stmt_execute($stmt5);

if($answersDeleted && $attemptsDeleted && $optionsDeleted && $questionsDeleted && $quizDeleted) {
    mysqli_commit($conn);
    header("Location: ../../view/instructor/dashboard.php?success=deleted");
} else {
    mysqli_rollback($conn);
    header("Location: ../../view/instructor/dashboard.php?error=Failed to delete quiz");
}
exit();
?>