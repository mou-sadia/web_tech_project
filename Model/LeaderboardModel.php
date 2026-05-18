<?php
include __DIR__ . "/../config/db.php";

class LeaderboardModel {
    
    private $conn;
    
    public function __construct(){
        global $conn;
        $this->conn = $conn;
    }

    public function getTopStudents($limit = 10){
        $sql = "SELECT u.id, u.name, SUM(COALESCE(at.score, 0)) as total_score, COUNT(at.id) as attempts
                FROM users u
                LEFT JOIN attempts at ON u.id = at.student_id AND at.completed_at IS NOT NULL
                WHERE u.role = 'student'
                GROUP BY u.id, u.name
                ORDER BY total_score DESC, u.name ASC
                LIMIT ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
}
?>
