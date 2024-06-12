<?php 
require_once __DIR__ . "/def.php";

$spotsrrow = array();
$diaryarrow = array();

$data = file_get_contents('php://input');
$json_deco=json_decode($data,true);
$branch = $json_deco["branch"];
$sort_text = $json_deco["sort_text"];
// $branch = 1;
// $sort_text = "a";

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
try {
  
  if($branch == 0){
  // スポット情報を取得
  $stmtSpots = $conn->prepare("
  SELECT SPOT_ID,SPOTNAME, PHOTO, REMARKS
  FROM SPOTS_POSTING
  ORDER BY CREATED_AT DESC
  LIMIT 8
  ");
  $stmtSpots->execute();
  $popularSpots = $stmtSpots->fetchAll(PDO::FETCH_ASSOC);

  // 人気日記を取得
  $stmtDiaries = $conn->prepare("SELECT TITLE, PHOTO,DIARY_ID,TEXT FROM DIARYS_POSTING ORDER BY GOOD DESC LIMIT 8");
  $stmtDiaries->execute();
  $popularDiaries = $stmtDiaries->fetchAll(PDO::FETCH_ASSOC);

  $returndata[]=array(
    'SPOT'=>spotdatainarray($popularSpots),
    'DIARY'=>diarydatainarray($popularDiaries)
  );
  }elseif($branch == 1){
    $sql = "SELECT SPOT_ID,SPOTNAME,PHOTO,REMARKS FROM SPOTS_POSTING";
    $where = " where 1=1";

    //s
    if(!empty($sort_text)){
      $sort_textLike = "%" . $sort_text . "%";
      $where .=" AND spotname LIKE :sort_text OR remarks LIKE :sort_text";
    }

    $stmt = $conn->prepare($sql.$where);
    
    if(!empty($sort_text)){
      $stmt->bindParam(':sort_text',$sort_textLike, PDO::PARAM_STR);
    }
    $stmt->execute();
    $popularSpots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // echo($popularSpots);
    $returndata[]=array(
      'SPOT'=>spotdatainarray($popularSpots)
    );
  }elseif($branch == 2){

  // 人気日記を取得
    $stmtDiaries = $conn->prepare("SELECT TITLE, PHOTO,DIARY_ID,TEXT FROM DIARYS_POSTING ");
    $stmtDiaries->execute();
    $popularDiaries = $stmtDiaries->fetchAll(PDO::FETCH_ASSOC);
  
  $returndata[]=array(
    'DIARY'=>diarydatainarray($popularDiaries)
  );

  }else{
  //こっから↓スポットのみの文字検索（簡易版）
  $sql = "SELECT SPOT_ID,SPOTNAME,PHOTO,REMARKS FROM SPOTS_POSTING";
  $where = " where 1=1";

  if(!empty($sort_text)){
    $sort_textLike = "%" . $sort_text . "%";
    $where .=" AND spotname LIKE :sort_text OR remarks LIKE :sort_text";
  }

  $stmt = $conn->prepare($sql.$where);
  
  if(!empty($sort_text)){
    $stmt->bindParam(':sort_text',$sort_textLike, PDO::PARAM_STR);
  }
  $stmt->execute();
  $popularSpots = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // echo($popularSpots);
  $returndata[]=array(
    'SPOT'=>spotdatainarray($popularSpots)
  );
  }
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

function spotdatainarray($popularSpots){
  //データベースから出力したものを配列に格納
  foreach($popularSpots as $value){
    $spotsrrow[]=array(
      'PHOTO'=>$value['PHOTO'],
      'SPOTNAME'=>$value['SPOTNAME'],
      'SPOT_ID'=>$value['SPOT_ID'],
      'REMARKS'=>$value['REMARKS']
    );
  }
  return $spotsrrow;

}

function diarydatainarray($popularDiaries){
  foreach($popularDiaries as $value){
    $diaryarrow[]=array(
      'TITLE'=>$value['TITLE'],
      'PHOTO'=>$value['PHOTO'],
      'DIARY_ID'=>$value['DIARY_ID'],
      'TEXT'=>$value['TEXT']
    );
  }
  return $diaryarrow;
}


//JSに返す
header('Content-type: application/json');
echo json_encode($returndata);


?>
