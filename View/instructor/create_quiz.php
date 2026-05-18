<?php 
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "instructor"){
    header("Location: ../login.php");
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

$oldTitle = $_SESSION["old_title"] ?? "";
$oldDesc = $_SESSION["old_desc"] ?? "";
$oldTime = $_SESSION["old_time"] ?? "";

unset($_SESSION["old_title"]);
unset($_SESSION["old_desc"]);
unset($_SESSION["old_time"]);
?>

<html>
<head>
    <title>Create Quiz</title>
</head>
<body>
    <h1>Create New Quiz</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <br/><br/>

    <?php if($generalError): ?>
    <p style="color:red"><?php echo $generalError; ?></p>
    <?php endif; ?>

    <form method="post" action="../../Controller/quiz/create_quiz.php">
        <table border="0">
            <tr>
                <td>Title:</td>
                <td><input type="text" name="title" value="<?php echo htmlspecialchars($oldTitle); ?>"/></td>
                <td style="color:red"><?php echo $titleError; ?></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="description"><?php echo htmlspecialchars($oldDesc); ?></textarea></td>
                <td style="color:red"><?php echo $descError; ?></td>
            </tr>
            <tr>
                <td>Time Limit (minutes):</td>
                <td><input type="number" name="time_limit" value="<?php echo $oldTime; ?>"/></td>
                <td style="color:red"><?php echo $timeError; ?></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    <select name="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Create Quiz"/>
                    <a href="dashboard.php">Cancel</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>