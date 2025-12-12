<?php
include 'init.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$users_count = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$reports_count = $conn->query("SELECT COUNT(*) as c FROM reports")->fetch_assoc()['c'];
$officers_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='TrafficOfficer'")->fetch_assoc()['c'];

$map_data = []; 
$result = $conn->query("SELECT * FROM reports"); 
while($row = $result->fetch_assoc()) { $map_data[] = $row; }

$alerts_q = $conn->query("SELECT * FROM reports WHERE status='Pending' ORDER BY created_at DESC LIMIT 3");

$status_counts = ['Pending' => 0, 'In Progress' => 0, 'Resolved' => 0];
$status_q = $conn->query("SELECT status, COUNT(*) as c FROM reports GROUP BY status");
while($row = $status_q->fetch_assoc()) { $status_counts[$row['status']] = $row['c']; }
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['admin_panel']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-content { display: block !important; padding: 30px; background-color: #f4f6f8; overflow-y: auto; width: 100%; }
        .sidebar { z-index: 10000; position: relative; background-color: #15356A !important; }
        .sidebar-logo { width: 80%; display: block; margin: 0 auto 20px auto; border-radius: 10px; }
        
        .card-box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h3 { margin-top: 0; color: #15356A; font-size: 18px; margin-bottom: 15px; }

        .stats-row { display: flex; gap: 20px; margin-bottom: 25px; }
        .stat-item { flex: 1; text-align: center; border-left: 5px solid #15356A; padding: 25px; }
        body.rtl .stat-item { border-left: none; border-right: 5px solid #15356A; }
        
        /* Map Section */
        .map-row { display: flex; gap: 20px; height: 500px; margin-bottom: 25px; }
        .map-wrapper { flex: 2; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; position: relative; z-index: 1; display: flex; flex-direction: column; background: white; }
        #map { flex: 1; width: 100%; z-index: 1; }
        .map-legend { height: 50px; background: #fff; border-top: 1px solid #eee; display: flex; align-items: center; justify-content: center; gap: 30px; direction: ltr;}
        
        /* --- ALERTS SECTION (Fixed to match Officer) --- */
        .alerts-container { flex: 1; background: white; border-radius: 12px; padding: 20px; overflow-y: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .alert-item { border-left: 4px solid #e74c3c; padding: 12px; margin-bottom: 12px; background: #fff5f5; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
        .alert-item:hover { background: #fee2e2; }
        body.rtl .alert-item { border-left: none; border-right: 4px solid #e74c3c; text-align: right; }
        
        .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .chart-container { height: 250px; position: relative; }

        /* Marker Styling */
        .custom-marker-container { position: relative; display: flex; justify-content: center; align-items: center; width: 60px; height: 60px; }
        .halo { position: absolute; width: 100%; height: 100%; border-radius: 50%; opacity: 0.9; z-index: 1; }
        .inner-badge { position: relative; z-index: 2; width: 32px; height: 32px; background-color: #3b3b3b; border: 2px solid white; border-radius: 50%; display: flex; justify-content: center; align-items: center; box-shadow: 0 2px 6px rgba(0,0,0,0.3); }
        .inner-badge svg { width: 18px; height: 18px; fill: white; }
        .halo-red { background-color: rgba(231, 76, 60, 0.3); border: 2px solid #e74c3c; }
        .halo-orange { background-color: rgba(243, 156, 18, 0.3); border: 2px solid #f39c12; }
        .halo-green { background-color: rgba(39, 174, 96, 0.3); border: 2px solid #2ecc71; }
    </style>
</head>
<body class="<?php echo $lang['dir']; ?>">

<div class="dashboard-container">
    <div class="sidebar">
        <!-- LOGO -->
        <img src="images/logo.png" alt="Logo" class="sidebar-logo">

        <a href="?lang=<?php echo $lang['lang_link']; ?>" style="background:rgba(255,255,255,0.1); text-align:center; display:block; padding:5px; border-radius:5px; margin-bottom:15px; color:white; text-decoration:none;">
             <?php echo $lang['lang_switch']; ?>
        </a>

        <h2><?php echo $lang['admin_panel']; ?></h2>
        <a href="dashboard_admin.php" class="active"><?php echo $lang['overview']; ?></a>
        <a href="manage_users.php"><?php echo $lang['manage_users']; ?></a>
        <a href="admin_reports.php"><?php echo $lang['report_oversight']; ?></a>
        <a href="admin_settings.php"><?php echo $lang['system_settings']; ?></a>
        <a href="profile.php"><?php echo $lang['profile']; ?></a>
        <a href="logout.php" class="logout-btn"><?php echo $lang['logout']; ?></a>
    </div>

    <div class="main-content">
        <h1><?php echo $lang['system_status']; ?></h1>
        
        <div class="stats-row">
            <div class="card-box stat-item"><h1><?php echo $users_count; ?></h1><p><?php echo $lang['registered_users']; ?></p></div>
            <div class="card-box stat-item"><h1><?php echo $officers_count; ?></h1><p><?php echo $lang['active_officers']; ?></p></div>
            <div class="card-box stat-item"><h1><?php echo $reports_count; ?></h1><p><?php echo $lang['total_reports']; ?></p></div>
            <div class="card-box stat-item" style="border-color: #27ae60;"><h1 style="color: #27ae60;"><?php echo $lang['active']; ?></h1><p><?php echo $lang['system_status']; ?></p></div>
        </div>

        <div class="map-row">
            <!-- MAP -->
            <div class="map-wrapper card-box" style="padding: 0;">
                <div id="map"></div>
                <div class="map-legend">
                    <div class="legend-item"><span class="dot" style="background:#e74c3c;"></span> High</div>
                    <div class="legend-item"><span class="dot" style="background:#f39c12;"></span> Medium</div>
                    <div class="legend-item"><span class="dot" style="background:#27ae60;"></span> Low</div>
                </div>
            </div>

            <!-- LIVE ALERTS (Fixed Style) -->
            <div class="alerts-container">
                <h3 style="color: #15356A;"><?php echo $lang['live_alerts']; ?></h3>
                <?php if($alerts_q->num_rows > 0): ?>
                    <?php while($alert = $alerts_q->fetch_assoc()): ?>
                        <div class="alert-item" onclick="zoomToAlert(<?php echo $alert['latitude']; ?>, <?php echo $alert['longitude']; ?>)">
                            <strong>Report #<?php echo $alert['report_id']; ?></strong><br>
                            <span style="font-size:13px; color:#555;"><?php echo substr($alert['description'], 0, 40); ?>...</span>
                            <br>
                            <a href="admin_view_report.php?id=<?php echo $alert['report_id']; ?>" style="font-size:12px; color:#15356A; font-weight:bold; display:block; margin-top:5px;">View Details →</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color:#777; text-align:center; margin-top:50px;">No pending alerts.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="charts-row">
            <div class="card-box"><h3><?php echo $lang['traffic_summary']; ?></h3><div class="chart-container"><canvas id="barChart"></canvas></div></div>
            <div class="card-box"><h3><?php echo $lang['traffic_severity']; ?></h3><div class="chart-container" style="display:flex; justify-content:center;"><canvas id="pieChart"></canvas></div></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([24.7136, 46.6753], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    setTimeout(function(){ map.invalidateSize(); }, 500);

    var warningIcon = '<svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>';
    var reports = <?php echo json_encode($map_data); ?>;
    
    reports.forEach(function(r) {
        var haloClass = 'halo-green';
        if(r.status == 'Pending') haloClass = 'halo-red';
        else if (r.status == 'In Progress') haloClass = 'halo-orange';

        var customIcon = L.divIcon({
            className: 'custom-marker-wrapper', 
            html: `<div class="custom-marker-container"><div class="halo ${haloClass}"></div><div class="inner-badge">${warningIcon}</div></div>`,
            iconSize: [60, 60], iconAnchor: [30, 30] 
        });
        L.marker([r.latitude, r.longitude], { icon: customIcon }).addTo(map);
    });

    function zoomToAlert(lat, lng) { map.setView([lat, lng], 16); }

    new Chart(document.getElementById('barChart'), { type: 'bar', data: { labels: ['Jan', 'Feb', 'Mar'], datasets: [{ label: 'Activity', data: [12, 19, 3], backgroundColor: '#3b82f6' }] } });
    new Chart(document.getElementById('pieChart'), { type: 'pie', data: { labels: ['High', 'Medium', 'Low'], datasets: [{ data: [<?php echo $status_counts['Pending']; ?>, <?php echo $status_counts['In Progress']; ?>, <?php echo $status_counts['Resolved']; ?>], backgroundColor: ['#ef4444', '#f97316', '#22c55e'] }] } });
</script>
</body>
</html>