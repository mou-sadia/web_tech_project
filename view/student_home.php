<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: /quiz_platform/view/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="/quiz_platform/view/css/student.css">
</head>
<body>

<div class="dashboard">

    <button class="logout"
        onclick="window.location.href='/quiz_platform/controller/logout.php'">
        Logout
    </button>

    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
    <p>Role: <strong>Student</strong></p>

    <hr>

    <div class="box">
        <h3>Quizzes Available</h3>
        <p>0 published quizzes</p>
    </div>

    <div class="box">
        <h3>Attempts Taken</h3>
        <p>0 attempts</p>
    </div>

    <div class="box">
        <h3>Total Score Earned</h3>
        <p>0 points</p>
    </div>

</div>

</body>
</html>
