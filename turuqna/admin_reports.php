<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// --- 1. HANDLE ACTIONS (Delete) ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM reports WHERE report_id = $id");
    header("Location: admin_reports.php");
    exit();
}

// --- 2. GET SUMMARY STATS ---
$total_q = $conn->query("SELECT COUNT(*) as c FROM reports");
$total = $total_q->fetch_assoc()['c'];

$pending_q = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status='Pending'");
$pending = $pending_q->fetch_assoc()['c'];

$resolved_q = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status='Resolved'");
$resolved = $resolved_q->fetch_assoc()['c'];

// Simulating "High Severity" as Pending reports created in the last 24 hours
$high_q = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status='Pending' AND created_at >= NOW() - INTERVAL 1 DAY");
$high = $high_q->fetch_assoc()['c'];

// --- 3. GET CHART DATA (Trends - Last 7 Days) ---
$dates = [];
$counts = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime("-$i days")); // Mon, Tue...
    $q = $conn->query("SELECT COUNT(*) as c FROM reports WHERE DATE(created_at) = '$date'");
    $dates[] = $day_name;
    $counts[] = $q->fetch_assoc()['c'];
}
$chart_labels = json_encode($dates);
$chart_data = json_encode($counts);

// --- 4. HANDLE FILTERS & SEARCH FOR TABLE ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT reports.*, users.full_name FROM reports JOIN users ON reports.user_id = users.user_id WHERE 1=1";

if ($filter != 'All') {
    $sql .= " AND status = '$filter'";
}

