<?php 
require_once __DIR__ . "/def.php";

// 接続情報
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// データベース接続を確立し、エラーモードを設定
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_GET['type'] == 'spot') {
        $sql = "SELECT SPOTNAME AS title, PHOTO, REMARKS AS text 
                FROM SPOTS_POSTING 
                ORDER BY  CREATED_AT DESC";
    } elseif ($_GET['type'] == 'diary') {
        $sql = "SELECT TITLE AS title, PHOTO, TEXT AS text 
                FROM DIARYS_POSTING 
                ORDER BY  CREATED_AT DESC";
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
        // 画像パスの直接埋め込み
        echo '<div class="resultitem">
                <div class="itemimg">
                    <img src="../uploads/' . $result['PHOTO'] . '" alt="' . htmlspecialchars($result['title']) . '">
                </div>
                <div class="item-explan">
                    <div class="list-title">' . htmlspecialchars($result['title']) . '</div>
                    <div>' . htmlspecialchars($result['text']) . '</div>
                </div>
            </div>';
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
