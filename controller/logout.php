<?php
session_start();
session_destroy();
header("Location: /quiz_platform/view/login.php");
exit();
?>
