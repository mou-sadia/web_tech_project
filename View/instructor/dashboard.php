<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../login.php");
    exit();
}

include "../../Model/QuizModel.php";

$quizModel = new QuizModel();
$instructorId = $_SESSION["user_id"];

$quizzes = $quizModel->getQuizzesByInstructor($instructorId);
$totalQuizzes = $quizModel->getTotalQuizzesCount($instructorId);
$totalQuestions = $quizModel->getTotalQuestionsCount($instructorId);
$publishedQuizzes = $quizModel->getPublishedQuizzesCount($instructorId);
?>

<html>
<head>
    <title>Instructor Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f0f0f0;
            padding: 15px;
            border: 1px solid #ccc;
            text-align: center;
            width: 150px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
        }
        .create-btn {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
        .status-draft {
            color: #ffc107;
        }
        .status-published {
            color: #28a745;
        }
        .btn-edit, .btn-questions {
            background: #007bff;
            color: white;
            padding: 3px 8px;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 3px 8px;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-toggle {
            background: #6c757d;
            color: white;
            padding: 3px 8px;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Instructor Dashboard</h1>
                <p>Welcome, <strong><?php echo $_SESSION["name"]; ?></strong>!</p>
            </div>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalQuizzes; ?></div>
                <div>Total Quizzes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalQuestions; ?></div>
                <div>Total Questions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $publishedQuizzes; ?></div>
                <div>Published</div>
            </div>
        </div>

        <a href="create_quiz.php" class="create-btn">+ Create New Quiz</a>

        <h2>Your Quizzes</h2>
        
        <?php if($quizzes && mysqli_num_rows($quizzes) > 0): ?>
        <table border="0" cellpadding="8" cellspacing="0">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Marks</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while($quiz = mysqli_fetch_assoc($quizzes)): ?>
            <tr>
                <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                <td><?php echo htmlspecialchars(substr($quiz['description'], 0, 50)); ?></td>
                <td><?php echo $quiz['total_marks']; ?></td>
                <td><?php echo $quiz['time_limit_minutes']; ?> min</td>
                <td class="status-<?php echo $quiz['status']; ?>"><?php echo ucfirst($quiz['status']); ?></td>
                <td>
                    <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn-edit">Edit</a>
                    <a href="manage_questions.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn-questions">Questions</a>
                    <a href="../../Controller/quiz/delete_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn-delete" onclick="return confirm('Delete this quiz?')">Delete</a>
                    <button class="btn-toggle" data-quiz-id="<?php echo $quiz['id']; ?>">
                        <?php echo ($quiz['status'] == 'draft') ? 'Publish' : 'Unpublish'; ?>
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p>No quizzes yet. Click "Create New Quiz" to start.</p>
        <?php endif; ?>
    </div>

    <script src="../../Controller/JS/quiz_manager.js"></script>
</body>
</html>