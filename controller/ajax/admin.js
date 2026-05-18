function toggleUser(userId){
    var formData = new FormData();
    formData.append("action",  "toggle_active");
    formData.append("user_id", userId);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            var data = JSON.parse(this.responseText);
            if(data.success){
                var btn    = document.getElementById("btn-"    + userId);
                var status = document.getElementById("status-" + userId);

                if(btn.innerHTML.trim() == "Suspend"){
                    btn.innerHTML    = "Activate";
                    btn.className    = "btn-activate";
                    status.innerHTML = "Suspended";
                }else{
                    btn.innerHTML    = "Suspend";
                    btn.className    = "btn-suspend";
                    status.innerHTML = "Active";
                }
            }
        }
    };
    xhttp.open("POST", "/quiz_platform/controller/AuthController.php", true);
    xhttp.send(formData);
}
