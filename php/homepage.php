<?php
session_start();
require_once __DIR__ . "/def.php";

// ログインセッションの確認
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

// データベース接続
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // スポット情報を取得
    $stmtSpots = $conn->prepare("
        SELECT SPOTNAME, PHOTO, REMARKS
        FROM SPOTS_POSTING
        ORDER BY CREATED_AT DESC
        LIMIT 8
    ");
    $stmtSpots->execute();
    $popularSpots = $stmtSpots->fetchAll(PDO::FETCH_ASSOC);

    // 人気日記を取得
    $stmtDiaries = $conn->prepare("SELECT TITLE, PHOTO, TEXT FROM DIARYS_POSTING ORDER BY GOOD DESC LIMIT 8");
    $stmtDiaries->execute();
    $popularDiaries = $stmtDiaries->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
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
                <h1><img src="../image/logo.png" alt="Geocation ロゴ"></h1>
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
        <div class="section-header">
            <h2>オススメの観光地</h2>
            <a class="more-link" href="./ranking.php">もっと見る</a>
        </div>
        <section id="spot-reco">
            <?php foreach ($popularSpots as $spot): ?>
                <div class="spot">
                    <img src="../uploads/<?php echo htmlspecialchars($spot['PHOTO']); ?>" alt="<?php echo htmlspecialchars($spot['SPOTNAME']); ?>">
                    <h2><?php echo htmlspecialchars($spot['SPOTNAME']); ?></h2>
                    <p><?php echo htmlspecialchars($spot['REMARKS']); ?></p>
                </div>
            <?php endforeach; ?>
        </section>

        <div class="section-header">
            <h2>スポットの人気ランキング</h2>
            <a class="more-link" href="./ranking.php">もっと見る</a>
        </div>
        <section id="spot-rank">
            <?php foreach ($popularSpots as $spot): ?>
                <div class="spot">
                    <img src="../uploads/<?php echo htmlspecialchars($spot['PHOTO']); ?>" alt="<?php echo htmlspecialchars($spot['SPOTNAME']); ?>">
                    <h2><?php echo htmlspecialchars($spot['SPOTNAME']); ?></h2>
                    <p><?php echo htmlspecialchars($spot['REMARKS']); ?></p>
                </div>
            <?php endforeach; ?>
        </section>

        <div class="section-header">
            <h2>日記人気ランキング</h2>
            <a class="more-link" href="./ranking.php">もっと見る</a>
        </div>
        <section id="diary-rank">
            <?php foreach ($popularDiaries as $diary): ?>
                <div class="diary">
                    <img src="../uploads/<?php echo htmlspecialchars($diary['PHOTO']); ?>" alt="<?php echo htmlspecialchars($diary['TITLE']); ?>">
                    <h2><?php echo htmlspecialchars($diary['TITLE']); ?></h2>
                    <p><?php echo htmlspecialchars($diary['TEXT']); ?></p>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <footer>
        <img src="../image/logo.png" alt="ロゴ" width="250">
        <small>Copyright &copy;2023 Geocation . All Rights Reserved.</small>
    </footer>
</body>
</html>
