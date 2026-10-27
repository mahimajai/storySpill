<?php
require "includes/auth_check.php";
header("Content-Type: application/json");

$bookId = (int)($_POST["book_id"] ?? 0);
$rating = (int)($_POST["rating"] ?? 0);
$text = trim($_POST["review_text"] ?? "");

if ($bookId <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(["ok" => false, "error" => "Invalid rating or book."]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO reviews (user_id, book_id, rating, review_text) VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE rating = VALUES(rating), review_text = VALUES(review_text)"
    );
    $stmt->execute([$_SESSION["user_id"], $bookId, $rating, $text !== "" ? $text : null]);
    echo json_encode(["ok" => true]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => "Could not save review."]);
}
