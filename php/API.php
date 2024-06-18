<?php 
require_once __DIR__ . "/def.php"; // 定義ファイルを読み込む

$spotsrrow = array(); // スポット情報を格納する配列
$diaryarrow = array(); // 日記情報を格納する配列

$data = file_get_contents('php://input'); // POSTデータを取得
$json_deco = json_decode($data, true); // JSONデータを連想配列に変換
$branch = $json_deco["branch"]; // ブランチの種類（0: スポットと日記, 1: スポットのみ, 2: 日記のみ, それ以外: 簡易版スポット検索）
$sort_text = $json_deco["sort_text"]; // 検索テキスト
$radiovalue = $json_deco["radiovalue"]; // ラジオボタンの値を受け取る

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET; // データベース接続用DSN
try {
    $conn = new PDO($dsn, DB_USER, DB_PASS); // PDOインスタンスを生成
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを設定
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage()); // 接続エラー時の処理
}

try {
  
  if($branch == 0){
    $where = " WHERE 1=1"; // 条件句の初期化
    // ラジオボタンの値に基づいてソート条件を追加
    if ($radiovalue == 3) {
      $order = " ORDER BY CREATED_AT DESC";
    } elseif ($radiovalue == 4) {
      $order = " ORDER BY CREATED_AT ASC";
    } else {
      $order = " ORDER BY CREATED_AT DESC"; // デフォルトのソート順
    }
    
    // スポット情報を取得（8件まで）
    $stmtSpots = $conn->prepare("
    SELECT SPOT_ID, SPOTNAME, PHOTO, REMARKS
    FROM SPOTS_POSTING
    " .$order);
    $stmtSpots->execute(); // クエリ実行
    $popularSpots = $stmtSpots->fetchAll(PDO::FETCH_ASSOC); // 結果を配列で取得

    // 人気日記を取得（8件まで）
    $stmtDiaries = $conn->prepare("
    SELECT TITLE, PHOTO, DIARY_ID, TEXT
    FROM DIARYS_POSTING
    " .$order);
    $stmtDiaries->execute(); // クエリ実行
    $popularDiaries = $stmtDiaries->fetchAll(PDO::FETCH_ASSOC); // 結果を配列で取得
    
    // 返却データにスポットと日記の情報を格納
    $returndata[] = array(
      'SPOT' => spotdatainarray($popularSpots), // スポット情報を配列化して格納
      'DIARY' => diarydatainarray($popularDiaries) // 日記情報を配列化して格納
    );
    
  } elseif($branch == 1){
    // スポットのみを取得
    $sql = "SELECT SPOT_ID, SPOTNAME, PHOTO, REMARKS FROM SPOTS_POSTING";
    $where = " WHERE 1=1"; // 条件句の初期化

    // 検索テキストがあれば条件を追加
    if(!empty($sort_text)){
      $sort_textLike = "%" . $sort_text . "%";
      $where .= " AND spotname LIKE :sort_text OR remarks LIKE :sort_text";
    }

    // ラジオボタンの値に基づいてソート条件を追加
    if ($radiovalue == 1) {
      $order = " ORDER BY CREATED_AT DESC";
    } elseif ($radiovalue == 2) {
      $order = " ORDER BY CREATED_AT ASC";
    } elseif ($radiovalue == 3) { 
      $order = " ORDER BY CREATED_AT DESC"; // デフォルトのソート順
    }elseif ($radiovalue == 4) {
      $order = " ORDER BY CREATED_AT ASC";

    }

    // SQL文を準備して実行
    $stmt = $conn->prepare($sql . $where . $order);
    
    if(!empty($sort_text)){
      $stmt->bindParam(':sort_text', $sort_textLike, PDO::PARAM_STR);
    }
    $stmt->execute(); // クエリ実行
    $popularSpots = $stmt->fetchAll(PDO::FETCH_ASSOC); // 結果を配列で取得

    // 返却データにスポット情報を格納
    $returndata[] = array(
      'SPOT' => spotdatainarray($popularSpots) // スポット情報を配列化して格納
    );
    
  } elseif($branch == 2){
    // 日記のみを取得
    $sql = "SELECT TITLE, PHOTO, DIARY_ID, TEXT FROM DIARYS_POSTING";
    $where = " WHERE 1=1"; // 条件句の初期化

    // 検索テキストがあれば条件を追加
    if (!empty($sort_text)) {
      $sort_textLike = "%" . $sort_text . "%";
      $where .= " AND title LIKE :sort_text OR text LIKE :sort_text";
    }

    // ラジオボタンの値に基づいてソート条件を追加
    if ($radiovalue == 1) {
      $order = " ORDER BY CREATED_AT DESC";
    } elseif ($radiovalue == 2) {
      $order = " ORDER BY CREATED_AT ASC";
    } else {
      $order = " ORDER BY CREATED_AT DESC"; // デフォルトのソート順
    }

    // SQL文を準備して実行
    $stmt = $conn->prepare($sql . $where . $order);
    
    if (!empty($sort_text)) {
      $stmt->bindParam(':sort_text', $sort_textLike, PDO::PARAM_STR);
    }
    $stmt->execute(); // クエリ実行
    $popularDiaries = $stmt->fetchAll(PDO::FETCH_ASSOC); // 結果を配列で取得

    // 返却データに日記情報を格納
    $returndata[] = array(
      'DIARY' => diarydatainarray($popularDiaries) // 日記情報を配列化して格納
    );

  } else {
    // スポットのみの簡易検索（簡易版）
    $sql = "SELECT SPOT_ID, SPOTNAME, PHOTO, REMARKS FROM SPOTS_POSTING";
    $where = " WHERE 1=1"; // 条件句の初期化
    $order = " ORDER BY CREATED_AT DESC"; // デフォルトのソート順

    // 検索テキストがあれば条件を追加
    if(!empty($sort_text)){
      $sort_textLike = "%" . $sort_text . "%";
      $where .= " AND spotname LIKE :sort_text OR remarks LIKE :sort_text";
    }

    // ラジオボタンの値に基づいてソート条件を追加
    if ($radiovalue == 3) {
      $order = " ORDER BY CREATED_AT DESC";
    } elseif ($radiovalue == 4) {
      $order = " ORDER BY CREATED_AT ASC";
    } else {
      $order = " ORDER BY CREATED_AT DESC"; // デフォルトのソート順
    }

    // SQL文を準備して実行
    $stmt = $conn->prepare($sql . $where . $order);
    
    if(!empty($sort_text)){
      $stmt->bindParam(':sort_text', $sort_textLike, PDO::PARAM_STR);
    }
    $stmt->execute(); // クエリ実行
    $popularSpots = $stmt->fetchAll(PDO::FETCH_ASSOC); // 結果を配列で取得

    // 返却データにスポット情報を格納
    $returndata[] = array(
      'SPOT' => spotdatainarray($popularSpots) // スポット情報を配列化して格納
    );
  }
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage()); // エラー時の処理
}

// スポット情報を配列に格納する関数
function spotdatainarray($popularSpots){
  $spotsrrow = array(); // スポット情報を格納する配列
  foreach($popularSpots as $value){
    // 配列にデータを追加
    $spotsrrow[] = array(
      'PHOTO' => $value['PHOTO'],
      'SPOTNAME' => $value['SPOTNAME'],
      'SPOT_ID' => $value['SPOT_ID'],
      'REMARKS' => $value['REMARKS']
    );
  }
  return $spotsrrow; // スポット情報が格納された配列を返す
}

// 日記情報を配列に格納する関数
function diarydatainarray($popularDiaries){
  $diaryarrow = array(); // 日記情報を格納する配列
  foreach($popularDiaries as $value){
    // 配列にデータを追加
    $diaryarrow[] = array(
      'TITLE' => $value['TITLE'],
      'PHOTO' => $value['PHOTO'],
      'DIARY_ID' => $value['DIARY_ID'],
      'TEXT' => $value['TEXT']
    );
  }
  return $diaryarrow; // 日記情報が格納された配列を返す
}

// JSに返すためにデータをJSON形式に変換して出力
header('Content-type: application/json');
echo json_encode($returndata); // 返却データをJSON形式に変換して出力

?>