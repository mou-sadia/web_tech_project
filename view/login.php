<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/quiz_platform/view/css/login.css">
</head>
<body>

<div class="container">
    <h2>Login Form</h2>

    <form id="loginForm">

        <label>Email:</label>
        <input type="text" id="loginEmail" name="email" placeholder="Enter your email">
        <span class="error" id="loginEmailError"></span>

        <label>Password:</label>
        <input type="password" id="loginPassword" name="password" placeholder="Enter your password">
        <span class="error" id="loginPasswordError"></span>

        <button type="submit">Login</button>

    </form>

    <p id="loginMessage"></p>

    <div class="register-link">
        Don't have an account? <a href="/quiz_platform/view/register.php">Register here</a>
    </div>

</div>

<script src="/quiz_platform/controller/ajax/login.js"></script>

</body>
</html>
