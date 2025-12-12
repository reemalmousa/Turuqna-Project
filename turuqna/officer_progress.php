<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'TrafficOfficer') {
    header("Location: login.php");
    exit();
}

// Default Filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';
$where_clause = "";

if ($status_filter != 'All') {
    $where_clause = "WHERE status = '$status_filter'";
}

$sql = "SELECT * FROM reports $where_clause ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Progress Report - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .filter-bar { background: white; padding: 20px; border-radius: 8px; display: flex; gap: 15px; align-items: center; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .filter-bar select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 200px; }
        .btn-print { background-color: #7f8c8d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-left: auto; cursor: pointer; border: none; }
        
        @media print {
            .sidebar, .filter-bar, .btn-print { display: none; }
            .main-content { margin: 0; padding: 0; }
        }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Officer Panel</h2>
        <a href="dashboard_officer.php">Dashboard</a>
        <a href="officer_reports.php">Manage Reports</a>
        <a href="officer_progress.php" class="active">Progress Reports</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h1>Generate Progress Report</h1>

        <!-- Filter Section -->
        <form method="GET" class="filter-bar">
            <label>Filter Status:</label>
            <select name="status">
                <option value="All">All Reports</option>
                <option value="Pending" <?php if($status_filter=='Pending') echo 'selected'; ?>>Pending</option>
                <option value="In Progress" <?php if($status_filter=='In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Resolved" <?php if($status_filter=='Resolved') echo 'selected'; ?>>Resolved</option>
            </select>
            <button type="submit" class="btn-primary" style="width: auto; padding: 10px 20px; margin: 0;">Apply</button>
            
            <button type="button" onclick="window.print()" class="btn-print">🖨️ Print Report</button>
        </form>

        <!-- Results Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['report_id']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td>
                            <span class="status-<?php echo str_replace(' ', '', $row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No reports found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>