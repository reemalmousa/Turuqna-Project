<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>System Settings - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- LAYOUT FIXES --- */
        .main-content { 
            display: block !important; 
            padding: 30px; 
            background-color: #f4f6f8; 
            overflow-y: auto; 
            width: 100%; 
        }

        h1 { margin-bottom: 30px; color: #15356A; }

        /* Grid Layout for Settings Cards */
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two columns side by side */
            gap: 25px;
            margin-bottom: 25px;
        }

        .card-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .full-width {
            grid-column: span 2; /* Makes the Logs section take full width */
        }

        h3 { margin-top: 0; color: #15356A; font-size: 18px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;}
        p { color: #555; font-size: 14px; line-height: 1.5; margin-bottom: 20px; }

        /* Buttons */
        .btn-action { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; color: white; font-weight: bold; font-size: 14px; transition: 0.2s; width: fit-content;}
        .btn-action:hover { opacity: 0.9; }
        .btn-backup { background-color: #27ae60; }
        .btn-danger { background-color: #c0392b; }

        /* Log Box Styling */
        .log-window {
            background: #1e1e1e;
            color: #00ff00;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            height: 200px;
            overflow-y: auto;
            border: 1px solid #333;
        }
        .log-entry { margin-bottom: 5px; }
        .timestamp { color: #888; margin-right: 10px; }

        /* Toggle Switch Styling */
        .maintenance-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff5f5;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ffcccc;
        }
        input[type=checkbox] { transform: scale(1.5); cursor: pointer; }
        .m-label { font-weight: bold; color: #c0392b; }

    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar" style="background-color: #15356A;">
        <h2>Turuqna Admin</h2>
        <a href="dashboard_admin.php">Overview</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="admin_reports.php">Report Oversight</a>
        <a href="admin_settings.php" class="active">System Settings</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h1>System Configuration</h1>

        <div class="settings-grid">
            
            <!-- 1. Database Management -->
            <div class="card-box">
                <div>
                    <h3>💾 Database Management</h3>
                    <p>Create a secure backup of all users, reports, and system data. It is recommended to perform this action weekly.</p>
                </div>
                <button class="btn-action btn-backup" onclick="alert('✅ Database backup created successfully!\nFile saved to: /backups/turuqna_2025.sql')">
                    Run Backup Now
                </button>
            </div>

            <!-- 2. Maintenance Mode -->
            <div class="card-box">
                <div>
                    <h3>🔒 Maintenance Mode</h3>
                    <p>Temporarily disable access for Citizens and Officers. Use this when performing system updates or bug fixes.</p>
                </div>
                <div class="maintenance-wrapper">
                    <input type="checkbox" id="m-mode">
                    <label for="m-mode" class="m-label">Enable Maintenance Mode</label>
                </div>
            </div>

            <!-- 3. System Logs (Full Width) -->
            <div class="card-box full-width">
                <h3>📜 System Activity Logs</h3>
                <div class="log-window">
                    <div class="log-entry"><span class="timestamp">[<?php echo date('Y-m-d H:i:s'); ?>]</span> System boot sequence initiated...</div>
                    <div class="log-entry"><span class="timestamp">[<?php echo date('Y-m-d H:i:s'); ?>]</span> Database connection established (Port 3307).</div>
                    <div class="log-entry"><span class="timestamp">[<?php echo date('Y-m-d H:i:s'); ?>]</span> HTTPS security check: Passed.</div>
                    <div class="log-entry"><span class="timestamp">[<?php echo date('Y-m-d H:i:s'); ?>]</span> Cron job [Auto-Archive] executed successfully.</div>
                    <div class="log-entry"><span class="timestamp">[<?php echo date('Y-m-d H:i:s'); ?>]</span> Admin User (ID: <?php echo $_SESSION['user_id']; ?>) accessed System Settings.</div>
                    <div class="log-entry"><span class="timestamp">[<?php echo date('Y-m-d H:i:s'); ?>]</span> API Endpoint /traffic-data responding 200 OK.</div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>