<?php
session_start();
// require_once __DIR__ . "/def.php";

// ログインセッションの確認
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geolocation ホームページ</title>
    <link rel="stylesheet" href="../css/homepage.css">
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
                    <li class="menu-item"><a href="./diary_post.php">日記投稿</a></li>
                    <li class="menu-item"><a href="./spot_post.php">スポット投稿</a></li>
                    <li class="menu-item"><a href="./ranking.php">ランキング・検索</a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <a href="./login.php" class="login-button">新規登録・ログイン</a>
        </div>
    </header>
    <!-- メイン画面 -->
    <main>
      <div id=section-item>
        <div class="section-header">
            <h2>オススメの観光地</h2>
            <div id = "more_recommendation" class="more-link" >もっと見る</div>
        </div>
        <section id="spot-reco">
        </section>

        <div class="section-header">
            <h2>スポットの人気ランキング</h2>
            <div id = "more_spotrank" class="more-link" >もっと見る</div>
        </div>
        <section id="spot-rank">
        </section>
        <div class="section-header">
            <h2>日記人気ランキング</h2>
            <div id = "more_diaryrank" class="more-link" >もっと見る</div>
        </div>
        <section id="diary-rank">
        </section>
        </div>
    </main>
    <footer>
        <img src="../img/logo.png" alt="ロゴ" width="250">
        <small>Copyright &copy;2023 Geocation . All Rights Reserved.</small>
    </footer>
    <script src="../js/homepage.js"></script>
</body>
</html>
