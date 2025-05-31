<?php 
require_once __DIR__ . "/def.php";

// セッションの開始とユーザーIDの確認
// session_start();
// if (!isset($_SESSION['USER_ID'])) {
//     die(json_encode(['error' => 'User not logged in']));
// }


$data = file_get_contents('php://input');
$json_deco=json_decode($data,true);

$user_id = $json_deco['user_id'];
// $user_id = "1";

// データベース接続設定
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Connection failed']));
}

try {
    // ユーザーが投稿したスポット情報を取得
    $stmtSpots = $conn->prepare("
    SELECT SPOT_ID, SPOTNAME, PHOTO, REMARKS
    FROM SPOTS_POSTING
    WHERE USER_ID = :user_id
    ORDER BY CREATED_AT DESC
    ");
    $stmtSpots->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtSpots->execute();
    $userSpots = $stmtSpots->fetchAll(PDO::FETCH_ASSOC);

    // ユーザーが投稿した日記を取得
    $stmtDiaries = $conn->prepare("
    SELECT TITLE, PHOTO, DIARY_ID, TEXT
    FROM DIARYS_POSTING
    WHERE USER_ID = :user_id
    ORDER BY CREATED_AT DESC
    ");
    $stmtDiaries->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtDiaries->execute();
    $userDiaries = $stmtDiaries->fetchAll(PDO::FETCH_ASSOC);

    $returndata = array(
      'SPOT' => spotdatainarray($userSpots),
      'DIARY' => diarydatainarray($userDiaries),
      'USER_ID' => $user_id  // ユーザーIDを返す
    );

} catch (PDOException $e) {
    die(json_encode(['error' => 'Query failed']));
}

function spotdatainarray($spots) {
  $spotsrrow = array();
  foreach($spots as $value) {
    $spotsrrow[] = array(
      'PHOTO' => $value['PHOTO'],
      'SPOTNAME' => $value['SPOTNAME'],
      'SPOT_ID' => $value['SPOT_ID'],
      'REMARKS' => $value['REMARKS']
    );
  }
  return $spotsrrow;
}

function diarydatainarray($diaries) {
  $diaryarrow = array();
  foreach($diaries as $value) {
    $diaryarrow[] = array(
      'TITLE' => $value['TITLE'],
      'PHOTO' => $value['PHOTO'],
      'DIARY_ID' => $value['DIARY_ID'],
      'TEXT' => $value['TEXT']
    );
  }
  return $diaryarrow;
}

// JSON形式で返す
header('Content-type: application/json');
echo json_encode($returndata);

?>
