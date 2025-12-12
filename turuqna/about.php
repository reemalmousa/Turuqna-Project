<?php include 'init.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <title><?php echo $lang['about']; ?> - <?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="<?php echo $lang['dir']; ?>" style="background-color: #f4f6f8;">

    <!-- TOP BAR -->
    <div class="sub-header">
        <a href="index.php" class="back-link">
            <span>&#8592;</span> <?php echo $lang['home']; ?>
        </a>
        <h2><?php echo $lang['about']; ?></h2>
        <a href="?lang=<?php echo $lang['lang_link']; ?>" class="sub-lang">
            🌐 <?php echo $lang['lang_switch']; ?>
        </a>
    </div>

    <!-- CONTENT -->
    <div class="container" style="margin-top: 30px; flex-direction: column; width: 90%; max-width: 800px; padding: 40px;">
        <img src="images/logo.png" style="width: 100px; margin: 0 auto 20px auto; display: block;">
        
        <h1 style="text-align: center; color: #00695C;"><?php echo $lang['title']; ?></h1>
        
        <p style="text-align: center; line-height: 1.8; color: #555; font-size: 16px;">
            <?php echo $lang['about_text']; ?>
        </p>

        <div style="background: #e0f2f1; padding: 20px; border-radius: 10px; margin-top: 30px;">
            <h3 style="color: #00695C; margin-top: 0;"><?php echo $lang['vision']; ?></h3>
            <p style="margin-bottom: 0; color: #333;"><?php echo $lang['vision_text']; ?></p>
        </div>
    </div>

</body>
</html>