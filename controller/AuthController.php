<?php
header('Content-Type: application/json');

session_start();

include "../Model/UserModel.php";

$model = new UserModel();

// reg
if(isset($_POST['action']) && $_POST['action'] == "register"){

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    $errors = [];

    if(empty($name)){
        $errors['name'] = "Name is required";
    }

    if(empty($email)){
        $errors['email'] = "Email is required";
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "Invalid email format";
    }else{
        $check = $model->checkEmail($email);
        if(mysqli_num_rows($check) > 0){
            $errors['email'] = "Email already exists";
        }
    }

    if(strlen($password) < 8){
        $errors['password'] = "Minimum 8 characters";
    }

    if(empty($role)){
        $errors['role'] = "Please select a role";
    }

    if(empty($errors)){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $result = $model->register($name, $email, $hash, $role);

        if($result){
            echo json_encode(["success" => "Registration Successful"]);
        }
    }else{
        echo json_encode(["errors" => $errors]);
    }

    exit();
}

// log
if(isset($_POST['action']) && $_POST['action'] == "login"){

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $errors = [];

    if(empty($email)){
        $errors['email'] = "Email is required";
    }

    if(empty($password)){
        $errors['password'] = "Password is required";
    }

    if(empty($errors)){
        $user = $model->getUserByEmail($email);

        if(!$user){
            $errors['email'] = "No account found";
        }else if(!password_verify($password, $user['password_hash'])){
            $errors['password'] = "Incorrect password";
        }else if($user['is_active'] == 0){
            $errors['general'] = "Your account has been suspended.";
        }else{
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            if($user['role'] == 'student'){
                echo json_encode([
                    "success"  => "Login Successful",
                    "redirect" => "/quiz_platform/view/student_home.php"
                ]);
            }else if($user['role'] == 'instructor'){
                echo json_encode([
                    "success"  => "Login Successful",
                    "redirect" => "/quiz_platform/view/instructor_home.php"
                ]);
            }else{
                echo json_encode([
                    "success"  => "Login Successful",
                    "redirect" => "/quiz_platform/view/admin_panel.php"
                ]);
            }
            exit();
        }
    }

    echo json_encode(["errors" => $errors]);
    exit();
}

// actv/not
if(isset($_POST['action']) && $_POST['action'] == "toggle_active"){

    if($_SESSION['role'] !== 'admin'){
        echo json_encode(["error" => "Unauthorized"]);
        exit();
    }

    $user_id = intval($_POST['user_id']);

    $result = $model->toggleActive($user_id);

    if($result){
        echo json_encode(["success" => true]);
    }else{
        echo json_encode(["error" => "Failed"]);
    }

    exit();
}
?>