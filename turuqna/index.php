<?php
include 'init.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>

    <!-- Main Style -->
    <link rel="stylesheet" href="style.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
</head>

<body class="<?php echo $lang['dir']; ?>">

<div class="app-hero">

    <!-- ================= TOP HEADER ================= -->
    <div class="app-header">

        <!-- Menu Icon -->
        <div style="font-size:24px; cursor:pointer;">☰</div>

        <!-- Navigation Links -->
        <div class="nav-links">
            <a href="about.php"><?php echo $lang['about']; ?></a>
            <a href="contact.php"><?php echo $lang['contact']; ?></a>
            <a href="help.php"><?php echo $lang['help_faq']; ?></a>
        </div>

        <!-- Language Switch -->
        <a href="?lang=<?php echo $lang['lang_link']; ?>" class="lang-btn">
            <?php echo $lang['lang_switch']; ?>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
            </svg>
        </a>

    </div>
    <!-- ================= END HEADER ================= -->


    <!-- ================= CENTER CONTENT ================= -->
    <div class="app-center">

        <!-- Logo -->
        <img src="images/index_logo.png" alt="Turuqna" class="app-logo">

        <!-- Welcome -->
        <h1><?php echo $lang['welcome_title']; ?></h1>
        <p><?php echo $lang['system_desc']; ?></p>

        <!-- Action Buttons -->
        <div class="app-buttons">
            <a href="login.php" class="btn-white"><?php echo $lang['login']; ?></a>
            <a href="signup.php" class="btn-outline"><?php echo $lang['signup']; ?></a>
        </div>

    </div>
    <!-- ================= END CENTER ================= -->


    <!-- ================= FOOTER ICONS ================= -->
    <div class="app-footer">

        <div class="footer-item">
            <div class="icon-circle-outline">👥</div>
            <span class="footer-text"><?php echo $lang['community']; ?></span>
        </div>

        <div class="footer-item">
            <div class="icon-circle-outline">📍</div>
            <span class="footer-text"><?php echo $lang['live_updates']; ?></span>
        </div>

        <div class="footer-item">
            <div class="icon-circle-outline">❗</div>
            <span class="footer-text"><?php echo $lang['reporting']; ?></span>
        </div>

    </div>
    <!-- ================= END FOOTER ================= -->


    <!-- Copyright -->
    <div class="copyright">
        <?php echo $lang['copyright']; ?>
    </div>

</div>

</body>
</html>
