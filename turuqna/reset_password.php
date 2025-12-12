<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password - Turuqna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="width: 600px;">
    <div class="form-section">
        
        <h2>Create New Password</h2>
        <p>Please enter a strong password.</p>

        <form onsubmit="event.preventDefault(); alert('Password has been reset successfully! Redirecting to login...'); window.location.href='login.php';">
            
            <label>New Password</label>
            <input type="password" placeholder="********" required>

            <label>Confirm Password</label>
            <input type="password" placeholder="********" required>

            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
    </div>
</div>

</body>
</html>