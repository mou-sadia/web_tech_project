<?php
include __DIR__ . "/../config/db.php";

class QuestionModel {
    
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function addQuestion($quizId, $questionText, $marks, $options, $correctOptionIndex) {

        $sql = "SELECT COALESCE(MAX(order_index), 0) + 1 as next_order FROM questions WHERE quiz_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $quizId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $nextOrder = $row['next_order'];
        
        $sql = "INSERT INTO questions (quiz_id, question_text, marks, order_index) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "isii", $quizId, $questionText, $marks, $nextOrder);
        
        if(!mysqli_stmt_execute($stmt)) {
            return false;
        }
        
        $questionId = mysqli_insert_id($this->conn);
        
        for($i = 0; $i < count($options); $i++) {
            $isCorrect = ($i == $correctOptionIndex) ? 1 : 0;
            $sql = "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "isi", $questionId, $options[$i], $isCorrect);
            
            if(!mysqli_stmt_execute($stmt)) {
                return false;
            }
        }
        
        $this->updateQuizTotalMarks($quizId);
        
        return true;
    }
    
    public function getQuestionsByQuiz($quizId, $instructorId = null) {
        if($instructorId) {
            $sql = "SELECT q.* FROM questions q 
                    INNER JOIN quizzes qu ON q.quiz_id = qu.id 
                    WHERE q.quiz_id = ? AND qu.instructor_id = ? 
                    ORDER BY q.order_index";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $quizId, $instructorId);
        } else {
            $sql = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $quizId);
        }
        
        mysqli_stmt_execute($stmt);
        $questions = mysqli_stmt_get_result($stmt);
        
        $result = array();
        while($question = mysqli_fetch_assoc($questions)) {

            $sql = "SELECT * FROM options WHERE question_id = ?";
            $stmt2 = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt2, "i", $question['id']);
            mysqli_stmt_execute($stmt2);
            $options = mysqli_stmt_get_result($stmt2);
            
            $question['options'] = array();
            while($option = mysqli_fetch_assoc($options)) {
                $question['options'][] = $option;
            }
            $result[] = $question;
        }
        
        return $result;
    }
    
    public function updateQuestionText($questionId, $quizId, $instructorId, $questionText) {
        $sql = "UPDATE questions q 
                INNER JOIN quizzes qu ON q.quiz_id = qu.id 
                SET q.question_text = ? 
                WHERE q.id = ? AND q.quiz_id = ? AND qu.instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "siii", $questionText, $questionId, $quizId, $instructorId);
        return mysqli_stmt_execute($stmt);
    }
    
    public function updateCorrectOption($questionId, $quizId, $instructorId, $correctOptionId) {

        $sql = "UPDATE options o 
                INNER JOIN questions q ON o.question_id = q.id 
                INNER JOIN quizzes qu ON q.quiz_id = qu.id 
                SET o.is_correct = 0 
                WHERE o.question_id = ? AND q.quiz_id = ? AND qu.instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $questionId, $quizId, $instructorId);
        mysqli_stmt_execute($stmt);

        $sql = "UPDATE options o 
                INNER JOIN questions q ON o.question_id = q.id 
                INNER JOIN quizzes qu ON q.quiz_id = qu.id 
                SET o.is_correct = 1 
                WHERE o.id = ? AND o.question_id = ? AND q.quiz_id = ? AND qu.instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $correctOptionId, $questionId, $quizId, $instructorId);
        return mysqli_stmt_execute($stmt);
    }
    
    public function deleteQuestion($questionId, $quizId, $instructorId) {

        $checkSql = "SELECT q.id FROM questions q 
                INNER JOIN quizzes qu ON q.quiz_id = qu.id 
                     WHERE q.id = ? AND q.quiz_id = ? AND qu.instructor_id = ?";
        $stmt = mysqli_prepare($this->conn, $checkSql);
         mysqli_stmt_bind_param($stmt, "iii", $questionId, $quizId, $instructorId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
         if(mysqli_num_rows($result) == 0) {
                return false;
        }
    
        $sql1 = "DELETE FROM options WHERE question_id = ?";
        $stmt1 = mysqli_prepare($this->conn, $sql1);
        mysqli_stmt_bind_param($stmt1, "i", $questionId);
        mysqli_stmt_execute($stmt1);
    
        $sql2 = "DELETE FROM questions WHERE id = ? AND quiz_id = ?";
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "ii", $questionId, $quizId);
    
        if(mysqli_stmt_execute($stmt2)) {
        $this->updateQuizTotalMarks($quizId);
        return true;
    }
    return false;
}
    
    public function updateQuizTotalMarks($quizId) {
        $sql = "UPDATE quizzes 
                SET total_marks = (SELECT COALESCE(SUM(marks), 0) FROM questions WHERE quiz_id = ?) 
                WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quizId, $quizId);
        return mysqli_stmt_execute($stmt);
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
}
?>