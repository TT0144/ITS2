<?php
require_once __DIR__ . '/def.php';

$data = file_get_contents('php://input');
$num = json_decode($data, true);
$spot_id = $num["imgid"];

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $returndata = array();

    $query = "
    SELECT SPOTS_POSTING.SPOTNAME, SPOTS_POSTING.COST, USERS.NAME, SPOTS_POSTING.COST, 
           SPOTS_POSTING.ADDRESS, SPOTS_POSTING.PHOTO, SPOTS_POSTING.REMARKS, 
           IFNULL(ROUND(AVG(DIARYS_POSTING.STERGOOD), 1), 'まだ評価されていません') AS AVG_STERGOOD
    FROM SPOTS_POSTING
    LEFT JOIN USERS ON SPOTS_POSTING.USER_ID = USERS.USER_ID
    LEFT JOIN DIARYS_POSTING ON SPOTS_POSTING.SPOT_ID = DIARYS_POSTING.SPOT_ID
    WHERE SPOTS_POSTING.SPOT_ID = :spot_id
    GROUP BY SPOTS_POSTING.SPOT_ID";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':spot_id', $spot_id);
    $stmt->execute();
    $spot_detail = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($spot_detail) {
      $avg_stergood = $spot_detail['AVG_STERGOOD'];
    if ($avg_stergood === 0.0) {
        $avg_stergood = 'まだ評価されていません';
        $returndata[] = array(
            'SPOTNAME' => $spot_detail['SPOTNAME'],
            'COST' => $spot_detail['COST'],
            'AVG_STERGOOD' => floatval($avg_stergood), // 小数点以下を含む数値としてキャスト
            'NAME' => $spot_detail['NAME'],
            'ADDRESS' => $spot_detail['ADDRESS'],
            'PHOTO' => $spot_detail['PHOTO'],
            'REMARKS' => $spot_detail['REMARKS']
        );
      }else{
        $returndata[] = array(
          'SPOTNAME' => $spot_detail['SPOTNAME'],
          'COST' => $spot_detail['COST'],
          'AVG_STERGOOD' => floatval($avg_stergood), // 小数点以下を含む数値としてキャスト
          'NAME' => $spot_detail['NAME'],
          'ADDRESS' => $spot_detail['ADDRESS'],
          'PHOTO' => $spot_detail['PHOTO'],
          'REMARKS' => $spot_detail['REMARKS']
      );
      }
    } else {
        $returndata[] = array('error' => 'データが見つかりませんでした');
    }
} catch (PDOException $e) {
    $returndata[] = array('error' => '接続に失敗しました: ' . $e->getMessage());
}

header('Content-type: application/json');
echo json_encode($returndata);
?>
