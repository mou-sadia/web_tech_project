<?php
// expects $quizzes, $quizId, $attempts, $analytics
if(!isset($quizzes)) $quizzes = [];
if(!isset($attempts)) $attempts = [];
if(!isset($analytics)) $analytics = [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quiz Analytics</title>
    <link rel="stylesheet" href="../view/css/touhid.css">
</head>
<body>
    <h1>Quiz Analytics</h1>
    
    <div class="analytics-section">
        <label>Select Quiz:</label>
        <select id="quizSelect" onchange="window.location.href='../controller/InstructorAnalyticsController.php?quiz_id=' + this.value;">
            <option value="">-- Choose a quiz --</option>
            <?php foreach($quizzes as $q): ?>
                <option value="<?php echo $q['id']; ?>" <?php echo ($q['id'] == $quizId) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($q['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if($quizId && count($attempts) > 0): ?>
        <div class="analytics-summary">
            <p>Total Attempts: <?php echo count($attempts); ?></p>
            <p>Class Average: <?php echo round($analytics['avg_score'] ?? 0, 2); ?></p>
            <p>Highest Score: <?php echo intval($analytics['max_score'] ?? 0); ?></p>
            <p>Lowest Score: <?php echo intval($analytics['min_score'] ?? 0); ?></p>
            <?php 
                $passCount = 0;
                $total = intval($analytics['total_marks'] ?? 1);
                foreach($attempts as $att){
                    $percentage = ($att['score'] / $total) * 100;
                    if($percentage >= 60) $passCount++;
                }
                $passRate = count($attempts) > 0 ? round(($passCount / count($attempts)) * 100) : 0;
            ?>
            <p>Pass Rate: <?php echo $passRate; ?>%</p>
        </div>

        <h2>Student Attempts</h2>
        <table class="attempts-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Score</th>
                    <th>Duration</th>
                    <th>Pass/Fail</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($attempts as $att): ?>
                <?php 
                    $score = intval($att['score']);
                    $percentage = ($score / $total) * 100;
                    $passed = $percentage >= 60;
                    $started = new DateTime($att['started_at']);
                    $completed = new DateTime($att['completed_at']);
                    $duration = $completed->diff($started)->format('%H:%I:%S');
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($att['name']); ?></td>
                    <td><?php echo $score . ' / ' . $total; ?></td>
                    <td><?php echo $duration; ?></td>
                    <td class="<?php echo $passed ? 'pass-badge' : 'fail-badge'; ?>">
                        <?php echo $passed ? 'PASS' : 'FAIL'; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif($quizId): ?>
        <p>No attempts for this quiz yet.</p>
    <?php else: ?>
        <p>Select a quiz to view analytics.</p>
    <?php endif; ?>
    
    <a href="../instructor/dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
