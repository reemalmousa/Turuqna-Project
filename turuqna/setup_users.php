<?php
include 'db.php';

// 1. Delete the old accounts if they exist (to avoid duplicates)
$conn->query("DELETE FROM users WHERE email='officer@turuqna.com'");
$conn->query("DELETE FROM users WHERE email='admin@turuqna.com'");

// 2. Create the Hash for password "123456"
$pass = password_hash("123456", PASSWORD_DEFAULT);

// 3. Insert Officer Account
$sql1 = "INSERT INTO users (full_name, email, id_number, phone_number, password, role) 
         VALUES ('Officer Ahmed', 'officer@turuqna.com', '7000000001', '0500000001', '$pass', 'TrafficOfficer')";

// 4. Insert Admin Account
$sql2 = "INSERT INTO users (full_name, email, id_number, phone_number, password, role) 
         VALUES ('System Admin', 'admin@turuqna.com', '8000000001', '0500000002', '$pass', 'Admin')";

if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
    echo "<h1>✅ Success!</h1>";
    echo "<p>Officer and Admin accounts created.</p>";
    echo "<p><strong>Officer:</strong> officer@turuqna.com / 123456</p>";
    echo "<p><strong>Admin:</strong> admin@turuqna.com / 123456</p>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "Error: " . $conn->error;
}
?>