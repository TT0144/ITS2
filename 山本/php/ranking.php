<?php 
// require_once __DIR__ . "/def.php";

// // 接続情報
// $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// // データベース接続を確立し、エラーモードを設定
// try {
//     $conn = new PDO($dsn, DB_USER, DB_PASS);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//     // 初期表示用のクエリ
//     $sql = "SELECT 'spot' AS type, SPOTNAME AS title, CONCAT('../uploads/', PHOTO) AS photo, REMARKS AS text, CREATED_AT 
//             FROM SPOTS_POSTING 
//             UNION 
//             SELECT 'diary' AS type, TITLE AS title, CONCAT('../uploads/', PHOTO) AS photo, TEXT AS text, CREATED_AT 
//             FROM DIARYS_POSTING 
//             ORDER BY CREATED_AT DESC";
//     $stmt = $conn->prepare($sql);
//     $stmt->execute();
//     $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// } catch (PDOException $e) {
//     die("Connection failed: " . $e->getMessage());
// }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ランキング・検索</title>
  <link rel="stylesheet" href="../css/ranking.css">
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

<main>
    <div id="split">
        <div id="split-child">
            <div id="button-group">
                <button class="buttonsec" id="search-button">検索</button>
                <button class="buttonsec" id="spot-ranking">スポットランキング</button>
                <button class="buttonsec" id="diary-ranking">日記ランキング</button>
            </div>
        </div>
        <div id="listitem">
            <div id="ranking-select">
                <select name="sort" id="selectsort">
                    <option value="0">すべて</option>
                    <option value="1">スポット</option>
                    <option value="2">日記</option>
                </select>
                <input type="text" id="searchbox" placeholder="検索キーワードを入力">
                <div id="searchicon">
                    <img src="../img/虫眼鏡の無料アイコン8.png" alt="虫眼鏡アイコン" width="20" height="21">
                </div>
            </div>
            <div id="results">
                <!-- <?php foreach ($results as $result): ?>
                    <div class="resultitem">
                        <div class="itemimg">
                            <img src="<?= htmlspecialchars($result['photo']) ?>" alt="<?= htmlspecialchars($result['title']) ?>"width="200" height="210">
                        </div>
                        <div class="item-explan">
                            <div class="list-title"><?= htmlspecialchars($result['title']) ?></div>
                            <div><?= htmlspecialchars($result['text']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?> -->
            </div>
        </div>
    </div>
</main>

<script src="../js/rannking.js"></script>

</body>
</html>
