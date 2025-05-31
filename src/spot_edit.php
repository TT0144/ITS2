<?php
session_start();
require_once __DIR__ . '/def.php';

$is_logged_in = isset($_SESSION['USER_ID']);
$userName = isset($_SESSION['NAME']) ? $_SESSION['NAME'] : '';
$userICON = isset($_SESSION['ICON_PATH']) ? $_SESSION['ICON_PATH'] : 'user_icon.jpg';
// ログインしているかどうかを確認
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

// エラーメッセージや成功メッセージを格納する変数を初期化
$errors = [];
$successMessage = "";

// 編集対象のスポットIDを取得
$spotId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($spotId > 0) {
    // データベース接続情報
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    try {
        // データベース接続を確立し、エラーモードを設定
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // スポット情報を取得
        $stmt = $conn->prepare("SELECT * FROM SPOTS_POSTING WHERE SPOT_ID = ? AND USER_ID = ?");
        $stmt->execute([$spotId, $_SESSION['USER_ID']]);
        $spot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$spot) {
            $errors[] = "指定されたスポットが見つかりません。";
        }
    } catch (PDOException $e) {
        $errors[] = "データベース接続に失敗しました: " . $e->getMessage();
    }
}

// フォームが送信されたかどうかをチェック
if ($_SERVER["REQUEST_METHOD"] === "POST" && $spotId > 0) {
    try {
        // データベース接続を確立し、エラーモードを設定
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 入力データを準備し、サニタイズする
        $title = htmlspecialchars($_POST['titlename'], ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars($_POST['addressname'], ENT_QUOTES, 'UTF-8');
        $cost = htmlspecialchars($_POST['cost'], ENT_QUOTES, 'UTF-8');
        $remarks = htmlspecialchars($_POST['memo'], ENT_QUOTES, 'UTF-8');
        $userId = $_SESSION['USER_ID']; // セッションからユーザーIDを取得

        // ファイルアップロードを処理
        $fileName = $spot['PHOTO']; // 既存のファイル名をデフォルトに設定
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $targetDir = "./uploads/";
            $fileName = basename($_FILES["photo"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // 許可されたファイル形式かどうかを確認
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
            if (in_array($fileType, $allowTypes)) {
                // ファイルをサーバにアップロード
                if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                    $errors[] = "ファイルのアップロード中にエラーが発生しました。";
                }
            } else {
                $errors[] = "JPG, JPEG, PNG, GIFファイルのみアップロード可能です。";
            }
        }

        if (empty($errors)) {
            // データベースのエントリを更新
            $stmt = $conn->prepare("
                UPDATE SPOTS_POSTING 
                SET SPOTNAME = ?, ADDRESS = ?, COST = ?, REMARKS = ?, PHOTO = ?, UPDATED_AT = NOW() 
                WHERE SPOT_ID = ? AND USER_ID = ?
            ");
            $stmt->execute([$title, $address, $cost, $remarks, $fileName, $spotId, $userId]);

            // spot_detail.phpにリダイレクトする際、spot_idをGETパラメーターとして渡す
            header("Location: spot_detail.php?id=$spotId");
            exit(); // リダイレクト後にスクリプトの実行を終了する
        }
    } catch (PDOException $e) {
        $errors[] = "データベース接続に失敗しました: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スポット編集画面</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap">
    <link rel="stylesheet" href="./css/spot_edit.css" />
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
    <main>
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif ($successMessage): ?>
            <div class="success-message">
                <p><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>
            <form action="spot_edit.php?id=<?php echo htmlspecialchars($spotId, ENT_QUOTES, 'UTF-8'); ?>" method="post" enctype="multipart/form-data">
                <h1>スポット編集</h1>
                <div class="container">
                    <div class="left-section">
                    <label for="photo">写真を選択</label>
                    <div class="photo-group">
                        <input type="file" id="photo" name="photo" accept="image/*" required>
                        <label for="photo" id="photo-label"></label>
                        <div id="photo-preview">
                        <?php if (!empty($spot['PHOTO'])): ?>
                                    <img src="./uploads/<?php echo htmlspecialchars($spot['PHOTO'], ENT_QUOTES, 'UTF-8'); ?>" alt="Current Photo" id="current-photo">
                                <?php endif; ?>
                        </div>
                        </div>
                    </div>
                    <div class="right-section">
                        <label for="title">タイトル</label>
                        <div class="form-group">
                            <input type="text" class="titlename" id="titlename" name="titlename" value="<?php echo htmlspecialchars($spot['SPOTNAME'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <label for="address">住所</label>
                        <div class="form-group">
                            <input type="text" class="addressname" id="addressname" name="addressname" value="<?php echo htmlspecialchars($spot['ADDRESS'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <label for="cost">費用</label>
                        <div class="form-group">
                            <input type="number" id="cost" name="cost" min="0" step="0.01" value="<?php echo htmlspecialchars($spot['COST'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <label for="memo">備考</label>
                        <div class="form-group">
                            <textarea id="memo" name="memo"><?php echo htmlspecialchars($spot['REMARKS'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="button-group">
                    <a href="./homepage.php" class="BackButton">戻る</a>
                    <button type="submit" class="decisionButton">決定</button>
                </div>
            </form>
    </main>
    <script>
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    </script>
    <script src="./js/spot_edit.js"></script>
</body>
</html>
