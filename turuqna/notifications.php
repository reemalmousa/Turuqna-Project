<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark all as read when opening page
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");

// Fetch Notifications
$sql = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Notifications - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .notif-item {
            background: white;
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-item:first-child { border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .notif-item:last-child { border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; border-bottom: none; }
        
        .time { font-size: 12px; color: #888; }
        .bell-icon { font-size: 40px; margin-bottom: 10px; }
        .empty-state { text-align: center; padding: 40px; color: #777; }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Turuqna</h2>
        <a href="dashboard_citizen.php">Live Map</a>
        <a href="submit_report.php">Submit Report</a>
        <a href="my_reports.php">My Reports</a>
        <a href="notifications.php" class="active">Notifications</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h1>Notifications</h1>
        
        <div class="report-card" style="padding: 0;">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="notif-item">
                        <div>
                            <strong>Status Update</strong><br>
                            <?php echo $row['message']; ?>
                        </div>
                        <span class="time"><?php echo date('M d, H:i', strtotime($row['created_at'])); ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="bell-icon">🔔</div>
                    <p>No new notifications.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>