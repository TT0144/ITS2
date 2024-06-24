<?php 
require_once __DIR__ . "/def.php";

$spotsrrow = array();
$diaryarrow = array();

$data = file_get_contents('php://input');
$json_deco=json_decode($data,true);
$branch = $json_deco["branch"];
$sort_text = $json_deco["sort_text"];
$radiovalue = $json_deco["radiovalue"]; // ラジオボタンの値を受け取る
// $branch = 0;
// $sort_text = "あ";
// $radiovalue = 4;

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
try {
  
  if($branch == 0){
    //ホームページの表示SQL
    if($radiovalue == ""){
      // スポット情報を取得(JS側でforeachで回す都合上昇順で出す)
      $stmtSpots = $conn->prepare("
      SELECT SPOT_ID,SPOTNAME, PHOTO, REMARKS
      FROM SPOTS_POSTING
      ORDER BY CREATED_AT DESC
      LIMIT 8
      ");
      $stmtSpots->execute();
      $popularSpots = $stmtSpots->fetchAll(PDO::FETCH_ASSOC);

      // 人気日記を取得(JS側でforeachで回す都合上昇順で出す)
      $stmtDiaries = $conn->prepare("
      SELECT TITLE, PHOTO,DIARY_ID,TEXT
      FROM DIARYS_POSTING
      ORDER BY GOOD DESC
      LIMIT 8
      ");
      $stmtDiaries->execute();
      $popularDiaries = $stmtDiaries->fetchAll(PDO::FETCH_ASSOC);

      $returndata[]=array(
        'SPOT'=>spotdatainarray($popularSpots),
        'DIARY'=>diarydatainarray($popularDiaries)
      );
      //配列を逆順にして降順に表示する
      $returndata[0]['SPOT'] = array_reverse($returndata[0]['SPOT']);
      $returndata[0]['DIARY'] = array_reverse($returndata[0]['DIARY']);

    //ランキングのすべて状態ソート
    }else{
      //スポット検索文字
      $spot_sql = "SELECT SPOT_ID,SPOTNAME,PHOTO,REMARKS FROM SPOTS_POSTING";
      $spot_sort_textLike = "%" . $sort_text . "%";
      $spot_where =" where spotname LIKE :sort_text OR remarks LIKE :sort_text";

      if ($radiovalue == 3) {
        $spot_order = " ORDER BY CREATED_AT DESC";
      } else{
        $spot_order = " ORDER BY CREATED_AT ASC";
      }

      $stmt = $conn->prepare($spot_sql.$spot_where.$spot_order);
      $stmt->bindParam(':sort_text',$spot_sort_textLike, PDO::PARAM_STR);
      $stmt->execute();
      $count = $stmt->fetchColumn();

      //検索したSPOTが存在するなら
      if(!$count == 0){
        $stmt->execute();
        $popularSpots = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }

      //日記検索文字
      $diary_sql = "SELECT TITLE, PHOTO, DIARY_ID, TEXT FROM DIARYS_POSTING";
      $diary_sort_textLike = "%" . $sort_text . "%";
      $diary_where = " where (title LIKE :sort_text OR text LIKE :sort_text)";

      if ($radiovalue == 3) {
        $diary_order = " ORDER BY GOOD DESC";
      } else{
        $diary_order = " ORDER BY GOOD ASC";
      }
      $stmtv2 = $conn->prepare($diary_sql.$diary_where.$diary_order);
      $stmtv2->bindParam(':sort_text',$diary_sort_textLike, PDO::PARAM_STR);
      $stmtv2->execute();
      $countv2 = $stmtv2->fetchColumn();

      //検索したSPOTが存在するなら
      if(!$countv2 == 0){
        $stmtv2->execute();
        $popularDiaries = $stmtv2->fetchAll(PDO::FETCH_ASSOC);
      }
      if(!$count == 0 && !$countv2 == 0){
        $returndata[]=array(
          'SPOT'=>spotdatainarray($popularSpots),
          'DIARY' => diarydatainarray($popularDiaries)
        );
      }elseif(!$count == 0){
        $returndata[]=array(
          'SPOT'=>spotdatainarray($popularSpots)
        );
      }elseif(!$countv2 == 0){
        $returndata[]=array(
          'DIARY' => diarydatainarray($popularDiaries)
        );
      }

    }
  
    //スポットのみそ0と
  }elseif($branch == 1){
    $sql = "SELECT SPOT_ID,SPOTNAME,PHOTO,REMARKS FROM SPOTS_POSTING";
    $where = " where 1=1";

    //空白判定
    if(!empty($sort_text)){
      $sort_textLike = "%" . $sort_text . "%";
      $where .=" AND spotname LIKE :sort_text OR remarks LIKE :sort_text";
    }
     // ラジオボタンの値に基づいてソート条件を追加
    if ($radiovalue == 1) {
      $order = " ORDER BY CREATED_AT DESC";
    } elseif ($radiovalue == 2) {
      $order = " ORDER BY CREATED_AT ASC";
    } elseif($radiovalue == 3) {
      $order = " ORDER BY CREATED_AT DESC"; // デフォルトのソート順
    }else{
      $order = " ORDER BY CREATED_AT ASC";
    }

    $stmt = $conn->prepare($sql.$where.$order);
    
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

    $sql = "SELECT TITLE, PHOTO, DIARY_ID, TEXT FROM DIARYS_POSTING";
    $where = " WHERE 1=1";

    if (!empty($sort_text)) {
        $sort_textLike = "%" . $sort_text . "%";
        $where .= " AND (title LIKE :sort_text OR text LIKE :sort_text)";
    }

    // ラジオボタンの値に基づいてソート条件を追加
    if ($radiovalue == 1) {
      $order = " ORDER BY GOOD DESC";
    } elseif ($radiovalue == 2) {
      $order = " ORDER BY GOOD ASC";
    } elseif ($radiovalue == 3) {
      $order = " ORDER BY CREATED_AT ASC";
    }else{
      $order = " ORDER BY CREATED_AT DESC";
    }

    $stmt = $conn->prepare($sql . $where . $order);
    
    if (!empty($sort_text)) {
        $stmt->bindParam(':sort_text', $sort_textLike, PDO::PARAM_STR);
    }
    $stmt->execute();
    $popularDiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $returndata[] = array(
        'DIARY' => diarydatainarray($popularDiaries)
    );

  }else{
  //こっから拡張する場合のSQL
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
