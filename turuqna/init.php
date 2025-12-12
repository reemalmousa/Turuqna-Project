<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if user clicked a language button
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// 2. SET DEFAULT TO ARABIC (If no language selected yet)
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar'; // Change 'en' to 'ar' here
}

// 3. Load the file
if ($_SESSION['lang'] == 'en') {
    $lang = include 'lang/en.php';
} else {
    $lang = include 'lang/ar.php';
}
?>