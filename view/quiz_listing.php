<?php
if(!isset($quizzes)) $quizzes = [];
if(!isset($attemptModel)){
    include __DIR__ . "/../Model/AttemptModel.php";
    $attemptModel = new AttemptModel();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Quizzes</title>
    <link rel="stylesheet" href="../view/css/touhid.css">
</head>
<body>
<h1>Published Quizzes</h1>

<?php if(count($quizzes) == 0): ?>
    <p>No published quizzes available at this time.</p>
<?php else: ?>
    <div class="quiz-grid">
    <?php foreach($quizzes as $quiz): ?>
        <div class="quiz-card">
            <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
            <p><?php echo htmlspecialchars(substr($quiz['description'] ?? '', 0, 120)); ?></p>
            <p><strong>Time Limit:</strong> <?php echo intval($quiz['time_limit_minutes']); ?> minutes</p>
            <p><strong>Total Marks:</strong> <?php echo intval($quiz['total_marks']); ?></p>
            <?php
                $attempted = $attemptModel->hasAttemptedQuiz($_SESSION['user_id'], $quiz['id']);
            ?>
            <?php if($attempted): ?>
                <a class="btn" href="../result.php?attempt_id=0">Already Attempted</a>
            <?php else: ?>
                <a class="btn start" href="../controller/QuizTakingController.php?quiz_id=<?php echo $quiz['id']; ?>">Start Quiz</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<br><a href="../view/student_home.php">← Back to Dashboard</a>

</body>
</html>
