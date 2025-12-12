<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password - Turuqna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="width: 600px;">
    <div class="form-section">
        
        <a href="login.php" class="nav-home"><span>&#8592;</span> Back to Login</a>

        <h2>Reset Password</h2>
        <p>Enter your email to receive a reset link.</p>

        <form onsubmit="event.preventDefault(); alert('If this email exists, a reset link has been sent!');">
            <label>Email Address</label>
            <input type="email" placeholder="name@example.com" required>

            <button type="submit" class="btn-primary">Send Reset Link</button>
        </form>
    </div>
</div>

</body>
</html>