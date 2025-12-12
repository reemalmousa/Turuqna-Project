<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // Get the role (Citizen, TrafficOfficer, Admin)
$msg = "";

// Handle Update Logic
if (isset($_POST['update_profile'])) {
    $name = $_POST['full_name'];
    $phone = $_POST['phone'];
    
    // Update basic info
    $conn->query("UPDATE users SET full_name='$name', phone_number='$phone' WHERE user_id=$user_id");
    
    // Update Password if provided
    if (!empty($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$pass' WHERE user_id=$user_id");
    }
    
    $msg = "Profile updated successfully!";
    $_SESSION['name'] = $name;
}

// Fetch user data
$user = $conn->query("SELECT * FROM users WHERE user_id=$user_id")->fetch_assoc();

// Determine Sidebar Color based on Role
$sidebar_color = ($role === 'Admin') ? '#15356A' : '#00695C';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific styling for profile form */
        .profile-card {
            background: white;
            width: 100%;
            max-width: 600px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        input[disabled] { background-color: #eee; cursor: not-allowed; }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    
    <!-- DYNAMIC SIDEBAR -->
    <div class="sidebar" style="background-color: <?php echo $sidebar_color; ?>;">
        
        <?php if ($role == 'Admin'): ?>
            <h2>Turuqna Admin</h2>
            <a href="dashboard_admin.php">Overview</a>
            <a href="manage_users.php">Manage Users</a>
			 <a href="admin_reports.php">Report Oversight</a> <!-- NEW -->
             <a href="admin_settings.php">System Settings</a> <!-- NEW -->
            <a href="profile.php" class="active">My Profile</a>

        <?php elseif ($role == 'TrafficOfficer'): ?>
            <h2>Officer Panel</h2>
            <a href="dashboard_officer.php">Dashboard</a>
            <a href="officer_reports.php">Manage Reports</a>
        <a href="officer_progress.php">Progress Reports</a> 
            <a href="profile.php" class="active">My Profile</a>

        <?php else: /* Default to Citizen */ ?>
            <h2>Turuqna</h2>
            <a href="dashboard_citizen.php">Live Map</a>
            <a href="submit_report.php">Submit Report</a>
            <a href="my_reports.php">My Reports</a>
			<a href="notifications.php">Notifications</a>
            <a href="profile.php" class="active">My Profile</a>
        <?php endif; ?>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="profile-card">
            <h2 style="color: <?php echo $sidebar_color; ?>;">Edit Profile</h2>
            
            <?php if($msg): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <label>Role</label>
                <input type="text" value="<?php echo $user['role']; ?>" disabled>

                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>

                <label>Email (Cannot be changed)</label>
                <input type="email" value="<?php echo $user['email']; ?>" disabled>

                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $user['phone_number']; ?>" required>

                <label>New Password (Leave blank to keep current)</label>
                <input type="password" name="password" placeholder="********">

                <button type="submit" name="update_profile" class="btn-primary" style="background-color: <?php echo $sidebar_color; ?>;">Save Changes</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>