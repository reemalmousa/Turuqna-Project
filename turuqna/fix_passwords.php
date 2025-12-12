<?php
include 'db.php';

// 1. Create the real encrypted hash for "123456"
$new_password = password_hash("123456", PASSWORD_DEFAULT);

// 2. Update the Officer
$sql1 = "UPDATE users SET password = '$new_password' WHERE email = 'officer@turuqna.com'";

// 3. Update the Admin
$sql2 = "UPDATE users SET password = '$new_password' WHERE email = 'admin@turuqna.com'";

if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
    echo "<h1>✅ Passwords Fixed!</h1>";
    echo "<p>You can now log in with the password: <strong>123456</strong></p>";
    echo "<a href='login.php'>Go to Login Page</a>";
} else {
    echo "Error updating record: " . $conn->error;
}
?>