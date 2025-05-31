<?php
session_start();


// // デバッグ用にセッション変数を表示
// echo '<pre>';
// echo 'SESSION DATA:<br>';
// print_r($_SESSION);
// echo '</pre>';
$userName = isset($_SESSION['NAME']) ? $_SESSION['NAME'] : '';
$userICON = isset($_SESSION['ICON_PATH']) ? $_SESSION['ICON_PATH'] : 'user_icon.jpg';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geolocation ホームページ</title>
    <link rel="stylesheet" href="./css/homepage.css">
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
            <form action="updateprofile.php" method="post">
                <button type="submit" name="update">プロフィール変更</button>
            </form>
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
    <div class="back_img parallax-bg"></div>

    <main>

    <div id=section-item>
        <div class="section-header">
            <h2>オススメの観光地</h2>
        </div>
        <div class="slideshow-container">
                <section id="spot-reco" class="slideshow"></section>
            </div>
        </section>

        <div class="section-header">
            <h2>スポットの人気ランキング</h2>
            <div id = "more_spotrank" class="more-link" >もっと見る</div>
        </div>
        <div class="slideshow-container">
                <section id="spot-rank" class="slideshow"></section>
            </div>
        </section>
        <div class="section-header">
            <h2>日記人気ランキング</h2>
            <div id = "more_diaryrank" class="more-link" >もっと見る</div>
        </div>
        <div class="slideshow-container">
                <section id="diary-rank" class="slideshow"></section>
            </div>
        </section>
        </div>
    </main>
    <footer>
        <img src="./img/logo.png" alt="ロゴ" width="250">
        <small>Copyright &copy;2023 Geocation . All Rights Reserved.</small>
    </footer>
    <script>
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    </script>
    <script src="./js/homepage.js"></script>
</body>
</html>