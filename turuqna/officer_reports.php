<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'TrafficOfficer') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT reports.*, users.full_name FROM reports JOIN users ON reports.user_id = users.user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Reports - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-view { background-color: #15356A; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar">
    <h2>Officer Panel</h2>
    <a href="dashboard_officer.php">Dashboard</a>
    <a href="officer_reports.php"class="active">Manage Reports</a>
    <a href="officer_progress.php">Progress Reports</a> <!-- Make sure this is here -->
    <a href="profile.php">My Profile</a>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

    <div class="main-content">
        <h2>Incoming Traffic Reports</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reporter</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['report_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                    <td>
                        <span class="status-<?php echo str_replace(' ', '', $row['status']); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="report_details.php?id=<?php echo $row['report_id']; ?>" class="btn-view">View / Update</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>