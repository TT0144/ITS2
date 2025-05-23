<!-- <?php

require_once __DIR__ . '/def.php';
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $query = "SELECT SPOTS_POSTING.SPOTNAME, DIARYS_POSTING.STERGOOD, USER.NAME, SPOTS_POSTING.COST, SPOTS_POSTING.ADDRESS, SPOTS_POSTING.REMARKS FROM SPOTS_POSTING
            JOIN USER ON SPOTS_POSTING.USER_ID = USER.ID 
            JOIN DIARYS_POSTING ON SPOTS_POSTING.SPOT_ID = DIARYS_POSTING.SPOT_ID 
            WHERE SPOTS_POSTING.SPOT_ID = :spot_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':spot_id', $spot_id);
    $stmt->execute();
    $spot_detail = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 取得した情報を変数に格納
$spot_name = $spot_detail['SPOTNAME'];
$satisfaction = $spot_detail['STERGOOD'];
$registered_by = $spot_detail['NAME'];
$cost = $spot_detail['COST'];
$address = $spot_detail['ADDRESS'];
$remarks = $spot_detail['REMARKS'];
} catch (PDOException $e) {
    // エラー処理
}
?> -->

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geocation - スポット詳細</title>
    <link rel="stylesheet" href="../css/spot_detail.css">
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
    <!-- メインコンテンツ -->
    <main>
        <div class="content">
            <div class="image-container">
                <!-- ここにスポットの画像を表示するためのコードを追加 -->
                <img id = "imgpass" src="" alt="Location Image">
            </div>
            <div class="detail">
            </div>
        </div>
        <div class="buttons">
            <a href="./ranking.php" class="diary-view-btn">日記を見る</a>
            <a href="./diary_post.php" class="diary-post-btn">日記投稿をする</a>
            
        </div>
    </main>
    <script src="../js/spot_detail.js"></script>

</body>
</html>
