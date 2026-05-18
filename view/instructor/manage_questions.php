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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin: 25px 0 15px 0;
            font-size: 18px;
        }
        .quiz-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .quiz-info p {
            margin: 5px 0;
        }
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .add-question-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #ddd;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 60px;
            resize: vertical;
        }
        .options-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .options-group input[type="text"] {
            flex: 1;
        }
        .radio-correct {
            width: 80px;
            text-align: center;
        }
        .submit-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #218838;
        }
        .question-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .question-text {
            font-weight: bold;
            font-size: 16px;
        }
        .question-marks {
            color: #666;
            font-size: 12px;
        }
        .options-list {
            padding-left: 20px;
        }
        .option-item {
            padding: 5px 0;
        }
        .option-correct {
            color: #28a745;
            font-weight: bold;
        }
        .btn-edit-question, .btn-delete-question {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }
        .btn-edit-question {
            background: #007bff;
            color: white;
        }
        .btn-edit-question:hover {
            background: #0069d9;
        }
        .btn-delete-question {
            background: #dc3545;
            color: white;
        }
        .btn-delete-question:hover {
            background: #c82333;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .inline-edit-form {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .inline-save-btn {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 5px;
        }
        .inline-cancel-btn {
            background: #6c757d;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <h1>Manage Questions</h1>
        
        <div class="quiz-info">
            <p><strong>Quiz:</strong> <?php echo htmlspecialchars($quiz['title']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($quiz['description']); ?></p>
            <p><strong>Total Marks:</strong> <?php echo $quiz['total_marks']; ?> | <strong>Time Limit:</strong> <?php echo $quiz['time_limit_minutes']; ?> minutes</p>
        </div>

        <?php if($success == "added"): ?>
        <div class="success-msg">✓ Question added successfully!</div>
        <?php elseif($success == "deleted"): ?>
        <div class="success-msg">✓ Question deleted successfully!</div>
        <?php elseif($error): ?>
        <div class="error-msg">✗ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Add Question Form -->
        <div class="add-question-form">
            <h2>Add New Question</h2>
            <form method="post" action="../../controller/quiz/add_question.php">
                <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>"/>
                
                <div class="form-group">
                    <label>Question Text:</label>
                    <textarea name="question_text" placeholder="Enter your question here..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Marks:</label>
                    <input type="number" name="marks" value="1" min="1" required/>
                </div>

                <div class="form-group">
                    <label>Options:</label>
                    <div class="options-group">
                        <input type="text" name="option_a" placeholder="Option A" required/>
                        <span class="radio-correct"><input type="radio" name="correct_option" value="0" checked/> Correct</span>
                    </div>
                    <div class="options-group">
                        <input type="text" name="option_b" placeholder="Option B" required/>
                        <span class="radio-correct"><input type="radio" name="correct_option" value="1"/> Correct</span>
                    </div>
                    <div class="options-group">
                        <input type="text" name="option_c" placeholder="Option C" required/>
                        <span class="radio-correct"><input type="radio" name="correct_option" value="2"/> Correct</span>
                    </div>
                    <div class="options-group">
                        <input type="text" name="option_d" placeholder="Option D" required/>
                        <span class="radio-correct"><input type="radio" name="correct_option" value="3"/> Correct</span>
                    </div>
                </div>

                <button type="submit" class="submit-btn">+ Add Question</button>
            </form>
        </div>

        <!-- Questions List -->
        <h2>Existing Questions (<?php echo count($questions); ?>)</h2>
        
        <?php if(count($questions) > 0): ?>
            <?php $counter = 1; ?>
            <?php foreach($questions as $question): ?>
            <div class="question-card" data-question-id="<?php echo $question['id']; ?>">
                <div class="question-header">
                    <div>
                        <span class="question-text">Q<?php echo $counter++; ?>. <?php echo htmlspecialchars($question['question_text']); ?></span>
                        <span class="question-marks">(<?php echo $question['marks']; ?> marks)</span>
                    </div>
                    <div>
                        <button class="btn-edit-question" data-question-id="<?php echo $question['id']; ?>">Edit</button>
                        <button class="btn-delete-question" data-question-id="<?php echo $question['id']; ?>">Delete</button>
                    </div>
                </div>
                <div class="options-list">
                    <?php foreach($question['options'] as $optIndex => $option): ?>
                    <div class="option-item <?php echo ($option['is_correct'] == 1) ? 'option-correct' : 'option-wrong'; ?>">
                        <?php echo chr(65 + $optIndex); ?>. <?php echo htmlspecialchars($option['option_text']); ?>
                        <?php if($option['is_correct'] == 1): ?> ✓ (Correct)<?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #666; padding: 20px; text-align: center;">No questions added yet. Use the form above to add your first question!</p>
        <?php endif; ?>
        
        <br/>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <script src="../../controller/ajax/quiz_manager.js"></script>
</body>
</html>