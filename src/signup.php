<?php
require_once __DIR__ . "/def.php";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $icon_path = null;
    
        // パスワードの確認
        if ($password !== $confirm_password) {
            die("パスワードが一致しません");
        }
    
        // パスワードをハッシュ化
        $hashed_password = hash('sha256', $password);
        
        // アイコン画像のアップロード処理
        if (isset($_FILES['icon']) && $_FILES['icon']['error'] == 0) {
            $targetDir = "./uploads/icons/";
            $fileName = basename($_FILES["icon"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // 許可されたファイル形式かどうかを確認
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif','webp','jfif'];
            if (in_array($fileType, $allowTypes)) {
                // ファイルをサーバにアップロード
                if (move_uploaded_file($_FILES["icon"]["tmp_name"], $targetFilePath)) {
                    $icon_path = $fileName;
                } else {
                    die("ファイルのアップロード中にエラーが発生しました。");
                }
            } else {
                die("JPG, JPEG, PNG, GIFファイルのみアップロード可能です。");
            }
        } else {
            die("ファイルのアップロードに失敗しました。エラーコード：" . $_FILES['icon']['error']);
        }

        // 新規ユーザー登録
        $sql = "INSERT INTO USERS (NAME, ADDRESS, PASSWORD, ICON_PATH, CREATED_AT, UPDATED_AT) VALUES (:username, :email, :password, :icon_path, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':icon_path', $icon_path);
    
        if ($stmt->execute()) {
            // 新規登録が完了したら login.php にリダイレクト
            header("Location: login.php");
            exit();
        } else {
            die("登録に失敗しました");
        }
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>サインイン画面</title>
    <link rel="stylesheet" href="../css/login.css" />
</head>
<body>
  <a href="./login.html" ><i class="icono-arrow2"></i></i> </a>
    <div class="signup">
      <div class="SignupContainer">
        <h1>新規登録</h1>
        <form action="signup.php" method="post" enctype="multipart/form-data">
        <div class="photo-group">
               <input type="file" id="icon" name="icon" accept="image/*" class="IconUpload" />
              <label for="icon" id="photo-label"><p>写真を選択してください。</p></label>
              <div id="photo-preview"></div>
          </div>
          <input type="text" name="username" class="SignupUsername" placeholder="アカウント名" required />
          <input type="email" name="email" class="UserEmail" placeholder="メールアドレス" required />
          <input type="password" name="password" class="SignupPassword" placeholder="パスワード" required />
          <input type="password" name="confirm_password" class="ConfirmPassword" placeholder="パスワード再確認" required />
          <button type="submit" class="SigninButton">新規作成</button>
      </form>
      </div>
    </div>
    <script src="./js/signup.js"></script>
  </body>
</html>
