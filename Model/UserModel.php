<?php
include "../config/db.php";

class UserModel {
  
    public function register($name, $email, $password, $role) {
        global $conn;

        $sql = "insert into users 
                (name, email, password_hash, role)
                values (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt, 
            "ssss", 
            $name, $email, $password, $role
        );

        return mysqli_stmt_execute($stmt);
    }

    public function checkEmail($email) {
        global $conn;

        $sql = "select id from users 
                where email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        return mysqli_stmt_get_result($stmt);
    }

    public function getUserByEmail($email) {
        global $conn;

        $sql = "select * from users 
                where email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function getAllUsers() {
        global $conn;

        $sql = "select * from users 
                order by created_at desc";

        $result = mysqli_query($conn, $sql);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function toggleActive($user_id) {
        global $conn;

        $sql = "update users 
                set is_active = if(is_active=1, 0, 1) 
                where id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $user_id);

        return mysqli_stmt_execute($stmt);
    }
}
?>