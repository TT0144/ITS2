<?php
session_start();
require_once __DIR__ . '/def.php';

// ログインを確認する
if (!isset($_SESSION['NAME'])) {
    header("Location: ./login.php");
    exit;
}

// POST データから spot_id を取得する
$data = file_get_contents('php://input');
$num = json_decode($data, true);
$spot_id = $num["imgid"];

// PDO 接続を作成する
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // トランザクションを開始する
    $conn->beginTransaction();

    // LIKES テーブルから対応するエントリを削除する
    $query1 = "DELETE LIKES FROM LIKES 
               JOIN DIARYS_POSTING ON LIKES.DIARY_ID = DIARYS_POSTING.DIARY_ID 
               WHERE DIARYS_POSTING.SPOT_ID = :spot_id";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bindParam(':spot_id', $spot_id);
    $stmt1->execute();

    // DIARYS_POSTING テーブルから対応するエントリを削除する
    $query2 = "DELETE FROM DIARYS_POSTING WHERE SPOT_ID = :spot_id";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':spot_id', $spot_id);
    $stmt2->execute();

    // SPOTS_POSTING テーブルから投稿を削除する
    $query3 = "DELETE FROM SPOTS_POSTING WHERE SPOT_ID = :spot_id";
    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':spot_id', $spot_id);
    $stmt3->execute();

    // 削除された行数を確認する
    $rowCount = $stmt3->rowCount();

    // トランザクションをコミットする
    $conn->commit();

    // 削除が成功したかどうかを判定する
    if ($rowCount > 0) {
        echo json_encode(array("message" => "投稿を削除しました"));
    } else {
        echo json_encode(array("message" => "削除する投稿が見つかりませんでした"));
    }
} catch (PDOException $e) {
    // エラーが発生した場合、トランザクションをロールバックする
    $conn->rollBack();
    echo json_encode(array("message" => "エラー: " . $e->getMessage()));
}
?>
