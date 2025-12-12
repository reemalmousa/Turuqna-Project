<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "turuqna_db";
$port = 3307; // YOUR SPECIFIC PORT

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>