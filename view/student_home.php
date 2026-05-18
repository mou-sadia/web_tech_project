<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: ../view/login.php");

    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Dashboard</title>

    <link rel="stylesheet" href="../view/css/student.css">

</head>
<body>

<div class="dashboard">

    <button class="logout"
        onclick="window.location.href='../controller/logout.php'">
        Logout
    </button>

    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
    <p>Role: <strong>Student</strong></p>

    <hr>

    <div class="box">
        <h3>Quizzes Available</h3>
        <?php 
            include __DIR__ . "/../Model/QuizModel.php";
            $quizModel = new QuizModel();
            $publishedQuizzes = $quizModel->getPublishedQuizzes();
            $quizCount = count($publishedQuizzes);
        ?>
        <p><?php echo $quizCount; ?> published quizzes</p>
        <a href="../controller/QuizListingController.php" style="color: blue; text-decoration: underline;">View Quizzes →</a>
    </div>

    <div class="box">
        <h3>Attempts Taken</h3>
        <?php 
            include __DIR__ . "/../Model/AttemptModel.php";
            $attemptModel = new AttemptModel();
            $studentId = $_SESSION['user_id'];
            $studentAttempts = $attemptModel->getStudentAttempts($studentId);
            $attemptCount = count($studentAttempts);
            $totalScore = 0;
            foreach($studentAttempts as $att){ $totalScore += intval($att['score']); }
        ?>
        <p><?php echo $attemptCount; ?> attempts</p>
        <a href="../controller/StudentResultsController.php" style="color: blue; text-decoration: underline;">View My Results →</a>
    </div>

    <div class="box">
        <h3>Total Score Earned</h3>
        <p><?php echo $totalScore; ?> points</p>
    </div>

</div>

</body>
</html>
