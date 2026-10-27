<?php
require "includes/auth_check.php";
header("Content-Type: application/json");

$bookId = (int)($_POST["book_id"] ?? 0);
$reason = trim($_POST["reason"] ?? "");

if ($bookId <= 0 || $reason === "") {
    echo json_encode(["ok" => false, "error" => "Book and reason required."]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO reports (reporter_id, book_id, reason, status) VALUES (?, ?, ?, 'pending')"
    );
    $stmt->execute([$_SESSION["user_id"], $bookId, $reason]);
    echo json_encode(["ok" => true]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => "Could not submit report."]);
}
