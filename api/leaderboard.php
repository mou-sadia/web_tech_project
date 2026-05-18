<?php
header('Content-Type: application/json');

include __DIR__ . "/../Model/LeaderboardModel.php";

$leaderboardModel = new LeaderboardModel();
$topStudents = $leaderboardModel->getTopStudents(10);

echo json_encode(['success' => true, 'data' => $topStudents]);
?>
