<?php
require_once __DIR__ . '/def.php';

$data = file_get_contents('php://input');
$num=json_decode($data,true);
// $raw = $num);
$spot_id = $num["imgid"]; // json形式をphp変数に変換
// $spot_id = 1;
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
  $conn = new PDO($dsn, DB_USER, DB_PASS);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  // 取得した情報を変数に格納
  $returndata = array();

  //存在確認
  $STERGOODSORT = "SELECT DIARYS_POSTING.SPOT_ID FROM DIARYS_POSTING WHERE DIARYS_POSTING.SPOT_ID = :spot_id";
  $stmt = $conn->prepare($STERGOODSORT);
  $stmt->bindParam(':spot_id', $spot_id);
  $stmt->execute();
  $count = $stmt->fetchColumn();

  if($count == 0){
  //日記がない場合
  $query = "SELECT SPOTS_POSTING.SPOTNAME,SPOTS_POSTING.COST, USERS.NAME, SPOTS_POSTING.COST, SPOTS_POSTING.ADDRESS,SPOTS_POSTING.PHOTO, SPOTS_POSTING.REMARKS FROM SPOTS_POSTING
            JOIN USERS ON SPOTS_POSTING.USER_ID = USERS.USER_ID
            WHERE SPOTS_POSTING.SPOT_ID = :spot_id";

  //メッセージ入力
  $evaluation = "まだ評価されていません";
  
  }else{
  //日記がある場合
  $query = "SELECT SPOTS_POSTING.SPOTNAME,SPOTS_POSTING.COST,DIARYS_POSTING.STERGOOD, USERS.NAME, SPOTS_POSTING.COST, SPOTS_POSTING.ADDRESS,SPOTS_POSTING.PHOTO, SPOTS_POSTING.REMARKS FROM SPOTS_POSTING
  JOIN USERS ON SPOTS_POSTING.USER_ID = USERS.USER_ID
  JOIN DIARYS_POSTING ON SPOTS_POSTING.SPOT_ID = DIARYS_POSTING.SPOT_ID 
  WHERE SPOTS_POSTING.SPOT_ID = :spot_id";
  }

  $stmt = $conn->prepare($query);
  $stmt->bindParam(':spot_id', $spot_id);
  $stmt->execute();
  $spot_detail = $stmt->fetch(PDO::FETCH_ASSOC);

  //満足度が存在した場合配列に入れる
  if(empty($evaluation)){
    $evaluation = $spot_detail['STERGOOD'];
  }

  $returndata[]=array(
  'SPOTNAME'=>$spot_detail['SPOTNAME'],
  'COST'=>$spot_detail['COST'],
  'STERGOOD'=>$evaluation,
  'NAME'=>$spot_detail['NAME'],
  'ADDRESS'=>$spot_detail['ADDRESS'],
  'PHOTO'=>$spot_detail['PHOTO'],
  'REMARKS'=>$spot_detail['REMARKS']
  );
} catch (PDOException $e) {
  // エラー処理
  die("Connection failed: " . $e->getMessage());
}

header('Content-type: application/json');
// echo($spot_id);
echo json_encode($returndata);
?>