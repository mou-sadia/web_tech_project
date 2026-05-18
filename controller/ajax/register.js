document.getElementById("registerForm")
    .addEventListener("submit", function(e){
        e.preventDefault();

        var role = document.querySelector('input[name="role"]:checked');

        var formData = new FormData();
        formData.append("action",   "register");
        formData.append("name",     document.getElementById("name").value);
        formData.append("email",    document.getElementById("email").value);
        formData.append("password", document.getElementById("password").value);
        formData.append("role",     role ? role.value : "");

        document.getElementById("nameError").innerHTML     = "";
        document.getElementById("emailError").innerHTML    = "";
        document.getElementById("passwordError").innerHTML = "";
        document.getElementById("roleError").innerHTML     = "";
        document.getElementById("message").innerHTML       = "";

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                var data = JSON.parse(this.responseText);

                if(data.errors){
                    document.getElementById("nameError").innerHTML     = data.errors.name     || "";
                    document.getElementById("emailError").innerHTML    = data.errors.email    || "";
                    document.getElementById("passwordError").innerHTML = data.errors.password || "";
                    document.getElementById("roleError").innerHTML     = data.errors.role     || "";
                }else if(data.success){
                    document.getElementById("message").innerHTML = data.success;
                    setTimeout(function(){
                        window.location.href = "../view/login.php";
                    }, 2000);
                }
            }
        };

        xhttp.open("POST", "../controller/AuthController.php", true);
        xhttp.send(formData);
    });