if (!empty($search)) {
    $sql .= " AND (users.full_name LIKE '%$search%' OR description LIKE '%$search%' OR report_id LIKE '%$search%')";
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Report Oversight - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* --- LAYOUT OVERRIDES --- */
        .main-content { display: block !important; padding: 30px; background-color: #f4f6f8; overflow-y: auto; width: 100%; }
        
        /* 1. TOP CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-info h3 { margin: 0; font-size: 14px; color: #777; font-weight: normal; }
        .stat-info strong { font-size: 24px; color: #333; }
        .icon-circle {
            width: 45px; height: 45px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        
        /* 2. CHARTS SECTION */
        .charts-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .chart-box h3 { margin-top: 0; color: #333; font-size: 16px; margin-bottom: 15px; }

        /* 3. TABLE CONTROLS (Header) */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            background: white;
            padding: 15px 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            border-bottom: 1px solid #eee;
        }
        .table-header h3 { margin: 0; color: #333; font-size: 18px; }
        
        .controls { display: flex; gap: 15px; align-items: center; }
        
        /* Filter Tabs */
        .filter-tabs {
            background: #f1f3f4;
            padding: 4px;
            border-radius: 8px;
            display: flex;
        }
        .filter-tabs a {
            padding: 6px 15px;
            text-decoration: none;
            color: #555;
            font-size: 13px;
            border-radius: 6px;
            font-weight: 600;
            transition: 0.3s;
        }
        .filter-tabs a.active {
            background: white;
            color: #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Search Input */
        .search-input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 200px;
            font-size: 13px;
            background-color: #f9f9f9;
        }

        /* Table Styling */
        .table-container {
            background: white;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 20px; color: #777; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #eee; background: white;}
        td { padding: 15px 20px; border-bottom: 1px solid #f9f9f9; font-size: 14px; color: #333; }
        
        .comment-cell { font-style: italic; color: #888; font-size: 12px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        /* Status Badges */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .b-Pending { background: #fff5f5; color: #e74c3c; }
        .b-Resolved { background: #f0fdf4; color: #27ae60; }
        .b-InProgress { background: #fff7ed; color: #f39c12; }

    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar" style="background-color: #15356A;">
        <h2>Turuqna Admin</h2>
        <a href="dashboard_admin.php">Overview</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="admin_reports.php" class="active">Report Oversight</a>
        <a href="admin_settings.php">System Settings</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        
        <!-- 1. STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info"><h3>Total Reports</h3><strong><?php echo $total; ?></strong></div>
                <div class="icon-circle" style="background:#e3f2fd; color:#2196f3;">📅</div>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3>Pending Review</h3><strong><?php echo $pending; ?></strong></div>
                <div class="icon-circle" style="background:#fff9c4; color:#fbc02d;">🕒</div>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3>Resolved</h3><strong><?php echo $resolved; ?></strong></div>
                <div class="icon-circle" style="background:#e8f5e9; color:#4caf50;">✔</div>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3>High Severity</h3><strong><?php echo $high; ?></strong></div>
                <div class="icon-circle" style="background:#ffebee; color:#f44336;">✖</div>
            </div>
        </div>

        <!-- 2. CHARTS ROW -->
        <div class="charts-grid">
            <div class="chart-box">
                <h3>Report Trends (Last 7 Days)</h3>
                <canvas id="trendsChart" height="150"></canvas>
            </div>
            <div class="chart-box">
                <h3>Status Breakdown</h3>
                <canvas id="statusChart" height="150"></canvas>
            </div>
        </div>

        <!-- 3. REPORT MANAGEMENT TABLE -->
        <div class="table-container">
            <!-- Header with Filters & Search -->
            <div class="table-header">
                <h3>Report Management</h3>
                
                <div class="controls">
                    <!-- Filter Tabs -->
                    <div class="filter-tabs">
                        <a href="?filter=All" class="<?php echo ($filter=='All')?'active':''; ?>">All</a>
                        <a href="?filter=Pending" class="<?php echo ($filter=='Pending')?'active':''; ?>">Pending</a>
                        <a href="?filter=Resolved" class="<?php echo ($filter=='Resolved')?'active':''; ?>">Resolved</a>
                        <a href="?filter=In Progress" class="<?php echo ($filter=='In Progress')?'active':''; ?>">In Progress</a>
                    </div>

                    <!-- Search -->
                    <form method="GET" style="margin:0;">
                        <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                        <input type="text" name="search" class="search-input" placeholder="Search reports..." value="<?php echo $search; ?>">
                    </form>
                </div>
            </div>

            <!-- Table -->
            <table>
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Reporter</th>
                        <th>Description</th>
                        <th>Officer Note</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['report_id']; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo substr($row['description'], 0, 30) . '...'; ?></td>
                            <td class="comment-cell">
                                <?php echo !empty($row['officer_comment']) ? $row['officer_comment'] : '-'; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <span class="badge b-<?php echo str_replace(' ', '', $row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_view_report.php?id=<?php echo $row['report_id']; ?>" style="margin-right:10px; text-decoration:none; color:#15356A; font-weight:bold;">View</a>
                                <a href="?delete=<?php echo $row['report_id']; ?>" onclick="return confirm('Delete?');" style="color:#d9534f; text-decoration:none;">🗑</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding:30px;">No reports found matching filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    // --- 1. Line Chart (Trends) ---
    new Chart(document.getElementById('trendsChart'), {
        type: 'line',
        data: {
            labels: <?php echo $chart_labels; ?>,
            datasets: [{
                label: 'Reports',
                data: <?php echo $chart_data; ?>,
                borderColor: '#1abc9c', /* Teal Color like screenshot */
                backgroundColor: 'rgba(26, 188, 156, 0.1)',
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#1abc9c',
                pointRadius: 4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } }
        }
    });

    // --- 2. Bar Chart (Status) ---
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: ['Pending', 'In Progress', 'Resolved'],
            datasets: [{
                data: [<?php echo $pending; ?>, <?php echo $total - $pending - $resolved; ?>, <?php echo $resolved; ?>],
                backgroundColor: ['#e74c3c', '#f39c12', '#1abc9c'],
                borderRadius: 4,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { display: false }, x: { grid: { display: false } } }
        }
    });
</script>

</body>
</html>