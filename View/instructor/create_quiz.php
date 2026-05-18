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
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #007bff;
        }
        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .submit-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background: #218838;
        }
        .cancel-btn {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
            display: inline-block;
        }
        .cancel-btn:hover {
            background: #5a6268;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <h1>Create New Quiz</h1>

        <?php if($generalError): ?>
        <div class="error-msg">
            <?php echo $generalError; ?>
        </div>
        <?php endif; ?>

        <form method="post" action="../../Controller/quiz/create_quiz.php">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($oldTitle); ?>" placeholder="Enter quiz title"/>
                <?php if($titleError): ?>
                <div class="error"><?php echo $titleError; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" placeholder="Enter quiz description"><?php echo htmlspecialchars($oldDesc); ?></textarea>
                <?php if($descError): ?>
                <div class="error"><?php echo $descError; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Time Limit (minutes):</label>
                <input type="number" name="time_limit" value="<?php echo $oldTime; ?>" placeholder="Enter time limit"/>
                <?php if($timeError): ?>
                <div class="error"><?php echo $timeError; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>

            <div>
                <button type="submit" class="submit-btn">Create Quiz</button>
                <a href="dashboard.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>