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
    <title>ランキング・検索</title>
    <link rel="stylesheet" href="./css/ranking.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
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
                    <img src="<?php echo htmlspecialchars('./uploads/icons/' . $userICON); ?>" alt="User Icon" onclick="">
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

    <main>
        <div id="split">
            <div id="split-child">
                <div id="button-group">
                    <button class="buttonsec" id="search-button">検索</button>
                    <button class="buttonsec" id="spot-ranking">スポットランキング</button>
                    <button class="buttonsec" id="diary-ranking">日記ランキング</button>
                    <button class="buttonsec" id="location_button">地域別スポット</button>
                </div>
            </div>
            <div id="listitem">
                <div id="ranking-select">
                    <select name="sort" id="selectsort">
                        <option value="0">すべて</option>
                        <option value="1">スポット</option>
                        <option value="2">日記</option>
                    </select>
                    <input type="text" id="searchbox" placeholder="検索キーワードを入力" value="">
                    <div id="searchicon">
                        <img src="../img/虫眼鏡の無料アイコン8.png" alt="虫眼鏡アイコン" width="20" height="21">
                    </div>
                </div>
                <form name="radiobottonitem" id="radioboxitem">
                </form>
                <div id="results">
                    <div id="diary-content"></div>
                </div>
            </div>
        </div>
    </main>
    <script>
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    </script>
    <script src="./js/ranking.js"></script>
</body>

</html>