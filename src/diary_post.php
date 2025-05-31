<?php
session_start();
$is_logged_in = isset($_SESSION['USER_ID']);
require_once 'def.php';
$userName = isset($_SESSION['NAME']) ? $_SESSION['NAME'] : '';

$userICON = isset($_SESSION['ICON_PATH']) ? $_SESSION['ICON_PATH'] : 'user_icon.jpg';
// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['USER_ID'])) {
    header('Location: login.php');
    exit();
}

// URLパラメータからspot_idを取得
$spot_id = isset($_GET['spot_id']) ? intval($_GET['spot_id']) : null;

// 送信されたデータを処理する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // エラーメッセージを格納する変数
    $errors = [];

    // フォームデータの取得とバリデーション
    $title = trim($_POST['title']);
    $text = trim($_POST['text']);
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

    if (empty($title)) {
        $errors[] = 'タイトルを入力してください。';
    }

    if (empty($text)) {
        $errors[] = 'テキストを入力してください。';
    }

    if ($rating < 1 || $rating > 5) {
        $errors[] = '満足度を選択してください。';
    }

    if (empty($spot_id)) {
        $errors[] = 'スポットIDが無効です。';
    }

    // ファイルアップロードを処理
    $photoPath = NULL;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $targetDir = "./uploads/";
        $fileName = basename($_FILES["photo"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // 許可されたファイル形式かどうかを確認
        $allowTypes = ['jpg', 'png', 'jpeg', 'gif','HEIC'];
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
        $stmt = $mysqli->prepare("INSERT INTO DIARYS_POSTING (USER_ID, SPOT_ID, TITLE, PHOTO, TEXT, STERGOOD, CREATED_AT, UPDATED_AT) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");

        // ログインしているユーザーのIDを使用
        $userId = $_SESSION['USER_ID'];
        $stmt->bind_param('iisssd', $userId, $spot_id, $title, $photoPath, $text, $rating);

        // クエリを実行
        if ($stmt->execute()) {
            // 挿入したレコードのIDを取得
            $diary_id = $stmt->insert_id;

            // 日記詳細ページにリダイレクト
            header("Location: diary_detail.php?id={$diary_id}");
            exit();
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
    <link rel="stylesheet" href="./css/diary_post.css">
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
                    <li class="menu-item"><a href="./spot_post.php">スポット投稿</a></li>
                    <li class="menu-item"><a href="./ranking.php">ランキング・検索</a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <?php if ($userName): ?>
                <div class="user-icon">
                <img src="<?php echo htmlspecialchars('./uploads/icons/' . $userICON); ?>" alt="User Icon" onclick="" >
                    <span><?php echo htmlspecialchars($userName); ?></span>
                    <div class="menu" onclick="toggleProfileDropdown()">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
                    </div>
            <?php else: ?>
                <a href="./login.php" class="login-button">新規登録・ログイン</a>
            <?php endif; ?>
        </div>
        <div id="profile-dropdown" class="profile-dropdown" style="display: none;">
        <div class="profile-container">
            <div class="profile-header">
            <form action="user_page.php" method="post">
                <button type="submit" name="update">投稿一覧</button>
            </form>
            <form action="logout.php" method="post">
                <button type="submit" name="logout">ログアウト</button>
            </form>
        </div>
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
                        <input type="file" id="photo" name="photo" accept="image/*">
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
                        <input type="hidden" id="rating" name="rating" value="">
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
    <script>
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    </script>
    <script src="./js/diary_post.js"></script>
</body>
</html>
