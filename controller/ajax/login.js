document.getElementById("loginForm")
    .addEventListener("submit", function(e){
        e.preventDefault();

        var formData = new FormData();
        formData.append("action",   "login");
        formData.append("email",    document.getElementById("loginEmail").value);
        formData.append("password", document.getElementById("loginPassword").value);

        document.getElementById("loginEmailError").innerHTML    = "";
        document.getElementById("loginPasswordError").innerHTML = "";
        document.getElementById("loginMessage").innerHTML       = "";

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                var data = JSON.parse(this.responseText);

                if(data.errors){
                    document.getElementById("loginEmailError").innerHTML    = data.errors.email    || "";
                    document.getElementById("loginPasswordError").innerHTML = data.errors.password || "";

                    if(data.errors.general){
                        document.getElementById("loginMessage").innerHTML = data.errors.general;
                    }

                }else if(data.success){
                    document.getElementById("loginMessage").innerHTML = data.success;
                    setTimeout(function(){
                        window.location.href = data.redirect;
                    }, 1000);
                }
            }
        };

        xhttp.open("POST", "../controller/AuthController.php", true);
        xhttp.send(formData);
    });
