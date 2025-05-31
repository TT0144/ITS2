<?php
session_start();
require_once __DIR__ . "/def.php";

$error = '';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // パスワードをハッシュ化
    $hashed_password = hash('sha256', $password);

    $sql = "SELECT USER_ID, NAME, ICON_PATH FROM USERS WHERE NAME=:username AND PASSWORD=:password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // ユーザーが存在する場合
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['USER_ID'] = $row['USER_ID'];
        $_SESSION['NAME'] = $row['NAME'];
        $_SESSION['ICON_PATH'] = $row['ICON_PATH']; // アイコンパスをセッションに保存
        header("Location: homepage.php");
        exit();
    } else {
        // ユーザーが存在しない場合
        $error = "無効なユーザー名またはパスワードです";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="../css/login.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var error = "<?php echo $error; ?>";
            if (error) {
                alert(error);
            }
        });
    </script>
</head>
<body>
    <div class="login">
        <a href="./homepage.php"><i class="icono-arrow2"></i></a>
        <div class="LoginContainer">
            <h1>ログイン</h1>
            <form action="login.php" method="post">
                <input type="text" name="username" class="LoginUsername" placeholder="アカウント名" required />
                <input type="password" name="password" class="LoginPassword" placeholder="パスワード" required />
                <button type="submit" class="LoginButton">ログイン</button>
            </form>
            <a href="./signup.php" class="SignupButton">新規登録こちら</a> 
        </div>
    </div>
</body>
</html>
