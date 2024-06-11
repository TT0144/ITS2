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
    
        // パスワードの確認
        if ($password !== $confirm_password) {
            die("パスワードが一致しません");
        }
    
        // パスワードをハッシュ化
        $hashed_password = hash('sha256', $password);
    
        // 新規ユーザー登録
        $sql = "INSERT INTO USERS (NAME, ADDRESS, PASSWORD, CREATED_AT, UPDATED_AT) VALUES (:username, :email, :password, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
    
        if ($stmt->execute()) {
            // 新規登録が完了したら login.php にリダイレクト
            header("Location: login.php");
            exit();

        } else {
            echo "登録に失敗しました";
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
        <form action="signup.php" method="post">
        <input type="text" name="username" class="SignupUsername" placeholder="アカウント名" required />
          <input type="email" name="email" class="UserEmail" placeholder="メールアドレス" required />
          <input type="password" name="password" class="SignupPassword" placeholder="パスワード" required />
          <input type="password" name="confirm_password" class="ConfirmPassword" placeholder="パスワード再確認" required />
          <button type="submit" class="SigninButton">新規作成</button>
        </form>
      </div>
    </div>
  </body>
</html>