<?php
// expects $quiz and $attemptId
if(!isset($quiz)){
    echo "Quiz not found";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($quiz['title']); ?></title>
    <link rel="stylesheet" href="../view/css/touhid.css">
</head>
<body>
<h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
<p><?php echo htmlspecialchars($quiz['description']); ?></p>
<div id="timer">Time left: <span id="t"></span></div>

<form id="quizForm">
    <input type="hidden" name="attempt_id" value="<?php echo intval($attemptId); ?>" />
    <?php foreach($quiz['questions'] as $qi => $q): ?>
        <div class="question">
            <p><strong>Q<?php echo $qi+1; ?>.</strong> <?php echo htmlspecialchars($q['question_text']); ?> (<?php echo $q['marks']; ?>)</p>
            <?php foreach($q['options'] as $opt): ?>
                <div>
                    <label>
                        <input type="radio" name="answer_<?php echo $q['id']; ?>" value="<?php echo $opt['id']; ?>"> <?php echo htmlspecialchars($opt['option_text']); ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <button type="button" id="submitBtn">Submit Quiz</button>
</form>

<script>
(function(){
    var minutes = parseInt(<?php echo intval($quiz['time_limit_minutes']); ?>);
    var seconds = minutes * 60;
    var tEl = document.getElementById('t');
    var attemptId = <?php echo intval($attemptId); ?>;

    function tick(){
        var m = Math.floor(seconds/60);
        var s = seconds % 60;
        tEl.textContent = (m<10?'0'+m:m) + ':' + (s<10?'0'+s:s);
        if(seconds<=0){
            submitQuiz();
            return;
        }
        seconds--;
    }
    setInterval(tick, 1000);
    tick();

    document.getElementById('submitBtn').addEventListener('click', submitQuiz);

    function submitQuiz(){
        var inputs = document.querySelectorAll('input[type=radio]:checked');
        var answers = [];
        inputs.forEach(function(inp){
            var name = inp.name; // answer_QID
            var qid = name.split('_')[1];
            answers.push({question_id: parseInt(qid), option_id: parseInt(inp.value)});
        });

        fetch('../api/quiz_submit.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({attempt_id: attemptId, answers: answers})
        }).then(r=>r.json()).then(function(data){
            if(data.success){
                window.location.href = '../result.php?attempt_id=' + attemptId;
            }else{
                alert('Submit failed: ' + (data.error||'Unknown'));
            }
        }).catch(function(e){
            alert('Network error');
        });
    }
})();
</script>

</body>
</html>
