<?php
require_once __DIR__ . '/def.php';

$data = file_get_contents('php://input');
$num=json_decode($data,true);
$diary_id = $num["diary_id"]; // json形式をphp変数に変    換
$resgood = $num["goodnum"];
// $resgood = "";
// $diary_id = 5; // json形式をphp変数に変換

// 取得した情報を変数に格納
$returndata = array();

// $returndata[]=array(
//   'diary_id'=>$diary_id,
//   'resgood'=>$resgood
// );


$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
  $conn = new PDO($dsn, DB_USER, DB_PASS);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if($resgood == "" || $resgood == 2){
    //存在確認
    $STERGOODSORT = "SELECT diary_id FROM DIARYS_POSTING WHERE DIARYS_POSTING.diary_id = :diary_id";
    $stmt = $conn->prepare($STERGOODSORT);
    $stmt->bindParam(':diary_id', $diary_id,PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if($count == 0){
    //日記がない場合
    //メッセージ入力
      echo("日記が存在しません");
    
    }else{
      // 日記がある場合
      $query = "SELECT DI.TITLE,DI.PHOTO,USERS.NAME,DI.CREATED_AT,DI.TEXT,DI.GOOD FROM DIARYS_POSTING AS DI
      JOIN USERS ON DI.USER_ID = USERS.USER_ID
      WHERE DI.diary_id = :diary_id";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':diary_id', $diary_id,PDO::PARAM_STR);
    $stmt->execute();
    $diary_detail = $stmt->fetch(PDO::FETCH_ASSOC);

    $returndata[]=array(
    'TITLE'=>$diary_detail['TITLE'],
    'PHOTO'=>$diary_detail['PHOTO'],
    'NAME'=>$diary_detail['NAME'],
    'CREATED_AT'=>$diary_detail['CREATED_AT'],
    'GOOD'=>$diary_detail['GOOD'],
    'TEXT'=>$diary_detail['TEXT']
    );
  }elseif($resgood == 1){
    $UPDATE_GOOD = "UPDATE DIARYS_POSTING SET GOOD = GOOD+1  WHERE DIARYS_POSTING.diary_id = :diary_id";
    $stmt = $conn->prepare($UPDATE_GOOD);
    $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_STR);
    $stmt->execute();

    $returndata[]=array(
      '1'=>$resgood
    );
  }
} catch (PDOException $e) {
  // エラー処理
  die("Connection failed: " . $e->getMessage());
}

header('Content-type: application/json');
// echo($diary_id);
echo json_encode($returndata);
?>