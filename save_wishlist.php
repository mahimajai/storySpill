<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$bookId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($bookId > 0) {
    $checkStmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?");
    $checkStmt->execute([$userId, $bookId]);

    if ($checkStmt->rowCount() === 0) {
        $insertStmt = $pdo->prepare("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)");
        $insertStmt->execute([$userId, $bookId]);
    }
}

header("Location: index.php");
exit;
?>
