<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");
date_default_timezone_set("Asia/Kolkata");

require "includes/auth_check.php";

if (!file_exists(__DIR__ . "/includes/library_access.php")) {
    die("Missing: includes/library_access.php — copy library_access.php into the includes folder.");
}
require_once "includes/library_access.php";

$userId = (int)$_SESSION["user_id"];
$dbError = "";
$bought = [];
$rented = [];
$now = time();

try {
    try {
        $pdo->prepare(
            "UPDATE rentals SET status = 'expired'
             WHERE user_id = ? AND returned_at IS NULL
               AND TIMESTAMPADD(SECOND, days * 86400, created_at) < NOW()
               AND status = 'active'"
        )->execute([$userId]);
    } catch (Throwable $e) { /* ignore */ }

    $purchases = $pdo->prepare(
        "SELECT p.*, b.title, b.author, b.pdf_path FROM purchases p
         JOIN books b ON p.book_id = b.id
         WHERE p.user_id = ? AND p.status = 'paid' ORDER BY p.created_at DESC"
    );
    $purchases->execute([$userId]);
    $bought = $purchases->fetchAll(PDO::FETCH_ASSOC);

    $rentals = $pdo->prepare(
        "SELECT r.*, b.title, b.author FROM rentals r
         JOIN books b ON r.book_id = b.id
         WHERE r.user_id = ? AND r.returned_at IS NULL
           AND TIMESTAMPADD(SECOND, r.days * 86400, r.created_at) >= NOW()
         ORDER BY TIMESTAMPADD(SECOND, r.days * 86400, r.created_at) ASC"
    );
    $rentals->execute([$userId]);
    $rented = $rentals->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Library – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .timer { font-family: ui-monospace, monospace; font-weight: 700; color: #c9a84c; }
    .timer.urgent { color: #e74c3c; }
  </style>
</head>
<body>
<?php include "includes/navbar.php"; ?>
<div class="app-container">
  <h2 class="section-title">My Library</h2>

  <?php if ($dbError): ?>
    <div class="form-message error-message">
      <p><strong>Database error:</strong> <?php echo htmlspecialchars($dbError); ?></p>
    </div>
  <?php endif; ?>

  <h3 class="section-title" style="font-size:1.1rem">Purchased (Buy) — lifetime</h3>
  <?php if (empty($bought)): ?>
    <p style="color:#888">No purchases yet.</p>
  <?php else: ?>
    <div style="overflow-x:auto;margin-bottom:28px">
      <table class="table-dark">
        <thead><tr><th>Title</th><th>Author</th><th>Paid</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($bought as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row["title"]); ?></td>
            <td><?php echo htmlspecialchars($row["author"]); ?></td>
            <td>₹<?php echo number_format((float)$row["amount"], 2); ?></td>
            <td><?php echo date("M j, Y", strtotime($row["created_at"])); ?></td>
            <td><a class="btn-sm-action btn-edit" href="read_book.php?id=<?php echo (int)$row["book_id"]; ?>">Read</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <h3 class="section-title" style="font-size:1.1rem">Active rentals</h3>
  <?php if (empty($rented)): ?>
    <p style="color:#888">No active rentals. When the timer hits zero, the rental is removed automatically.</p>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="table-dark">
        <thead><tr><th>Title</th><th>Days</th><th>Rent</th><th>Ends at</th><th>Time left</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rented as $row):
          $days = (int)$row["days"];
          // Exact end = payment time + N * 24h (using DB created_at string in Asia/Kolkata)
          $startTs = strtotime($row["created_at"]);
          $endTs = $startTs + ($days * 86400);
          $remaining = max(0, $endTs - $now);
        ?>
          <tr class="rental-row" data-remaining="<?php echo (int)$remaining; ?>">
            <td><?php echo htmlspecialchars($row["title"]); ?></td>
            <td><?php echo $days; ?></td>
            <td>₹<?php echo number_format((float)$row["rent_amount"], 2); ?></td>
            <td><?php echo date("M j, Y g:i A", $endTs); ?></td>
            <td><span class="timer">--:--:--</span></td>
            <td><a class="btn-sm-action btn-edit" href="read_book.php?id=<?php echo (int)$row["book_id"]; ?>">Read</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<script>
(function () {
  function pad(n) { return String(n).padStart(2, '0'); }
  // Server ne exact remaining seconds diye — browser timezone matter nahi karta
  const rows = Array.from(document.querySelectorAll('.rental-row')).map(row => ({
    el: row,
    left: parseInt(row.dataset.remaining, 10) || 0,
    timer: row.querySelector('.timer')
  }));
  const pageLoad = Date.now();

  function tick() {
    const elapsed = Math.floor((Date.now() - pageLoad) / 1000);
    rows.forEach(r => {
      if (!r.el.parentNode) return;
      let left = r.left - elapsed;
      if (left <= 0) {
        r.timer.textContent = '0d 00:00:00';
        r.el.remove();
        return;
      }
      const d = Math.floor(left / 86400);
      const h = Math.floor((left % 86400) / 3600);
      const m = Math.floor((left % 3600) / 60);
      const s = left % 60;
      r.timer.textContent = d + 'd ' + pad(h) + ':' + pad(m) + ':' + pad(s);
      if (left < 3600) r.timer.classList.add('urgent');
    });
  }
  tick();
  setInterval(tick, 1000);
})();
</script>
</body>
</html>