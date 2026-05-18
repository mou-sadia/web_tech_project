<?php
include __DIR__ . "/../config/db.php";

class QuizModel {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function createQuiz($instructorId, $title, $description, $timeLimit, $status) {
        $sql = "INSERT INTO quizzes (instructor_id, title, description, time_limit_minutes, status, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "issis", $instructorId, $title, $description, $timeLimit, $status);
        
        if(mysqli_stmt_execute($stmt)) {
            return mysqli_insert_id($this->conn);
        }
        return false;
    }
    
    public function getQuizzesByInstructor($instructorId) {
        $sql = "SELECT * FROM quizzes WHERE instructor_id = ? ORDER BY created_at DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $instructorId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    
    public function getQuizById($quizId, $instructorId) {
        $sql = "SELECT * FROM quizzes WHERE id = ? AND instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quizId, $instructorId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    
    public function updateQuiz($quizId, $instructorId, $title, $description, $timeLimit, $status) {
        $sql = "UPDATE quizzes 
                SET title = ?, description = ?, time_limit_minutes = ?, status = ? 
                WHERE id = ? AND instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisii", $title, $description, $timeLimit, $status, $quizId, $instructorId);
        return mysqli_stmt_execute($stmt);
    }
    
    public function deleteQuiz($quizId, $instructorId) {
        $sql = "DELETE FROM quizzes WHERE id = ? AND instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quizId, $instructorId);
        return mysqli_stmt_execute($stmt);
    }
    
    public function toggleStatus($quizId, $instructorId) {
        $sql = "SELECT status FROM quizzes WHERE id = ? AND instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quizId, $instructorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $quiz = mysqli_fetch_assoc($result);
        
        if(!$quiz) {
            return false;
        }
        
        $newStatus = ($quiz['status'] == 'draft') ? 'published' : 'draft';
        
        $sql = "UPDATE quizzes SET status = ? WHERE id = ? AND instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sii", $newStatus, $quizId, $instructorId);
        
        if(mysqli_stmt_execute($stmt)) {
            return $newStatus;
        }
        return false;
    }
    
    public function updateTotalMarks($quizId) {
        $sql = "UPDATE quizzes 
                SET total_marks = (SELECT COALESCE(SUM(marks), 0) FROM questions WHERE quiz_id = ?) 
                WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quizId, $quizId);
        return mysqli_stmt_execute($stmt);
    }
    
    public function getTotalQuizzesCount($instructorId) {
        $sql = "SELECT COUNT(*) as count FROM quizzes WHERE instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $instructorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
    
    public function getTotalQuestionsCount($instructorId) {
        $sql = "SELECT COUNT(q.id) as count 
                FROM questions q 
                INNER JOIN quizzes qu ON q.quiz_id = qu.id 
                WHERE qu.instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $instructorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
    
    public function getPublishedQuizzesCount($instructorId) {
        $sql = "SELECT COUNT(*) as count FROM quizzes WHERE instructor_id = ? AND status = 'published'";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $instructorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
    
    public function hasQuestions($quizId, $instructorId) {
        $sql = "SELECT COUNT(q.id) as count 
                FROM questions q 
                INNER JOIN quizzes qu ON q.quiz_id = qu.id 
                WHERE qu.id = ? AND qu.instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quizId, $instructorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['count'] > 0;
    }

    public function getPublishedQuizzes() {
        $sql = "SELECT * FROM quizzes WHERE status = 'published' ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getQuizWithQuestions($quizId) {
        $sql = "SELECT * FROM quizzes WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $quizId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $quiz = mysqli_fetch_assoc($res);
        if(!$quiz) return null;

        $sqlQ = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index";
        $stmtQ = mysqli_prepare($this->conn, $sqlQ);
        mysqli_stmt_bind_param($stmtQ, "i", $quizId);
        mysqli_stmt_execute($stmtQ);
        $questionsRes = mysqli_stmt_get_result($stmtQ);

        $quiz['questions'] = [];
        while($q = mysqli_fetch_assoc($questionsRes)){
            $sqlO = "SELECT * FROM options WHERE question_id = ?";
            $stmtO = mysqli_prepare($this->conn, $sqlO);
            mysqli_stmt_bind_param($stmtO, "i", $q['id']);
            mysqli_stmt_execute($stmtO);
            $optsRes = mysqli_stmt_get_result($stmtO);
            $q['options'] = mysqli_fetch_all($optsRes, MYSQLI_ASSOC);
            $quiz['questions'][] = $q;
        }

        return $quiz;
    }
}
?>
