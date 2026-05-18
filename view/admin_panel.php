<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

include "../Model/UserModel.php";
$model = new UserModel();
$users = $model->getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="panel">

    <button class="logout"
        onclick="window.location.href='../controller/logout.php'">
        Logout
    </button>

    <h2>Admin Panel-User Management</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($users as $user): ?>
            <tr id="row-<?= $user['id'] ?>">
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <td id="status-<?= $user['id'] ?>">
                    <?= $user['is_active'] ? 'Active' : 'Suspended' ?>
                </td>
                <td>
                    <?php if($user['role'] !== 'admin'): ?>
                    <button
                        id="btn-<?= $user['id'] ?>"
                        class="<?= $user['is_active'] ? 'btn-suspend' : 'btn-activate' ?>"
                        onclick="toggleUser(<?= $user['id'] ?>)">
                        <?= $user['is_active'] ? 'Suspend' : 'Activate' ?>
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script src="../controller/ajax/admin.js"></script>

</body>
</html>
