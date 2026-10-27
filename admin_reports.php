<?php
require "includes/admin_auth_check.php";

if (isset($_GET["resolve"]) && (int)$_GET["resolve"] > 0) {
    $pdo->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?")->execute([(int)$_GET["resolve"]]);
    header("Location: admin_reports.php");
    exit;
}
if (isset($_GET["review"]) && (int)$_GET["review"] > 0) {
    $pdo->prepare("UPDATE reports SET status = 'reviewed' WHERE id = ?")->execute([(int)$_GET["review"]]);
    header("Location: admin_reports.php");
    exit;
}

$reports = $pdo->query(
    "SELECT r.*, u.name AS reporter, b.title AS book_title
     FROM reports r
     JOIN users u ON r.reporter_id = u.id
     JOIN books b ON r.book_id = b.id
     ORDER BY FIELD(r.status,'pending','reviewed','resolved'), r.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports – Admin · StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>
<div class="app-container">
  <p><a href="admin_dashboard.php" style="color:#c9a84c;text-decoration:none;"><i class="fas fa-arrow-left"></i> Dashboard</a></p>
  <h2 class="section-title">Book Reports</h2>
  <div style="overflow-x:auto">
    <table class="table-dark">
      <thead>
        <tr><th>ID</th><th>Book</th><th>Reporter</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if (empty($reports)): ?>
          <tr><td colspan="7" style="color:#888">No reports yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td><?php echo (int)$r["id"]; ?></td>
            <td><?php echo htmlspecialchars($r["book_title"]); ?></td>
            <td><?php echo htmlspecialchars($r["reporter"]); ?></td>
            <td><?php echo htmlspecialchars($r["reason"]); ?></td>
            <td><?php echo htmlspecialchars($r["status"]); ?></td>
            <td><?php echo date("M j, Y", strtotime($r["created_at"])); ?></td>
            <td>
              <?php if ($r["status"] !== "reviewed"): ?>
                <a class="btn-sm-action btn-edit" href="admin_reports.php?review=<?php echo $r["id"]; ?>">Mark reviewed</a>
              <?php endif; ?>
              <?php if ($r["status"] !== "resolved"): ?>
                <a class="btn-sm-action btn-edit" href="admin_reports.php?resolve=<?php echo $r["id"]; ?>">Resolve</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<footer class="site-footer">
  <p>© 2026 StorySpill · A personal digital bookshelf</p>
</footer>

</body>
</html>
