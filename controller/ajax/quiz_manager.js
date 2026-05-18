document.querySelectorAll('.btn-toggle').forEach(function(button) {
    button.addEventListener('click', function() {
        var quizId = this.dataset.quizId;
        var buttonElement = this;
        var originalText = buttonElement.textContent;
        
        buttonElement.disabled = true;
        buttonElement.textContent = 'Processing...';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response.success) {
                    var newButtonText = response.new_status === 'draft' ? 'Publish' : 'Unpublish';
                    buttonElement.textContent = newButtonText;
                    
                    var row = buttonElement.closest('tr');
                    var statusCell = row.querySelector('td:nth-child(5)');
                    statusCell.textContent = response.new_status.charAt(0).toUpperCase() + response.new_status.slice(1);
                    statusCell.className = 'status-' + response.new_status;
                } else {
                    alert(response.error || 'Failed to update status');
                    buttonElement.textContent = originalText;
                }
                buttonElement.disabled = false;
            }
        };
        
        xhttp.open("POST", "../../controller/quiz/toggle_status.php", true);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.send(JSON.stringify({quiz_id: quizId}));
    });
});

function setupDeleteButtons() {
    document.querySelectorAll('.btn-delete-question').forEach(function(button) {
        button.removeEventListener('click', handleDeleteClick);
        button.addEventListener('click', handleDeleteClick);
    });
}

function handleDeleteClick(event) {
    var button = event.currentTarget;
    var questionCard = button.closest('.question-card');
    var questionId = questionCard.dataset.questionId;
    var quizId = getQuizIdFromUrl();
    
    if(confirm('Delete this question?')) {
        button.disabled = true;
        button.textContent = 'Deleting...';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response.success) {
                    questionCard.remove();
                    alert('Question deleted');
                } else {
                    alert(response.error || 'Failed to delete');
                    button.disabled = false;
                    button.textContent = 'Delete';
                }
            }
        };
        
        xhttp.open("POST", "../../controller/quiz/delete_question.php", true);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.send(JSON.stringify({
            question_id: questionId,
            quiz_id: quizId
        }));
    }
}

function setupEditButtons() {
    document.querySelectorAll('.btn-edit-question').forEach(function(button) {
        button.removeEventListener('click', handleEditClick);
        button.addEventListener('click', handleEditClick);
    });
}

function handleEditClick(event) {
    var button = event.currentTarget;
    var questionCard = button.closest('.question-card');
    var questionId = questionCard.dataset.questionId;
    var quizId = getQuizIdFromUrl();
    
    var questionTextSpan = questionCard.querySelector('.question-text');
    var currentText = questionTextSpan.textContent;
    
    var editForm = document.createElement('div');
    editForm.className = 'inline-edit-form';
    editForm.style.marginTop = '10px';
    editForm.innerHTML = `
        <textarea id="edit-question-text" rows="2" cols="60">${escapeHtml(currentText)}</textarea><br/>
        <button class="inline-save-btn">Save</button>
        <button class="inline-cancel-btn">Cancel</button>
    `;
    
    questionTextSpan.style.display = 'none';
    questionCard.insertBefore(editForm, questionCard.querySelector('.question-header').nextSibling);
    
    editForm.querySelector('.inline-save-btn').addEventListener('click', function() {
        var newText = editForm.querySelector('#edit-question-text').value.trim();
        
        if(newText === '') {
            alert('Question text cannot be empty');
            return;
        }
        
        this.disabled = true;
        this.textContent = 'Saving...';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response.success) {
                    questionTextSpan.textContent = newText;
                    questionTextSpan.style.display = '';
                    editForm.remove();
                    alert('Question updated');
                } else {
                    alert(response.error || 'Failed to update');
                    this.disabled = false;
                    this.textContent = 'Save';
                }
            }
        };
        
        xhttp.open("POST", "../../controller/quiz/update_question.php", true);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.send(JSON.stringify({
            question_id: questionId,
            quiz_id: quizId,
            question_text: newText
        }));
    });
    
    editForm.querySelector('.inline-cancel-btn').addEventListener('click', function() {
        questionTextSpan.style.display = '';
        editForm.remove();
    });
}

function getQuizIdFromUrl() {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('quiz_id');
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
document.addEventListener('DOMContentLoaded', function() {
    setupDeleteButtons();
    setupEditButtons();
});