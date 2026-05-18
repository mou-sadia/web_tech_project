<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../../View/login.php");
    exit();
}

include "../../config/db.php";

$quizId = $_GET["id"] ?? 0;

if($quizId == 0) {
    header("Location: ../../View/instructor/dashboard.php?error=Invalid quiz ID");
    exit();
}

$instructorId = $_SESSION["user_id"];

$getQuestionsSql = "SELECT id FROM questions WHERE quiz_id = ?";
$stmt = mysqli_prepare($conn, $getQuestionsSql);
mysqli_stmt_bind_param($stmt, "i", $quizId);
mysqli_stmt_execute($stmt);
$questionsResult = mysqli_stmt_get_result($stmt);

while($question = mysqli_fetch_assoc($questionsResult)) {
    $questionId = $question['id'];
    
    $deleteOptionsSql = "DELETE FROM options WHERE question_id = ?";
    $stmt2 = mysqli_prepare($conn, $deleteOptionsSql);
    mysqli_stmt_bind_param($stmt2, "i", $questionId);
    mysqli_stmt_execute($stmt2);
}

$deleteQuestionsSql = "DELETE FROM questions WHERE quiz_id = ?";
$stmt3 = mysqli_prepare($conn, $deleteQuestionsSql);
mysqli_stmt_bind_param($stmt3, "i", $quizId);
mysqli_stmt_execute($stmt3);

$deleteQuizSql = "DELETE FROM quizzes WHERE id = ? AND instructor_id = ?";
$stmt4 = mysqli_prepare($conn, $deleteQuizSql);
mysqli_stmt_bind_param($stmt4, "ii", $quizId, $instructorId);
$result = mysqli_stmt_execute($stmt4);

if($result) {
    header("Location: ../../View/instructor/dashboard.php?success=deleted");
} else {
    header("Location: ../../View/instructor/dashboard.php?error=Failed to delete quiz");
}
exit();
?>