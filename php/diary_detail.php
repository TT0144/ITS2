<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geocation</title>
    <link rel="stylesheet" href="../css/diary_detail.css">
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
            <div id="content">
                <div class="image-container">
                  <img id="imgpass" src="" alt="Location Image">
                </div>
                <div class="detail">
                </div>
            </div>
            <button class="BackButton">戻る</button> 
        </main>
  <script src="../js/diary_detail.js"></script>
</body>
</html>
