<?php
include __DIR__ . "/../config/db.php";

class AttemptModel {

    private $conn;

    public function __construct(){
        global $conn;
        $this->conn = $conn;
    }

    public function hasAttemptedQuiz($studentId, $quizId){
        $sql = "SELECT COUNT(*) as cnt FROM attempts WHERE student_id = ? AND quiz_id = ? AND completed_at IS NOT NULL";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $studentId, $quizId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        return $row['cnt'] > 0;
    }

    public function createAttempt($studentId, $quizId){
        $sql = "INSERT INTO attempts (quiz_id, student_id, started_at) VALUES (?, ?, NOW())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quizId, $studentId);
        if(mysqli_stmt_execute($stmt)){
            return mysqli_insert_id($this->conn);
        }
        return false;
    }

    public function getActiveAttempt($studentId, $quizId){
        $sql = "SELECT * FROM attempts WHERE student_id = ? AND quiz_id = ? AND completed_at IS NULL ORDER BY id DESC LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $studentId, $quizId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($res);
    }

    public function saveAnswer($attemptId, $questionId, $optionId){
        $sql = "INSERT INTO answers (attempt_id, question_id, selected_option_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $attemptId, $questionId, $optionId);
        return mysqli_stmt_execute($stmt);
    }

    public function submitQuiz($attemptId){
        // calculate score
        $sql = "SELECT a.question_id, a.selected_option_id, o.is_correct, q.marks
                FROM answers a
                INNER JOIN options o ON a.selected_option_id = o.id
                INNER JOIN questions q ON a.question_id = q.id
                WHERE a.attempt_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $attemptId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $score = 0;
        while($row = mysqli_fetch_assoc($res)){
            if(intval($row['is_correct']) == 1){
                $score += intval($row['marks']);
            }
        }

        $update = "UPDATE attempts SET score = ?, completed_at = NOW() WHERE id = ?";
        $stmt2 = mysqli_prepare($this->conn, $update);
        mysqli_stmt_bind_param($stmt2, "ii", $score, $attemptId);
        mysqli_stmt_execute($stmt2);

        return $score;
    }

    public function getAttemptWithAnswers($attemptId){
        $out = [];

        $sql = "SELECT at.*, q.id as quiz_id, q.title, q.time_limit_minutes, q.total_marks
                FROM attempts at
                INNER JOIN quizzes q ON at.quiz_id = q.id
                WHERE at.id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $attemptId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $attempt = mysqli_fetch_assoc($res);
        if(!$attempt) return null;

        $out['attempt'] = $attempt;

        $sql2 = "SELECT a.*, o.option_text, o.is_correct, q.question_text, q.marks
                 FROM answers a
                 INNER JOIN options o ON a.selected_option_id = o.id
                 INNER JOIN questions q ON a.question_id = q.id
                 WHERE a.attempt_id = ?";
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "i", $attemptId);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);

        $out['answers'] = mysqli_fetch_all($res2, MYSQLI_ASSOC);

        return $out;
    }

    public function getStudentAttempts($studentId){
        $sql = "SELECT at.id, at.score, at.started_at, at.completed_at, q.title, q.total_marks
                FROM attempts at
                INNER JOIN quizzes q ON at.quiz_id = q.id
                WHERE at.student_id = ? AND at.completed_at IS NOT NULL
                ORDER BY at.completed_at DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $studentId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    public function getQuizAttempts($quizId){
        $sql = "SELECT at.id, at.score, at.started_at, at.completed_at, u.name
                FROM attempts at
                INNER JOIN users u ON at.student_id = u.id
                WHERE at.quiz_id = ? AND at.completed_at IS NOT NULL
                ORDER BY at.completed_at DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $quizId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    public function getQuizAnalytics($quizId){
        $sql = "SELECT AVG(score) as avg_score, MAX(score) as max_score, MIN(score) as min_score, COUNT(*) as total_attempts
                FROM attempts
                WHERE quiz_id = ? AND completed_at IS NOT NULL";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $quizId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($res);
    }
}
?>
