<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor'){
    header("Location: ../view/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="../view/css/instructor.css">
</head>
<body>

<div class="dashboard">

    <button class="logout"
        onclick="window.location.href='../controller/logout.php'">
        Logout
    </button>

    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
    <p>Role: <strong>Instructor</strong></p>

    <hr>

    <div class="box">
        <h3>Quizzes Created</h3>
        <p>0 quizzes</p>
    </div>

    <div class="box">
        <h3>Total Attempts Across My Quizzes</h3>
        <p>0 attempts</p>
    </div>

</div>

</body>
</html>
