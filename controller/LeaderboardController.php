<?php
session_start();

include __DIR__ . "/../Model/LeaderboardModel.php";

$leaderboardModel = new LeaderboardModel();
$topStudents = $leaderboardModel->getTopStudents(10);

include __DIR__ . "/../view/leaderboard.php";
?>
