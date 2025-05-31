<?php
session_start();
require_once __DIR__ . '/def.php';

$data = file_get_contents('php://input');
$num = json_decode($data, true);
$diary_id = $num["diary_id"];
$resgood = $num["goodnum"];

$returndata = array();
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($resgood == "" || $resgood == 2) {
        $STERGOODSORT = "SELECT diary_id FROM DIARYS_POSTING WHERE DIARYS_POSTING.diary_id = :diary_id";
        $stmt = $conn->prepare($STERGOODSORT);
        $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            echo json_encode(array("error" => "日記が存在しません"));
            exit;
        } else {
            $query = "SELECT DI.TITLE, DI.PHOTO, USERS.NAME, DI.CREATED_AT, DI.TEXT, DI.GOOD FROM DIARYS_POSTING AS DI
                      JOIN USERS ON DI.USER_ID = USERS.USER_ID
                      WHERE DI.diary_id = :diary_id";
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_STR);
        $stmt->execute();
        $diary_detail = $stmt->fetch(PDO::FETCH_ASSOC);

        // ユーザーがログインしている場合、いいねしたかどうかをチェック
        $liked = false;
        if (isset($_SESSION['USER_ID'])) {
            $user_id = $_SESSION['USER_ID'];
            $likeCheckQuery = "SELECT COUNT(*) FROM LIKES WHERE USER_ID = :user_id AND DIARY_ID = :diary_id";
            $stmt = $conn->prepare($likeCheckQuery);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_INT);
            $stmt->execute();
            $liked = $stmt->fetchColumn() > 0;
        }

        $returndata[] = array(
            'TITLE' => $diary_detail['TITLE'],
            'PHOTO' => $diary_detail['PHOTO'],
            'NAME' => $diary_detail['NAME'],
            'CREATED_AT' => $diary_detail['CREATED_AT'],
            'GOOD' => $diary_detail['GOOD'],
            'TEXT' => $diary_detail['TEXT'],
            'LIKED' => $liked
        );

    } elseif (($resgood == 1 || $resgood == -1) && isset($_SESSION['USER_ID'])) {
        $user_id = $_SESSION['USER_ID'];

        // ユーザーが既にこの日記にいいねしているかチェック
        $likeCheckQuery = "SELECT COUNT(*) FROM LIKES WHERE USER_ID = :user_id AND DIARY_ID = :diary_id";
        $stmt = $conn->prepare($likeCheckQuery);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_INT);
        $stmt->execute();
        $liked = $stmt->fetchColumn() > 0;

        if ($resgood == 1 && !$liked) {
            $UPDATE_GOOD = "UPDATE DIARYS_POSTING SET GOOD = GOOD + 1 WHERE DIARYS_POSTING.diary_id = :diary_id";
            $stmt = $conn->prepare($UPDATE_GOOD);
            $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_INT);
            $stmt->execute();

            $INSERT_LIKE = "INSERT INTO LIKES (USER_ID, DIARY_ID, CREATED_AT) VALUES (:user_id, :diary_id, NOW())";
            $stmt = $conn->prepare($INSERT_LIKE);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_INT);
            $stmt->execute();

            $returndata[] = array('1' => $resgood);
        } elseif ($resgood == -1 && $liked) {
            $UPDATE_GOOD = "UPDATE DIARYS_POSTING SET GOOD = GOOD - 1 WHERE DIARYS_POSTING.diary_id = :diary_id";
            $stmt = $conn->prepare($UPDATE_GOOD);
            $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_INT);
            $stmt->execute();

            $DELETE_LIKE = "DELETE FROM LIKES WHERE USER_ID = :user_id AND DIARY_ID = :diary_id";
            $stmt = $conn->prepare($DELETE_LIKE);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':diary_id', $diary_id, PDO::PARAM_INT);
            $stmt->execute();

            $returndata[] = array('1' => $resgood);
        }
    }
} catch (PDOException $e) {
    echo json_encode(array("error" => $e->getMessage()));
    exit;
}

header('Content-type: application/json');
echo json_encode($returndata);
?>