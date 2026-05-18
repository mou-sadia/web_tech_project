<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../login.php");
    exit();
}

include "../../Model/QuizModel.php";

$quizId = $_GET["id"] ?? 0;

$quizModel = new QuizModel();
$instructorId = $_SESSION["user_id"];

$result = $quizModel->getQuizById($quizId, $instructorId);
$quiz = mysqli_fetch_assoc($result);

if(!$quiz) {
    header("Location: dashboard.php");
    exit();
}

$titleError = $_SESSION["titleError"] ?? "";
$descError = $_SESSION["descError"] ?? "";
$timeError = $_SESSION["timeError"] ?? "";
$generalError = $_SESSION["generalError"] ?? "";

unset($_SESSION["titleError"]);
unset($_SESSION["descError"]);
unset($_SESSION["timeError"]);
unset($_SESSION["generalError"]);
?>

<html>
<head>
    <title>Edit Quiz</title>
</head>
<body>
    <h1>Edit Quiz</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <br/><br/>

    <?php if($generalError): ?>
    <p style="color:red"><?php echo $generalError; ?></p>
    <?php endif; ?>

    <form method="post" action="../../Controller/quiz/update_quiz.php">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>"/>
        
        <table border="0">
            <tr>
                <td>Title:</td>
                <td><input type="text" name="title" value="<?php echo htmlspecialchars($quiz['title']); ?>"/></td>
                <td style="color:red"><?php echo $titleError; ?></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="description"><?php echo htmlspecialchars($quiz['description']); ?></textarea></td>
                <td style="color:red"><?php echo $descError; ?></td>
            </tr>
            <tr>
                <td>Time Limit (minutes):</td>
                <td><input type="number" name="time_limit" value="<?php echo $quiz['time_limit_minutes']; ?>"/></td>
                <td style="color:red"><?php echo $timeError; ?></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    <select name="status">
                        <option value="draft" <?php echo ($quiz['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo ($quiz['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Update Quiz"/>
                    <a href="dashboard.php">Cancel</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>