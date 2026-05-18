<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<div class="container">
    <h2>Registration Form</h2>

    <form id="registerForm">

        <label>Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your name">
        <span class="error" id="nameError"></span>

        <label>Email:</label>
        <input type="text" id="email" name="email" placeholder="Enter your email">
        <span class="error" id="emailError"></span>

        <label>Password:</label>
        <input type="password" id="password" name="password" placeholder="Min 8 characters">
        <span class="error" id="passwordError"></span>

        <label>Role:</label><br><br>
        <input type="radio" name="role" value="student" id="student" style="width:auto;">
        <label for="student">Student</label>

        <input type="radio" name="role" value="instructor" id="instructor" style="width:auto; margin-left:20px;">
        <label for="instructor">Instructor</label>
        <br>
        <span class="error" id="roleError"></span>

        <button type="submit">Register</button>

    </form>

    <p id="message"></p>

    <div class="login-link">
        Already have an account? 
        <a href="login.php">Login here</a>
    </div>

</div>

<script src="../controller/ajax.js"></script>

</body>
</html>