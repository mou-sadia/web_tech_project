<?php 
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$quizId = $input['quiz_id'] ?? 0;

if(!$quizId) {
    echo json_encode(['success' => false, 'error' => 'Quiz ID required']);
    exit();
}

include "../../Model/QuizModel.php";
include "../../Model/QuestionModel.php";

$quizModel = new QuizModel();
$questionModel = new QuestionModel();
$instructorId = $_SESSION["user_id"];

// Get current quiz
$result = $quizModel->getQuizById($quizId, $instructorId);
$quiz = mysqli_fetch_assoc($result);

if(!$quiz) {
    echo json_encode(['success' => false, 'error' => 'Quiz not found']);
    exit();
}

// If trying to publish, check if has questions
if($quiz['status'] == 'draft') {
    $hasQuestions = $questionModel->hasQuestions($quizId, $instructorId);
    if(!$hasQuestions) {
        echo json_encode(['success' => false, 'error' => 'Cannot publish: Add at least one question first']);
        exit();
    }
}

$newStatus = $quizModel->toggleStatus($quizId, $instructorId);

if($newStatus) {
    echo json_encode(['success' => true, 'new_status' => $newStatus]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update status']);
}
?>