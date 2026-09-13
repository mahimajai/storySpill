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
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookId, $userId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        $deleteStmt = $pdo->prepare("DELETE FROM books WHERE id = ? AND user_id = ?");
        $deleteStmt->execute([$bookId, $userId]);

        // Remove uploaded files from the server, if they exist
        if (!empty($book["cover_image"]) && file_exists($book["cover_image"])) {
            unlink($book["cover_image"]);
        }

        if (!empty($book["intro_video"]) && file_exists($book["intro_video"])) {
            unlink($book["intro_video"]);
        }
    }
}

header("Location: dashboard.php");
exit;
?>
