<?php

session_start();
require_once "db_connect.php";


$timeoutSeconds = 30 * 60; // 30 minutes

if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $timeoutSeconds)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION["last_activity"] = time();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}


$activityStmt = $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
$activityStmt->execute([$_SESSION["user_id"]]);
?>
