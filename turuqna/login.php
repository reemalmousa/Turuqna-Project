<?php
ob_start();
include 'init.php'; 
include 'db.php'; 

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'Admin') header("Location: dashboard_admin.php");
    elseif ($_SESSION['role'] == 'TrafficOfficer') header("Location: dashboard_officer.php");
    else header("Location: dashboard_citizen.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['login']; ?> - <?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .logo-img { width: 150px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto; }
    </style>
</head>
<body class="<?php echo $lang['dir']; ?>"> 

<div class="container" style="width: 700px;">
    
    <div class="image-section">
        <!-- Optional: Big Logo on the colored side too -->
        <h1 style="font-size: 80px;">🛣</h1>
        <h3><?php echo $lang['secure_login']; ?></h3>
    </div>

    <div class="form-section">
        
        <!-- Language Switcher -->
        <div style="margin-bottom: 10px; text-align: <?php echo ($lang['dir'] == 'rtl') ? 'left' : 'right'; ?>;">
            <a href="?lang=<?php echo $lang['lang_link']; ?>" style="text-decoration:none; font-weight:bold; color:#00695C; border:1px solid #00695C; padding:5px 10px; border-radius:5px;">
                 <?php echo $lang['lang_switch']; ?>
            </a>
        </div>

        <a href="index.php" class="nav-home"><span>&#8592;</span> <?php echo $lang['home']; ?></a>

        <!-- YOUR LOGO HERE -->
        <img src="images/logo.png" alt="Turuqna Logo" class="logo-img">

        <h2 style="text-align: center;"><?php echo $lang['login']; ?></h2>
        <p style="text-align: center;"><?php echo $lang['welcome']; ?></p>

        <?php
        if (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $sql = "SELECT * FROM users WHERE email='$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['name'] = $row['full_name'];
                    
                    if ($row['role'] == 'Admin') header("Location: dashboard_admin.php");
                    elseif ($row['role'] == 'TrafficOfficer') header("Location: dashboard_officer.php");
                    else header("Location: dashboard_citizen.php");
                    exit();
                } else { echo "<p style='color:red; text-align:center;'>Invalid Password</p>"; }
            } else { echo "<p style='color:red; text-align:center;'>No account found.</p>"; }
        }
        ?>

        <form method="POST" action="">
            <label><?php echo $lang['email']; ?></label>
            <input type="email" name="email" required>

            <label><?php echo $lang['password']; ?></label>
            <input type="password" name="password" required>

            <button type="submit" name="login" class="btn-primary"><?php echo $lang['login']; ?></button>

            <div class="link-text">
                <a href="signup.php"><?php echo $lang['signup']; ?></a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php ob_end_flush(); ?>