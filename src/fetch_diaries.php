<?php
require_once __DIR__ . '/def.php';

// POSTデータからspot_idを取得
$data = file_get_contents('php://input');
$request = json_decode($data, true);
$spot_id = isset($request['spot_id']) ? intval($request['spot_id']) : null;

// spot_idが無効な場合
if (empty($spot_id)) {
    echo json_encode([]);
    exit();
}

// DB接続
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // spot_idに基づいてデータを取得
    $stmt = $conn->prepare("SELECT TITLE, DIARY_ID, TEXT, COALESCE(PHOTO, '../img/noimage.png') AS PHOTO 
    FROM DIARYS_POSTING 
    WHERE SPOT_ID = :spot_id ORDER BY CREATED_AT DESC");
    $stmt->bindParam(':spot_id', $spot_id, PDO::PARAM_INT);
    $stmt->execute();
    $diaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $returndata[] = array(
        'DIARY' => diarydatainarray($diaries)
    );

    echo json_encode($returndata);
} catch (PDOException $e) {
    echo json_encode([]);
}

function diarydatainarray($popularDiaries)
{
    foreach ($popularDiaries as $value) {
        $diaryarrow[] = array(
            'TITLE' => $value['TITLE'],
            'PHOTO' => $value['PHOTO'],
            'DIARY_ID' => $value['DIARY_ID'],
            'TEXT' => $value['TEXT']
        );
    }
    return $diaryarrow;
}

?>