<?php
session_start();

if(isset($_SESSION['user_id'])){
    $role = $_SESSION['role'];

    if($role == 'student'){
        header("Location: view/student_home.php");
    }else if($role == 'instructor'){
        header("Location: view/instructor_home.php");
    }else if($role == 'admin'){
        header("Location: view/admin_panel.php");
    }
    exit();
}

include "view/register.php";
?>