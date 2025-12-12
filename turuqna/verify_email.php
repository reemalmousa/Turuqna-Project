<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify Email - Turuqna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="width: 600px;">
    <div class="form-section" style="text-align: center;">
        
        <div style="font-size: 50px; margin-bottom: 20px;">✉️</div>
        <h2>Verify Your Email</h2>
        <p>We sent a 6-digit code to your email. Please enter it below.</p>

        <form onsubmit="event.preventDefault(); alert('Account Verified! Logging you in...'); window.location.href='dashboard_citizen.php';">
            
            <input type="number" placeholder="123456" style="text-align: center; letter-spacing: 5px; font-size: 20px;" required>

            <button type="submit" class="btn-primary">Verify Account</button>
            
            <p style="margin-top: 20px; font-size: 13px;">
                Didn't receive code? <a href="#" style="color: #00695C;">Resend</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>