<?php include 'init.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <title><?php echo $lang['help']; ?> - <?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .faq-item { border-bottom: 1px solid #eee; padding: 15px 0; }
        .faq-question { font-weight: bold; color: #15356A; cursor: pointer; display: flex; justify-content: space-between; }
        .faq-answer { display: none; margin-top: 10px; color: #555; line-height: 1.6; }
        .faq-item.active .faq-answer { display: block; }
    </style>
    <script>
        function toggleFaq(el) { el.parentElement.classList.toggle('active'); }
    </script>
</head>
<body class="<?php echo $lang['dir']; ?>" style="background-color: #f4f6f8;">

    <!-- TOP BAR -->
    <div class="sub-header">
        <a href="index.php" class="back-link">
            <span>&#8592;</span> <?php echo $lang['home']; ?>
        </a>
        <h2><?php echo $lang['help']; ?></h2>
        <a href="?lang=<?php echo $lang['lang_link']; ?>" class="sub-lang">
            🌐 <?php echo $lang['lang_switch']; ?>
        </a>
    </div>

    <!-- CONTENT -->
    <div class="container" style="margin-top: 30px; flex-direction: column; width: 90%; max-width: 800px; padding: 40px;">
        <h2 style="text-align: center; color: #00695C; margin-bottom: 30px;"><?php echo $lang['faq_title']; ?></h2>

        <!-- Q1 -->
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <?php echo $lang['faq_q1']; ?> <span>▼</span>
            </div>
            <div class="faq-answer"><?php echo $lang['faq_a1']; ?></div>
        </div>

        <!-- Q2 -->
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <?php echo $lang['faq_q2']; ?> <span>▼</span>
            </div>
            <div class="faq-answer"><?php echo $lang['faq_a2']; ?></div>
        </div>

        <!-- Q3 -->
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <?php echo $lang['faq_q3']; ?> <span>▼</span>
            </div>
            <div class="faq-answer"><?php echo $lang['faq_a3']; ?></div>
        </div>
    </div>

</body>
</html>