<?php
include __DIR__ . "/../Model/AttemptModel.php";

$attemptId = intval($_GET['attempt_id'] ?? 0);
if(!$attemptId){
    echo "No attempt specified";
    exit();
}

$am = new AttemptModel();
$data = $am->getAttemptWithAnswers($attemptId);
if(!$data){
    echo "Attempt not found";
    exit();
}

$attempt = $data['attempt'];
$answers = $data['answers'];
$score = intval($attempt['score']);
$total = intval($attempt['total_marks']);
$percentage = $total > 0 ? round(($score / $total) * 100) : 0;
$passed = $percentage >= 60;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Result</title>
    <link rel="stylesheet" href="view/css/touhid.css">
</head>
<body>
    <h1>Quiz Result</h1>
    <div class="result-header">
        <p>Quiz: <?php echo htmlspecialchars($attempt['title']); ?></p>
        <div class="result-banner <?php echo $passed ? 'pass' : 'fail'; ?>">
            <?php echo $passed ? 'PASS' : 'FAIL'; ?>
        </div>
    </div>
    
    <div class="score-display">
        <p>Score: <strong><?php echo $score; ?> / <?php echo $total; ?></strong></p>
        <p>Percentage: <strong><?php echo $percentage; ?>%</strong></p>
    </div>

    <h2>Answer Breakdown</h2>
    <table class="answer-table">
        <thead>
            <tr>
                <th>Question</th>
                <th>Your Answer</th>
                <th>Correct Answer</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($answers as $ans): ?>
            <tr class="<?php echo $ans['is_correct'] ? 'correct' : 'wrong'; ?>">
                <td><?php echo htmlspecialchars($ans['question_text']); ?></td>
                <td><?php echo htmlspecialchars($ans['option_text']); ?></td>
                <td><?php 
                    // Find correct answer from same question
                    $correct = array_filter($answers, function($a) use ($ans) {
                        return $a['question_id'] == $ans['question_id'] && $a['is_correct'] == 1;
                    });
                    if(count($correct) > 0){
                        echo htmlspecialchars(reset($correct)['option_text']);
                    } else {
                        echo "N/A";
                    }
                ?></td>
                <td class="<?php echo $ans['is_correct'] ? 'correct-text' : 'wrong-text'; ?>">
                    <?php echo $ans['is_correct'] ? '✓ Correct' : '✗ Wrong'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="result-actions">
        <a href="controller/QuizListingController.php" class="btn">Back to Quizzes</a>
        <a href="controller/StudentResultsController.php" class="btn">My Results</a>
    </div>

</body>
</html>
