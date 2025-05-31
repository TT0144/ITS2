<?php
session_start();
$is_logged_in = isset($_SESSION['USER_ID']);
$userName = isset($_SESSION['NAME']) ? $_SESSION['NAME'] : '';
$userICON = isset($_SESSION['ICON_PATH']) ? $_SESSION['ICON_PATH'] : 'user_icon.jpg';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geocation</title>
    <link rel="stylesheet" href="./css/diary_detail.css">
</head>
<body>
    <!-- ヘッダー部分 -->
    <header>
        <div class="header-left">
            <a href="./homepage.php">
                <h1><img src="../img/logo.png" alt="Geocation ロゴ"></h1>
            </a>
            <nav>
                <ul>
                    <li class="menu-item"><a href="./spot_post.php">スポット投稿</a></li>
                    <li class="menu-item"><a href="./ranking.php">ランキング・検索</a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <?php if ($userName): ?>
                <div class="user-icon">
                <img src="<?php echo htmlspecialchars('./uploads/icons/' . $userICON); ?>" alt="User Icon" onclick="" >
                    <span><?php echo htmlspecialchars($userName); ?></span>
                    <div class="menu" onclick="toggleProfileDropdown()">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
                    </div>
            <?php else: ?>
                <a href="./login.php" class="login-button">新規登録・ログイン</a>
            <?php endif; ?>
        </div>
        <div id="profile-dropdown" class="profile-dropdown" style="display: none;">
        <div class="profile-container">
            <div class="profile-header">
            <form action="user_page.php" method="post">
                <button type="submit" name="update">投稿一覧</button>
            </form>
            <form action="logout.php" method="post">
                <button type="submit" name="logout">ログアウト</button>
            </form>
        </div>
    </div>
    </header>
    <!-- メイン画面 -->
    <main>
        <div id="content">
            <div class="image-container">
                <img id="imgpass" src="" alt="Location Image">
            </div>
            <div class="detail">
            <button id="BackButton">戻る</button> 
            </div>
        </div>
    </main>
    <script>
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    </script>
    <script src="./js/diary_detail.js"></script>
</body>
</html>