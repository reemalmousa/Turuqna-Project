<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // First, delete related reports to avoid database errors
    $conn->query("DELETE FROM reports WHERE user_id = $id");
    
    // Then delete the user
    $conn->query("DELETE FROM users WHERE user_id = $id");
}

header("Location: manage_users.php");
exit();
?>