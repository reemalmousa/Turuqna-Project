<?php
include 'init.php'; // Loads Language
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'TrafficOfficer') {
    header("Location: login.php");
    exit();
}

// Logic (Stats & Charts)
$total = $conn->query("SELECT COUNT(*) as c FROM reports")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status='Pending'")->fetch_assoc()['c'];
$resolved = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status='Resolved'")->fetch_assoc()['c'];

$map_data = []; $result = $conn->query("SELECT * FROM reports"); while($row = $result->fetch_assoc()) { $map_data[] = $row; }
$alerts_q = $conn->query("SELECT * FROM reports WHERE status='Pending' ORDER BY created_at DESC LIMIT 3");
$hours_data = array_fill(0, 24, 0); 
$hour_q = $conn->query("SELECT HOUR(created_at) as hr, COUNT(*) as c FROM reports GROUP BY hr");
while($row = $hour_q->fetch_assoc()) { $hours_data[$row['hr']] = $row['c']; }
$hours_js_data = implode(',', $hours_data);
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['officer_panel']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-content { display: block !important; padding: 30px; overflow-y: auto; }
        .sidebar { z-index: 10000; position: relative; }
        .sidebar-logo { width: 80%; display: block; margin: 0 auto 20px auto; border-radius: 10px; }
        
        .stats-row { display: flex; gap: 20px; margin-bottom: 25px; width: 100%; }
        .card { background: white; padding: 25px; border-radius: 12px; flex: 1; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card h3 { font-size: 36px; margin: 10px 0; color: #00695C; }
        
        .middle-section { display: flex; gap: 25px; height: 500px; margin-bottom: 25px; width: 100%; }
        .map-container { flex: 3; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #ddd; position: relative; z-index: 1; display: flex; flex-direction: column; background: white; }
        #map { flex: 1; width: 100%; z-index: 1; }
        
        .map-legend { height: 50px; background: #fff; border-top: 1px solid #eee; display: flex; align-items: center; justify-content: center; gap: 30px; direction: ltr; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #555; }
        .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
        
        .alerts-container { flex: 1; background: white; border-radius: 12px; padding: 20px; overflow-y: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .alert-item { border-left: 4px solid #e74c3c; padding: 12px; margin-bottom: 12px; background: #fff5f5; border-radius: 6px; cursor: pointer; }
        body.rtl .alert-item { border-left: none; border-right: 4px solid #e74c3c; text-align: right; }
        
        .charts-row { display: flex; gap: 25px; height: 380px; width: 100%; }
        .chart-box { background: white; flex: 1; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Marker CSS */
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
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- LOGO -->
        <img src="images/logo.png" alt="Logo" class="sidebar-logo">
        
        <a href="?lang=<?php echo $lang['lang_link']; ?>" style="background:rgba(255,255,255,0.1); text-align:center; display:block; padding:5px; border-radius:5px; margin-bottom:15px; color:white; text-decoration:none;">
            <?php echo $lang['lang_switch']; ?>
        </a>

        <h2><?php echo $lang['officer_panel']; ?></h2>
        <a href="dashboard_officer.php" class="active"><?php echo $lang['dashboard']; ?></a>
        <a href="officer_reports.php"><?php echo $lang['manage_reports']; ?></a>
        <a href="officer_progress.php"><?php echo $lang['progress_reports']; ?></a>
        <a href="profile.php"><?php echo $lang['profile']; ?></a>
        <a href="logout.php" class="logout-btn"><?php echo $lang['logout']; ?></a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1><?php echo $lang['dashboard']; ?></h1>
        
        <div class="stats-row">
            <div class="card"><h3><?php echo $total; ?></h3><span><?php echo $lang['total_reports']; ?></span></div>
            <div class="card"><h3 style="color: #e74c3c;"><?php echo $pending; ?></h3><span><?php echo $lang['pending_action']; ?></span></div>
            <div class="card"><h3 style="color: #27ae60;"><?php echo $resolved; ?></h3><span><?php echo $lang['resolved']; ?></span></div>
        </div>

        <div class="middle-section">
            <div class="map-container">
                <div id="map"></div>
                <div class="map-legend">
                    <div class="legend-item"><span class="dot" style="background:#e74c3c;"></span> <?php echo $lang['high']; ?></div>
                    <div class="legend-item"><span class="dot" style="background:#f39c12;"></span> <?php echo $lang['medium']; ?></div>
                    <div class="legend-item"><span class="dot" style="background:#27ae60;"></span> <?php echo $lang['low']; ?></div>
                </div>
            </div>
            <div class="alerts-container">
                <h3 style="color: #15356A;"><?php echo $lang['live_alerts']; ?></h3>
                <?php while($alert = $alerts_q->fetch_assoc()): ?>
                    <div class="alert-item" onclick="location.href='report_details.php?id=<?php echo $alert['report_id']; ?>'">
                        <strong>Report #<?php echo $alert['report_id']; ?></strong><br>
                        <span style="font-size:13px; color:#555;"><?php echo substr($alert['description'], 0, 40); ?>...</span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="charts-row">
            <div class="chart-box"><h4><?php echo $lang['traffic_behavior']; ?></h4><canvas id="lineChart"></canvas></div>
            <div class="chart-box"><h4><?php echo $lang['congestion_level']; ?></h4><canvas id="pieChart"></canvas></div>
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

    new Chart(document.getElementById('pieChart'), { type: 'doughnut', data: { labels: ['Pending', 'In Progress', 'Resolved'], datasets: [{ data: [<?php echo $pending; ?>, <?php echo $total-$pending-$resolved; ?>, <?php echo $resolved; ?>], backgroundColor: ['#e74c3c', '#f39c12', '#27ae60'] }] } });
    new Chart(document.getElementById('lineChart'), { type: 'line', data: { labels: ['00:00','04:00','08:00','12:00','16:00','20:00'], datasets: [{ label: 'Reports', data: [<?php echo $hours_js_data; ?>], borderColor: '#00695C', tension: 0.3 }] } });
</script>
</body>
</html>