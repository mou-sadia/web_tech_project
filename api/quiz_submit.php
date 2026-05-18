<?php
header('Content-Type: application/json');
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if(!$input || !isset($input['attempt_id']) || !isset($input['answers'])){
    echo json_encode(['success' => false, 'error' => 'Invalid payload']);
    exit();
}

$attemptId = intval($input['attempt_id']);
$answers = $input['answers']; // expected format: [{question_id:..., option_id:...}, ...]

include __DIR__ . "/../Model/AttemptModel.php";

$attemptModel = new AttemptModel();

// Save answers
foreach($answers as $a){
    $q = intval($a['question_id'] ?? 0);
    $o = intval($a['option_id'] ?? 0);
    if($q && $o){
        $attemptModel->saveAnswer($attemptId, $q, $o);
    }
}

$score = $attemptModel->submitQuiz($attemptId);

echo json_encode(['success' => true, 'score' => $score, 'attempt_id' => $attemptId]);
exit();
?>
