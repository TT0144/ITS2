<?php
session_start();

// ログインセッションの確認
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$userName = isset($_SESSION['NAME']) ? $_SESSION['NAME'] : '';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geolocation ホームページ</title>
    <link rel="stylesheet" href="../css/user_page.css">
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
                    <li class="menu-item"><a href="./diary_post.php">アカウント設定</a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <a href="./logout.php" class="logout-button">ログアウト</a>
        </div>
    </header>
    <!-- メイン画面 -->
    <main>
        <h1><span><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>さん</h1>
        <div id="section-item">
            <div class="section-header">
                <h2>投稿スポット一覧</h2>
                <a href="./diary_post.php" class="more-link">もっと見る</a>
            </div>
            <section id="spot-posts"></section>

            <div class="section-header">
                <h2>日記一覧</h2>
                <a href="./spot_post.php" class="more-link">もっと見る</a>
            </div>
            <section id="diary-posts"></section>
            
        </div>
        <a href="./homepage.php" class="BackButton">戻る</a>
    </main>
    <footer>
        <img src="../img/logo.png" alt="ロゴ" width="250">
        <small>Copyright &copy;2023 Geocation. All Rights Reserved.</small>
    </footer>
    <script src="../js/user_page.js"></script>
</body>
</html>
