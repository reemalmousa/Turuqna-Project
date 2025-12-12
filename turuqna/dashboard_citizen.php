<?php
include 'init.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT latitude, longitude, description, status, created_at FROM reports";
$result = $conn->query($sql);
$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['title']; ?> - <?php echo $lang['live_map']; ?></title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">

    <style>
        .main-content {
            padding: 0 !important;
            position: relative;
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }
        .sidebar { height: 100vh; }
        #map { height: 100vh; width: 100%; }

        .welcome-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            background: white;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        body.rtl .welcome-overlay { left: auto; right: 20px; flex-direction: row-reverse; }

        .quick-btn {
            position: absolute;
            bottom: 40px;
            right: 40px;
            z-index: 1000;
            background-color: #00695C;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        body.rtl .quick-btn { right: auto; left: 40px; flex-direction: row-reverse; }

        .citizen-legend {
            position: absolute;
            bottom: 40px;
            left: 20px;
            background: white;
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            gap: 15px;
            font-size: 12px;
            font-weight: bold;
            color: #555;
        }
        body.rtl .citizen-legend { left: auto; right: 20px; }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
    </style>
</head>

<body class="<?php echo $lang['dir']; ?>">

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="?lang=<?php echo $lang['lang_link']; ?>" style="background:rgba(255,255,255,0.1); text-align:center; margin-bottom:15px;">
            🌐 <?php echo $lang['lang_switch']; ?>
        </a>

        <h2><?php echo $lang['title']; ?></h2>
        <a href="dashboard_citizen.php" class="active"><?php echo $lang['live_map']; ?></a>
        <a href="submit_report.php"><?php echo $lang['submit_report']; ?></a>
        <a href="my_reports.php"><?php echo $lang['my_reports']; ?></a>
        <a href="notifications.php"><?php echo $lang['notifications']; ?></a>
        <a href="profile.php"><?php echo $lang['profile']; ?></a>
        <a href="logout.php" class="logout-btn"><?php echo $lang['logout']; ?></a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="welcome-overlay">
            <span style="font-size:20px;">👋</span>
            <div>
                <h2><?php echo $lang['welcome']; ?>, <?php echo explode(' ', $_SESSION['name'])[0]; ?></h2>
                <span><?php echo $lang['traffic_status']; ?></span>
            </div>
        </div>

        <div id="map"></div>

        <div class="citizen-legend">
            <div><span class="dot" style="background:#e74c3c;"></span> <?php echo $lang['high']; ?></div>
            <div><span class="dot" style="background:#f39c12;"></span> <?php echo $lang['medium']; ?></div>
            <div><span class="dot" style="background:#27ae60;"></span> <?php echo $lang['low']; ?></div>
        </div>

        <a href="submit_report.php" class="quick-btn">
            <span style="font-size:20px;">+</span> <?php echo $lang['report_traffic']; ?>
        </a>

    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    var map = L.map('map', { zoomControl: false }).setView([24.7136, 46.6753], 12);

    setTimeout(() => {
        map.invalidateSize();
    }, 200);

    L.control.zoom({ position: 'topright' }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var reports = <?php echo json_encode($reports); ?>;

    reports.forEach(function (r) {
        var color = '#27ae60';
        var status = r.status.toLowerCase();

        if (status.includes('pending')) color = '#e74c3c';
        else if (status.includes('progress')) color = '#f39c12';

        L.circleMarker([r.latitude, r.longitude], {
            radius: 8,
            fillColor: color,
            color: '#fff',
            weight: 2,
            fillOpacity: 0.9
        }).addTo(map).bindPopup(r.description);
    });
</script>

</body>
</html>
