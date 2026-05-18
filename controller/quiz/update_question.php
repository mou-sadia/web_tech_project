<?php 
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$questionId = $input['question_id'] ?? 0;
$quizId = $input['quiz_id'] ?? 0;
$questionText = trim($input['question_text'] ?? "");
$correctOptionId = $input['correct_option_id'] ?? null;

if(!$questionId || !$quizId || empty($questionText)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

include "../../Model/QuestionModel.php";

$questionModel = new QuestionModel();
$instructorId = $_SESSION["user_id"];

$result = $questionModel->updateQuestionText($questionId, $quizId, $instructorId, $questionText);

if($result) {
    if($correctOptionId) {
        $questionModel->updateCorrectOption($questionId, $quizId, $instructorId, $correctOptionId);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update question']);
}
?>