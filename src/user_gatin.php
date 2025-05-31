<?php
session_start();

if (!isset($_SESSION['USER_ID'])) {
    die(json_encode(['error' => 'User not logged in']));
}

echo json_encode(['user_id' => $_SESSION['USER_ID']]);
?>
