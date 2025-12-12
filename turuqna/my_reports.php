<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM reports WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Reports - Turuqna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Turuqna</h2>
        <a href="dashboard_citizen.php">Live Map</a>
        <a href="submit_report.php">Submit Report</a>
        <a href="my_reports.php" class="active">My Reports</a>
			<a href="notifications.php">Notifications</a>
		<a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h2>My Submitted Reports</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Photo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['report_id']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <a href="<?php echo $row['image_path']; ?>" target="_blank">View Image</a>
                    </td>
                    <td class="status-<?php echo $row['status']; ?>">
                        <?php echo $row['status']; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>