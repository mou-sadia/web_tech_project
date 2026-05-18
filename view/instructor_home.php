<?php
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: login.php");
    exit();
}

// Redirect to your Task 2 dashboard
header("Location: ../view/instructor/dashboard.php");
exit();
?>