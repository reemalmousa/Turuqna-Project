<?php include 'init.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <title><?php echo $lang['contact']; ?> - <?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="<?php echo $lang['dir']; ?>" style="background-color: #f4f6f8;">

    <!-- TOP BAR -->
    <div class="sub-header">
        <a href="index.php" class="back-link">
            <span>&#8592;</span> <?php echo $lang['home']; ?>
        </a>
        <h2><?php echo $lang['contact']; ?></h2>
        <a href="?lang=<?php echo $lang['lang_link']; ?>" class="sub-lang">
            🌐 <?php echo $lang['lang_switch']; ?>
        </a>
    </div>

    <!-- CONTENT -->
    <div class="container" style="margin-top: 30px; flex-direction: column; width: 90%; max-width: 600px; padding: 40px;">
        <h2 style="text-align: center; color: #00695C; margin-bottom: 20px;"><?php echo $lang['contact']; ?></h2>
        
        <form onsubmit="event.preventDefault(); alert('<?php echo $lang['contact_success']; ?>');">
            <label><?php echo $lang['name']; ?></label>
            <input type="text" required>
            
            <label><?php echo $lang['email']; ?></label>
            <input type="email" required>
            
            <label><?php echo $lang['message']; ?></label>
            <textarea rows="5" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;"></textarea>
            
            <button class="btn-primary" style="margin-top: 20px;">
                <?php echo $lang['send_message']; ?>
            </button>
        </form>

        <p style="text-align: center; color: #888; font-size: 13px; margin-top: 20px;">
            <?php echo $lang['support_text']; ?> <br> support@turuqna.com
        </p>
    </div>

</body>
</html>