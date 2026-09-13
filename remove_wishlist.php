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
    $deleteStmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
    $deleteStmt->execute([$userId, $bookId]);
}

header("Location: wishlist.php");
exit;
?>
