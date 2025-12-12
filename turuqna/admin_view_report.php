<?php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Get Report ID
if (!isset($_GET['id'])) {
    header("Location: admin_reports.php");
    exit();
}

$report_id = $_GET['id'];

// Fetch Full Report Details
$sql = "SELECT reports.*, users.full_name, users.phone_number, users.email 
        FROM reports 
        JOIN users ON reports.user_id = users.user_id 
        WHERE report_id = $report_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Report not found.";
    exit();
}

$report = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Report #<?php echo $report_id; ?> - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        /* --- LAYOUT STYLES --- */
        .main-content { display: block !important; padding: 30px; background-color: #f4f6f8; overflow-y: auto; width: 100%; }
        
        /* Header & Back Button */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .back-btn { text-decoration: none; color: #555; font-weight: bold; display: flex; align-items: center; gap: 5px; background: white; padding: 8px 15px; border-radius: 5px; border: 1px solid #ddd; transition: 0.2s; }
        .back-btn:hover { background: #eee; }

        /* Grid Layout */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two Equal Columns */
            gap: 25px;
        }

        /* Content Boxes */
        .info-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .info-card h3 { margin-top: 0; color: #15356A; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }

        /* Text Data */
        .data-row { margin-bottom: 15px; }
        .data-label { font-size: 12px; color: #888; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 3px; }
        .data-value { font-size: 15px; color: #333; font-weight: 500; }

        /* Status Badge */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block;}
        .b-Pending { background: #fff5f5; color: #e74c3c; }
        .b-Resolved { background: #f0fdf4; color: #27ae60; }
        .b-InProgress { background: #fff7ed; color: #f39c12; }

        /* Officer Note Box */
        .officer-note {
            background-color: #f8f9fa;
            border-left: 4px solid #15356A;
            padding: 15px;
            border-radius: 4px;
            color: #555;
            font-style: italic;
        }

        /* Evidence Image */
        .evidence-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-bottom: 20px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .evidence-img:hover { transform: scale(1.02); }

        /* Map */
        #map {
            width: 100%;
            height: 250px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <!-- Admin Sidebar -->
    <div class="sidebar" style="background-color: #15356A;">
        <h2>Turuqna Admin</h2>
        <a href="dashboard_admin.php">Overview</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="admin_reports.php" class="active">Report Oversight</a>
        <a href="admin_settings.php">System Settings</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <a href="admin_reports.php" class="back-btn">← Back to List</a>
            </div>
            <div style="display: flex; gap: 10px;">
                <!-- Admin Actions -->
                <a href="admin_reports.php?delete=<?php echo $report_id; ?>" onclick="return confirm('Permanently delete this report?')" 
                   style="background: #e74c3c; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                   Delete Report
                </a>
            </div>
        </div>

        <div class="details-grid">
            
            <!-- LEFT COLUMN: Information -->
            <div class="info-card">
                <h3>Report Details #<?php echo $report_id; ?></h3>
                
                <div class="data-row">
                    <span class="data-label">Current Status</span>
                    <span class="badge b-<?php echo str_replace(' ', '', $report['status']); ?>">
                        <?php echo $report['status']; ?>
                    </span>
                </div>

                <div class="data-row">
                    <span class="data-label">Submitted By</span>
                    <div class="data-value"><?php echo $report['full_name']; ?></div>
                    <div style="font-size: 13px; color: #777;"><?php echo $report['email']; ?> | <?php echo $report['phone_number']; ?></div>
                </div>

                <div class="data-row">
                    <span class="data-label">Date & Time</span>
                    <div class="data-value"><?php echo date('F j, Y, g:i a', strtotime($report['created_at'])); ?></div>
                </div>

                <div class="data-row">
                    <span class="data-label">Description</span>
                    <div class="data-value" style="background: #f9f9f9; padding: 10px; border-radius: 5px;">
                        <?php echo $report['description']; ?>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                <div class="data-row">
                    <span class="data-label">Officer's Internal Note</span>
                    <?php if(!empty($report['officer_comment'])): ?>
                        <div class="officer-note">
                            "<?php echo $report['officer_comment']; ?>"
                        </div>
                    <?php else: ?>
                        <div style="color: #999; font-style: italic;">No notes added yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT COLUMN: Visuals -->
            <div class="info-card">
                <h3>Evidence & Location</h3>
                
                <span class="data-label">Photo Evidence</span>
                <a href="<?php echo $report['image_path']; ?>" target="_blank">
                    <img src="<?php echo $report['image_path']; ?>" class="evidence-img" alt="Report Evidence">
                </a>

                <span class="data-label">GPS Location</span>
                <div id="map"></div>
                <div style="margin-top: 5px; font-size: 12px; color: #777;">
                    Lat: <?php echo $report['latitude']; ?>, Lng: <?php echo $report['longitude']; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Map Script -->
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    var lat = <?php echo $report['latitude']; ?>;
    var lng = <?php echo $report['longitude']; ?>;
    
    var map = L.map('map').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add Red Marker
    var redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    L.marker([lat, lng], {icon: redIcon}).addTo(map)
     .bindPopup("<b>Incident Location</b>").openPopup();
</script>

</body>
</html>