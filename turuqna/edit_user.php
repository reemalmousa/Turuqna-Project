<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$user_q = $conn->query("SELECT * FROM users WHERE user_id = $id");
$user = $user_q->fetch_assoc();

if (isset($_POST['update_user'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET full_name='$name', email='$email', phone_number='$phone', role='$role' WHERE user_id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: manage_users.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User - Turuqna</title>
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
            <h2>Edit User #<?php echo $id; ?></h2>
            
            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $user['phone_number']; ?>" required>

                <label>Role</label>
                <select name="role" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;">
                    <option value="Citizen" <?php if($user['role']=='Citizen') echo 'selected'; ?>>Citizen</option>
                    <option value="TrafficOfficer" <?php if($user['role']=='TrafficOfficer') echo 'selected'; ?>>Traffic Officer</option>
                    <option value="Admin" <?php if($user['role']=='Admin') echo 'selected'; ?>>Administrator</option>
                </select>

                <button type="submit" name="update_user" class="btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>