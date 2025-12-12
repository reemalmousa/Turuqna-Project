<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'TrafficOfficer') {
    header("Location: login.php");
    exit();
}

$report_id = $_GET['id'];
$msg = "";

// --- SAVE CHANGES ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $comment = $_POST['officer_comment'];

    // Update Status & Comment
    $stmt = $conn->prepare("UPDATE reports SET status = ?, officer_comment = ? WHERE report_id = ?");
    $stmt->bind_param("ssi", $status, $comment, $report_id);
    $stmt->execute();

    // Notify User
    $user_q = $conn->query("SELECT user_id FROM reports WHERE report_id = $report_id");
    $owner_id = $user_q->fetch_assoc()['user_id'];
    $notif_msg = "Status updated to $status. Officer Note: $comment";
    $conn->query("INSERT INTO notifications (user_id, message) VALUES ($owner_id, '$notif_msg')");

    $msg = "Report updated successfully!";
}

// Fetch Data
$sql = "SELECT reports.*, users.full_name, users.phone_number FROM reports JOIN users ON reports.user_id = users.user_id WHERE report_id = $report_id";
$result = $conn->query($sql);
$report = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Report - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        .split-view { display: flex; gap: 20px; }
        .box { background: white; padding: 25px; border-radius: 8px; flex: 1; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .evidence-img { width: 100%; height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
        label { display: block; font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
        textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Officer Panel</h2>
        <a href="dashboard_officer.php">Dashboard</a>
        <a href="officer_reports.php" class="active">Manage Reports</a>
        <a href="officer_progress.php">Progress Reports</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h2>Manage Report #<?php echo $report['report_id']; ?></h2>
        <?php if($msg) echo "<p style='color:green; font-weight:bold;'>$msg</p>"; ?>

        <form method="POST">
            <div class="split-view">
                <!-- Left: Details & Actions -->
                <div class="box">
                    <p><strong>Reporter:</strong> <?php echo $report['full_name']; ?> (<?php echo $report['phone_number']; ?>)</p>
                    <p><strong>Description:</strong> <?php echo $report['description']; ?></p>
                    <hr>
                    
                    <label>Update Status:</label>
                    <select name="status">
                        <option value="Pending" <?php if($report['status']=='Pending') echo 'selected'; ?>>Pending</option>
                        <option value="In Progress" <?php if($report['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                        <option value="Resolved" <?php if($report['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                    </select>

                    <label>Officer Comment (Visible to Admin):</label>
                    <textarea name="officer_comment" rows="4" placeholder="Add notes about this incident..."><?php echo $report['officer_comment']; ?></textarea>

                    <button type="submit" class="btn-primary" style="margin-top: 20px;">Save Changes</button>
                </div>

                <!-- Right: Evidence -->
                <div class="box">
                    <img src="<?php echo $report['image_path']; ?>" class="evidence-img">
                    <div id="map" style="height: 250px; border-radius: 8px;"></div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    var lat = <?php echo $report['latitude']; ?>;
    var lng = <?php echo $report['longitude']; ?>;
    var map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([lat, lng]).addTo(map);
</script>
</body>
</html>