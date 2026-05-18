<?php
// expects $attempts array
if(!isset($attempts)) $attempts = [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Results</title>
    <link rel="stylesheet" href="../view/css/touhid.css">
</head>
<body>
    <h1>My Quiz Results</h1>
    
    <?php if(count($attempts) == 0): ?>
        <p>No completed attempts yet.</p>
    <?php else: ?>
        <table class="results-table">
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Score</th>
                    <th>Date Taken</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($attempts as $att): ?>
                <?php 
                    $score = intval($att['score']);
                    $total = intval($att['total_marks']);
                    $percentage = $total > 0 ? round(($score / $total) * 100) : 0;
                    $passed = $percentage >= 60;
                    
                    $started = new DateTime($att['started_at']);
                    $completed = new DateTime($att['completed_at']);
                    $duration = $completed->diff($started)->format('%H:%I:%S');
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($att['title']); ?></td>
                    <td><?php echo $score . ' / ' . $total; ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($att['completed_at'])); ?></td>
                    <td><?php echo $duration; ?></td>
                    <td class="<?php echo $passed ? 'pass-badge' : 'fail-badge'; ?>">
                        <?php echo $passed ? 'PASS' : 'FAIL'; ?>
                    </td>
                    <td><a href="../result.php?attempt_id=<?php echo $att['id']; ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
</body>
</html>
