<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

if (isset($_POST['add_user'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $id_num = $_POST['id_number'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, id_number, phone_number, password, role) 
            VALUES ('$name', '$email', '$id_num', '$phone', '$pass', '$role')";

    if ($conn->query($sql) === TRUE) {
        header("Location: manage_users.php");
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add User - Turuqna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar" style="background-color: #15356A;">
        <h2>Turuqna Admin</h2>
        <a href="dashboard_admin.php">Overview</a>
        <a href="manage_users.php" class="active">Manage Users</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <div class="report-card">
            <h2>Add New User</h2>
            <?php if($msg) echo "<p style='color:red'>$msg</p>"; ?>
            
            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="full_name" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>ID Number</label>
                <input type="text" name="id_number" required>

                <label>Phone Number</label>
                <input type="text" name="phone" required>

                <label>Role</label>
                <select name="role" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;">
                    <option value="Citizen">Citizen</option>
                    <option value="TrafficOfficer">Traffic Officer</option>
                    <option value="Admin">Administrator</option>
                </select>

                <label>Password</label>
                <input type="password" name="password" required>

                <button type="submit" name="add_user" class="btn-primary">Create User</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>