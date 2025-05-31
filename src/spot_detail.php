<?php
session_start();
$userName = isset($_SESSION['NAME']) ? $_SESSION['NAME'] : '';
$userId = isset($_SESSION['NAME']) ? $_SESSION['NAME'] : '';
$userICON = isset($_SESSION['ICON_PATH']) ? $_SESSION['ICON_PATH'] : 'user_icon.jpg';

require_once __DIR__ . '/def.php';
$user_id = " res[0]['USER_ID']";
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geocation - スポット詳細</title>
    <link rel="stylesheet" href="./css/spot_detail.css">
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
    <!-- メインコンテンツ -->
    <main>
        <div class="content">
        <div id="edit-delete-buttons" style="display: none;">
        <a href=""><img id="edit_button" src="./img/edit.png" alt="edit button"></a>
        <a href="./diary_post.php"><img id = "remove_button" src="./img/remove.png" alt="remove button"></a><!-- 削除ボタン追加する -->
    </div>
            <div class="image-container">
                <img id="imgpass" src="" alt="Location Image">
            </div>
            <div class="detail"></div>
        </div>
        <div class="buttons">
            <a href="javascript:void(0);" class="diary-view-btn">日記を見る</a>
            <a href="javascript:void(0);" class="diary-post-btn">日記投稿をする</a>
        </div>
    </main>
    <script>
        const loggedInUserId = "<?php echo htmlspecialchars($userId); ?>";
    </script>
    <script src="./js/spot_detail.js"></script>
</body>
</html>
