<?php
session_start();
require_once 'def.php';

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['USER_ID'])) {
    header('Location: login.php');
    exit();
}

// 送信されたデータを処理する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // エラーメッセージを格納する変数
    $errors = [];

    // フォームデータの取得とバリデーション
    $title = trim($_POST['title']);
    $text = trim($_POST['text']);
    $rating = floatval($_POST['rating']);

    if (empty($title)) {
        $errors[] = 'タイトルを入力してください。';
    }

    if (empty($text)) {
        $errors[] = 'テキストを入力してください。';
    }

    if (empty($rating)) {
        $errors[] = '満足度を選択してください。';
    }

    // ファイルアップロードを処理
    $photoPath = NULL;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $targetDir = "../uploads/";
        $fileName = basename($_FILES["photo"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // 許可されたファイル形式かどうかを確認
        $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
        if (in_array($fileType, $allowTypes)) {
            // ファイルをサーバにアップロード
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                $photoPath = $fileName;
            } else {
                $errors[] = "ファイルのアップロード中にエラーが発生しました。";
            }
        } else {
            $errors[] = "JPG, JPEG, PNG, GIFファイルのみアップロード可能です。";
        }
    }

    // エラーがない場合、データベースに保存する
    if (empty($errors)) {
        // データベースに接続
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // 接続エラーのチェック
        if ($mysqli->connect_errno) {
            echo 'データベース接続エラー: ' . $mysqli->connect_error;
            exit();
        }

        // データベースに挿入する準備
        $stmt = $mysqli->prepare("INSERT INTO DIARYS_POSTING (USER_ID, TITLE, PHOTO, TEXT, STERGOOD, CREATED_AT, UPDATED_AT) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");

        // ログインしているユーザーのIDを使用
        $userId = $_SESSION['USER_ID'];
        $stmt->bind_param('isssd', $userId, $title, $photoPath, $text, $rating);

        // クエリを実行
        if ($stmt->execute()) {
            echo '日記の投稿が完了しました。';
        } else {
            echo 'データベースエラー: ' . $stmt->error;
        }

        // ステートメントと接続を閉じる
        $stmt->close();
        $mysqli->close();
    } else {
        // エラーメッセージを表示する
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geocation - 日記投稿</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap">
    <link rel="stylesheet" href="../css/diary_post.css">
</head>
<body>
    <!-- ヘッダー部分 -->
    <header>
        <div class="header-left">
            <a href="./homepage.php">
                <h1><img src="../img/logo.png" alt="Geocation ロゴ"></h1>
            </a>
            <nav>
                <ul>
                    <li class="menu-item"><a href="./diary_post.php">日記投稿</a></li>
                    <li class="menu-item"><a href="./spot_post.php">スポット投稿</a></li>
                    <li class="menu-item"><a href="./ranking.php">ランキング・検索</a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <a href="./login.php" class="login-button">新規登録・ログイン</a>
        </div>
    </header>
    <!-- メインコンテンツ -->
    <main>
            <form method="post" enctype="multipart/form-data">
            <h1>日記投稿</h1>
            <div class="container">
                <div class="left-section">
                    <label for="photo">写真を選択</label>
                    <div class="photo-group">
                        <input type="file" id="photo" name="photo" accept="image/*" required>
                        <label for="photo" id="photo-label"><p>写真を選択してください。</p></label>
                        <div id="photo-preview"></div>
                    </div>
                    <div class="rating-group">
                        <label for="rating">満足度：</label>
                        <div class="rating">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" id="rating" name="rating" value="5">
                    </div>
                </div>
                <div class="right-section">
                    <label for="title">タイトル</label>
                    <div class="form-group">
                        <input type="text" class="title" id="title" name="title" required>
                    </div>
                    <label for="text">テキスト</label>
                    <div class="form-group">
                        <textarea id="text" name="text"></textarea>
                    </div>

                </div>
                </div>
                <div class="button-group">
                        <a href="./homepage.php" class="BackButton">戻る</a>
                        <button type="submit" class="addcomp">投稿完了</button>
                    </div>
 
            </form>
 
    </main>
    <script src="../js/diary_post.js"></script>
</body>
</html>
