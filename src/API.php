<?php 
require_once __DIR__ . "/def.php";

$spotsrrow = array();
$diaryarrow = array();

$data = file_get_contents('php://input');
$json_deco=json_decode($data,true);
$branch = $json_deco["branch"];
$sort_text = $json_deco["sort_text"];
$radiovalue = $json_deco["radiovalue"]; // ラジオボタンの値を受け取る
// $branch = 3;
// $sort_text = '';
// $radiovalue = 5; // ラジオボタンの値を受け取る

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
       //スポットのランダムで8つ取得
      $randomSpots = $conn->prepare("
      SELECT s.SPOT_ID, s.SPOTNAME, s.PHOTO, s.REMARKS, IFNULL(AVG(d.STERGOOD), 0) AS STERGOOD
        FROM SPOTS_POSTING s
        LEFT JOIN DIARYS_POSTING d ON s.SPOT_ID = d.SPOT_ID
        GROUP BY s.SPOT_ID, s.SPOTNAME, s.PHOTO, s.REMARKS
        ORDER BY RAND() DESC
        LIMIT 5
      ");
      $randomSpots->execute();
      $rndSpots = $randomSpots->fetchAll(PDO::FETCH_ASSOC);

      // スポット情報を取得(JS側でforeachで回す都合上昇順で出す)
      $stmtSpots = $conn->prepare("
      SELECT s.SPOT_ID, s.SPOTNAME, s.PHOTO, s.REMARKS, IFNULL(AVG(d.STERGOOD), 0) AS STERGOOD
        FROM SPOTS_POSTING s
        LEFT JOIN DIARYS_POSTING d ON s.SPOT_ID = d.SPOT_ID
        GROUP BY s.SPOT_ID, s.SPOTNAME, s.PHOTO, s.REMARKS
        ORDER BY STERGOOD DESC
        LIMIT 5
      ");
      $stmtSpots->execute();
      $popularSpots = $stmtSpots->fetchAll(PDO::FETCH_ASSOC);

      // 人気日記を取得(JS側でforeachで回す都合上昇順で出す)
      $stmtDiaries = $conn->prepare("
      SELECT TITLE, PHOTO,DIARY_ID,TEXT
      FROM DIARYS_POSTING
      ORDER BY GOOD DESC
      LIMIT 5
      ");
      $stmtDiaries->execute();
      $popularDiaries = $stmtDiaries->fetchAll(PDO::FETCH_ASSOC);

      $returndata[]=array(
        'RANDOM'=>spotdatainarray($rndSpots),
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
    $sql = "
        SELECT s.SPOT_ID, s.SPOTNAME, s.PHOTO, s.REMARKS, IFNULL(AVG(d.STERGOOD), 0) AS AVG_STERGOOD
        FROM SPOTS_POSTING s
        LEFT JOIN DIARYS_POSTING d ON s.SPOT_ID = d.SPOT_ID
        WHERE 1=1";

    // 空白判定
    if (!empty($sort_text)) {
        $sort_textLike = "%" . $sort_text . "%";
        $sql .= " AND (s.SPOTNAME LIKE :sort_text OR s.REMARKS LIKE :sort_text)";
    }
    
    
    // ラジオボタンの値に基づいてソート条件を追加
    if ($radiovalue == 1) {
      $order = " ORDER BY AVG_STERGOOD DESC";
    } elseif ($radiovalue == 2) {
      $order = " ORDER BY AVG_STERGOOD ASC";
    } elseif($radiovalue == 3) {
      $order = " ORDER BY AVG_STERGOOD DESC"; // デフォルトのソート順
    }else{
      $order = " ORDER BY AVG_STERGOOD ASC";
    }
    $sql .= " GROUP BY s.SPOT_ID, s.SPOTNAME, s.PHOTO, s.REMARKS" . $order;
    $stmt = $conn->prepare($sql);
    
    if (!empty($sort_text)) {
        $stmt->bindParam(':sort_text', $sort_textLike, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $popularSpots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 結果を配列に格納
    $returndata[] = array(
        'SPOT' => spotdatainarray($popularSpots)
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

  }elseif($branch == 3){
  //地域別表示処理
  switch($radiovalue){
    case 1:
      $regions = array('北海道');
      break;
    case 2:
      $regions = array('青森','岩手','秋田','宮城','山形','福島','新潟');
      break;
    case 5:
      $regions = array(
        '茨城', '栃木', '群馬', '山梨', '長野', '埼玉', '千葉', '東京', '神奈川');
      break;
    case 6:
      $regions = array(
        '静岡', '岐阜', '愛知', '三重');
      break;
    case 7:
      $regions = array(
        '富山', '石川', '福井'
    );
      break;
    case 8:
      $regions = array(
        '滋賀', '京都', '奈良', '和歌山', '大阪', '兵庫'
    );
      break;
    case 9:
      $regions = array(
        '鳥取', '島根', '岡山', '広島', '山口'
    );
      break;
    case 10:
      $regions = array(
        '徳島', '香川', '愛媛', '高知'
    );
      break;
    case 11:
      $regions = array(
        '福岡', '佐賀', '長崎', '大分', '熊本', '宮崎', '鹿児島'
    );
      break;
    case 12:
      $regions = array(
        '沖縄'
    );
      break;
    }
    //sql整形処理
    $sql = "SELECT s.SPOT_ID, s.SPOTNAME, s.PHOTO, s.REMARKS FROM SPOTS_POSTING s";
    $where = " WHERE 1=1";
    $prefectureLike = "";
    foreach($regions as $prefecture){
      $prefectureLike .= " OR s.ADDRESS LIKE '%" . $prefecture . "%' ";
    }
    // echo($prefectureLike);

  // WHERE句に最初の条件を追加するための修正
  if ($prefectureLike) {
      $where .= " AND (1=0" . $prefectureLike . ")";
  }

    // SQL文を準備
    $stmt = $conn->prepare($sql . $where);
    // echo (prepare($sql . $where . $prefectureLike));
    $stmt->execute();
    $popularSpots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $returndata[] = array(
      'SPOT' => spotdatainarray($popularSpots)
    );

        // 結果を配列に格納


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