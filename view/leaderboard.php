<?php
// expects $topStudents array
if(!isset($topStudents)) $topStudents = [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
    <link rel="stylesheet" href="../view/css/touhid.css">
</head>
<body>
    <h1>Leaderboard</h1>
    <p>Top 10 Students by Total Score</p>
    <div id="refresh-info">Refreshing in <span id="countdown">30</span>s</div>
    
    <table id="leaderboardTable" class="leaderboard-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Student Name</th>
                <th>Total Score</th>
                <th>Attempts</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            $rank = 1;
            foreach($topStudents as $student): 
        ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo intval($student['total_score']); ?></td>
                <td><?php echo intval($student['attempts']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<script>
var refreshInterval = 30;
var countdownEl = document.getElementById('countdown');

function updateLeaderboard(){
    fetch('../api/leaderboard.php')
        .then(r => r.json())
        .then(function(data){
            if(data.success && data.data){
                var tbody = document.querySelector('#leaderboardTable tbody');
                tbody.innerHTML = '';
                var rank = 1;
                data.data.forEach(function(student){
                    var tr = document.createElement('tr');
                    tr.innerHTML = '<td>' + rank + '</td>' +
                        '<td>' + escapeHtml(student.name) + '</td>' +
                        '<td>' + student.total_score + '</td>' +
                        '<td>' + student.attempts + '</td>';
                    tbody.appendChild(tr);
                    rank++;
                });
            }
            refreshInterval = 30;
            updateCountdown();
        })
        .catch(function(e){ console.error(e); });
}

function updateCountdown(){
    countdownEl.textContent = refreshInterval;
    if(refreshInterval <= 0){
        updateLeaderboard();
    } else {
        refreshInterval--;
        setTimeout(updateCountdown, 1000);
    }
}

updateCountdown();

function escapeHtml(text){
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

</body>
</html>
