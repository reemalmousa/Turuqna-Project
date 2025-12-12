<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Turuqna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="form-section">
        
        <!-- NEW: Back to Home Link -->
        <a href="index.php" class="nav-home">
            <span>&#8592;</span> Back to Home
        </a>

        <h2>Sign Up</h2>
        <p>Create an account to report traffic.</p>
        
        <?php
        if (isset($_POST['signup'])) {
            $name = $_POST['fullname'];
            $email = $_POST['email'];
            $id_num = $_POST['id_number'];
            $phone = $_POST['phone'];
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (full_name, email, id_number, phone_number, password) VALUES ('$name', '$email', '$id_num', '$phone', '$pass')";

           if ($conn->query($sql) === TRUE) {
    // Redirect to Verification Page instead of just showing a message
    header("Location: verify_email.php"); 
    exit();
}
        }
        ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div>
                    <label>Full Name *</label>
                    <input type="text" name="fullname" placeholder="Enter your full name" required>
                </div>
                <div>
                    <label>ID Number *</label>
                    <input type="text" name="id_number" placeholder="Enter ID number" required>
                </div>
                <div class="full-width">
                    <label>Email *</label>
                    <input type="email" name="email" placeholder="your.email@example.com" required>
                </div>
                <div class="full-width">
                    <label>Mobile Number *</label>
                    <input type="text" name="phone" placeholder="Enter your mobile number" required>
                </div>
                <div>
                    <label>Password *</label>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>
                <div>
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" placeholder="Confirm password" required>
                </div>
            </div>
            
            <button type="submit" name="signup" class="btn-primary">Create Account</button>
            
            <div class="link-text">
                Already have an account? <a href="login.php">Sign In</a>
            </div>
        </form>
    </div>

    <div class="image-section">
        <h1>Turuqna</h1>
        <p>Traffic Congestion Reporting System</p>
    </div>
</div>

</body>
</html>