<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../login.php");
    exit();
}

$quizId = $_GET["quiz_id"] ?? 0;

include "../../Model/QuizModel.php";
include "../../Model/QuestionModel.php";

$quizModel = new QuizModel();
$questionModel = new QuestionModel();
$instructorId = $_SESSION["user_id"];

$result = $quizModel->getQuizById($quizId, $instructorId);
$quiz = mysqli_fetch_assoc($result);

if(!$quiz) {
    header("Location: dashboard.php");
    exit();
}

$questions = $questionModel->getQuestionsByQuiz($quizId, $instructorId);
$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";
?>

<html>
<head>
    <title>Manage Questions - <?php echo htmlspecialchars($quiz['title']); ?></title>
</head>
<body>
    <h1>Manage Questions</h1>
    <p><strong>Quiz:</strong> <?php echo htmlspecialchars($quiz['title']); ?></p>
    <p><strong>Total Marks:</strong> <?php echo $quiz['total_marks']; ?></p>
    
    <a href="dashboard.php">Back to Dashboard</a>
    <br/><br/>

    <?php if($success == "added"): ?>
    <p style="color:green">Question added successfully!</p>
    <?php elseif($success == "deleted"): ?>
    <p style="color:green">Question deleted successfully!</p>
    <?php elseif($error): ?>
    <p style="color:red"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Add Question Form -->
    <h2>Add New Question</h2>
    <form method="post" action="../../Controller/quiz/add_question.php">
        <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>"/>
        
        <table border="0">
            <tr>
                <td>Question Text:</td>
                <td><textarea name="question_text" rows="3" cols="50" required></textarea>
                </td>
            </tr>
            <tr>
                <td>Marks:</td>
                <td><input type="number" name="marks" value="1" min="1" required/>
                </td>
            </tr>
            <tr>
                <td valign="top">Options:</td>
                <td>
                    A: <input type="text" name="option_a" required/> 
                    <input type="radio" name="correct_option" value="0" checked/> Correct<br/>
                    B: <input type="text" name="option_b" required/> 
                    <input type="radio" name="correct_option" value="1"/> Correct<br/>
                    C: <input type="text" name="option_c" required/> 
                    <input type="radio" name="correct_option" value="2"/> Correct<br/>
                    D: <input type="text" name="option_d" required/> 
                    <input type="radio" name="correct_option" value="3"/> Correct
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Add Question"/>
                </td>
            </tr>
        </table>
    </form>

    <!-- Questions List -->
    <h2>Existing Questions</h2>
    
    <?php if(count($questions) > 0): ?>
        <?php $counter = 1; ?>
        <?php foreach($questions as $question): ?>
        <div class="question-card" data-question-id="<?php echo $question['id']; ?>" style="border:1px solid #ccc; padding:10px; margin-bottom:15px;">
            <div class="question-header">
                <strong>Q<?php echo $counter++; ?>.</strong> 
                <span class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></span>
                <span style="color:#666;">(<?php echo $question['marks']; ?> marks)</span>
                
                <div style="float:right;">
                    <button class="btn-edit-question" data-question-id="<?php echo $question['id']; ?>">Edit</button>
                    <button class="btn-delete-question" data-question-id="<?php echo $question['id']; ?>">Delete</button>
                </div>
            </div>
            <div style="margin-top:10px; padding-left:20px;">
                <?php foreach($question['options'] as $optIndex => $option): ?>
                <div style="margin:5px 0;">
                    <?php echo chr(65 + $optIndex); ?>. <?php echo htmlspecialchars($option['option_text']); ?>
                    <?php if($option['is_correct'] == 1): ?> 
                        <span style="color:green;">✓ (Correct)</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No questions added yet. Use the form above to add your first question.</p>
    <?php endif; ?>
    
    <br/>
    <a href="dashboard.php">Back to Dashboard</a>

    <script src="../../Controller/JS/quiz_manager.js"></script>
</body>
</html>