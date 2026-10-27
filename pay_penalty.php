<?php
require "includes/auth_check.php";
require_once "includes/library_access.php";

$rentalId = (int)($_GET["rental_id"] ?? $_POST["rental_id"] ?? 0);
$userId = (int)$_SESSION["user_id"];

$s = $pdo->prepare("SELECT r.*, b.title FROM rentals r JOIN books b ON r.book_id = b.id WHERE r.id = ? AND r.user_id = ?");
$s->execute([$rentalId, $userId]);
$rental = $s->fetch(PDO::FETCH_ASSOC);

if (!$rental) {
    header("Location: my_library.php");
    exit;
}

// refresh overdue calc
user_active_rental($pdo, $userId, (int)$rental["book_id"]);
$s->execute([$rentalId, $userId]);
$rental = $s->fetch(PDO::FETCH_ASSOC);

$due = max(0, (float)$rental["penalty_amount"] - (float)$rental["penalty_paid"]);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $due > 0) {
    $pdo->prepare("UPDATE rentals SET penalty_paid = penalty_amount, status = 'returned', returned_at = NOW() WHERE id = ?")
        ->execute([$rentalId]);
    header("Location: my_library.php?penalty_paid=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penalty – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>
<div class="app-container" style="max-width:520px;margin:40px auto;">
  <h2 class="section-title">Overdue penalty</h2>
  <p style="color:#ccc">Book: <strong><?php echo htmlspecialchars($rental["title"]); ?></strong></p>
  <p style="color:#888">Rental ended: <?php echo htmlspecialchars($rental["end_date"]); ?></p>
  <p style="color:#e74c3c; font-size:1.3rem">Penalty due: ₹<?php echo number_format($due, 2); ?></p>
  <p style="color:#777; font-size:0.85rem">Rule: 10% of rent amount per day late (minimum ₹20). Pay to clear this rental.</p>
  <?php if ($due > 0): ?>
  <form method="POST">
    <input type="hidden" name="rental_id" value="<?php echo $rentalId; ?>">
    <button class="submit-btn" type="submit">Pay penalty (demo)</button>
  </form>
  <?php else: ?>
  <p style="color:#2ecc71">No penalty due.</p>
  <?php endif; ?>
  <p style="margin-top:16px"><a href="my_library.php" style="color:#c9a84c">My Library</a></p>
</div>
</body>
</html>
