<?php
session_start();
include 'db.php';

// Security Check: Only Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle Search
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM users WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' OR id_number LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM users";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- FIXED TOP BAR STYLING --- */
        .top-bar { 
            display: flex; 
            justify-content: space-between; /* Pushes Search to Left, Add to Right */
            align-items: center; 
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            gap: 20px; /* Ensures they never touch on small screens */
        }

        .search-box {
            display: flex;
            gap: 10px; /* Space between Input and Search Button */
            align-items: center;
            flex: 1; /* Allows search bar to take available space */
            max-width: 500px;
        }

        .search-box input { 
            padding: 0 15px; 
            width: 100%; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            height: 45px; /* Fixed Height */
            margin: 0;
            font-size: 14px;
        }

        /* Unified Button Sizes */
        .btn-action { 
            height: 45px; /* Matches Input Height Exactly */
            padding: 0 25px; 
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            border: none;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap; /* Prevents text breaking */
        }

        .btn-search { background-color: #00695C; color: white; }
        .btn-search:hover { background-color: #004D40; }

        .btn-add { background-color: #15356A; color: white; }
        .btn-add:hover { background-color: #0d2347; }

        /* Table Actions */
        .btn-edit { color: #15356A; font-weight: bold; text-decoration: none; margin-right: 10px; }
        .btn-delete { color: #d9534f; font-weight: bold; text-decoration: none; }
        
        .role-badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; font-weight: bold;}
        .role-Admin { background-color: #15356A; }
        .role-TrafficOfficer { background-color: #e67e22; }
        .role-Citizen { background-color: #00695C; }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
  <div class="sidebar" style="background-color: #15356A;">
        <h2>Turuqna Admin</h2>
        <a href="dashboard_admin.php">Overview</a>
        <a href="manage_users.php"class="active">Manage Users</a>
        <a href="admin_reports.php" >Report Oversight</a>
        <a href="admin_settings.php">System Settings</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h1>User Management</h1>

        <!-- Top Toolbar -->
        <div class="top-bar">
            <!-- Left Side: Search Form -->
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search Name, Email or ID..." value="<?php echo $search; ?>">
                <button type="submit" class="btn-action btn-search">Search</button>
            </form>

            <!-- Right Side: Add Button -->
            <a href="add_user.php" class="btn-action btn-add">+ Add New User</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>ID Number</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['id_number']; ?></td>
                    <td>
                        <span class="role-badge role-<?php echo $row['role']; ?>">
                            <?php echo $row['role']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" class="btn-edit">Edit</a>
                        <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>