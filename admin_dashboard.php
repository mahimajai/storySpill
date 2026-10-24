<?php
require "includes/admin_auth_check.php";

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$onlineUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE last_activity >= NOW() - INTERVAL 5 MINUTE")->fetchColumn();

$allUsers = $pdo->query(
    "SELECT u.id, u.name, u.email, u.last_activity, u.created_at,
            (SELECT COUNT(*) FROM books b WHERE b.user_id = u.id) AS book_count
     FROM users u
     ORDER BY u.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$allBooks = $pdo->query(
    "SELECT books.id, books.title, books.author, books.genre, books.created_at, users.name AS uploader, users.id AS user_id
     FROM books JOIN users ON books.user_id = users.id
     ORDER BY books.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .clickable-row { cursor: pointer; }
    .clickable-row:hover { background: #1f1f1f !important; }
    .table-dark a { color: #c9a84c; text-decoration: none; }
    .table-dark a:hover { text-decoration: underline; }
  </style>
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="app-container">
  <h2 class="section-title">Admin Dashboard</h2>
  <p style="color:#888; margin-bottom:24px">
    Logged in as <strong><?php echo htmlspecialchars($_SESSION["admin_username"]); ?></strong>
    · <a href="admin_logout.php">Logout</a>
  </p>

  <?php if (isset($_GET["user_deleted"])): ?>
    <div class="form-message success-message" style="max-width:600px;margin-bottom:20px"><p>User deleted successfully.</p></div>
  <?php endif; ?>
  <?php if (isset($_GET["book_deleted"])): ?>
    <div class="form-message success-message" style="max-width:600px;margin-bottom:20px"><p>Book deleted successfully.</p></div>
  <?php endif; ?>
  <?php if (isset($_GET["book_updated"])): ?>
    <div class="form-message success-message" style="max-width:600px;margin-bottom:20px"><p>Book updated successfully.</p></div>
  <?php endif; ?>

  <div class="admin-stats">
    <div class="stat-card">
      <div class="stat-num"><?php echo $totalUsers; ?></div>
      <div class="stat-label">TOTAL USERS</div>
    </div>
    <div class="stat-card">
      <div class="stat-num"><?php echo $onlineUsers; ?></div>
      <div class="stat-label">ONLINE NOW</div>
    </div>
    <div class="stat-card">
      <div class="stat-num"><?php echo $totalBooks; ?></div>
      <div class="stat-label">TOTAL BOOKS</div>
    </div>
  </div>

  <h3 class="section-title" style="font-size:1.15rem">All Users <span style="color:#888;font-size:0.85rem">(click row to view profile)</span></h3>
  <div style="overflow-x:auto; margin-bottom:36px">
    <table class="table-dark">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Books</th>
          <th>Status</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allUsers as $u):
          $isOnline = $u["last_activity"] && (strtotime($u["last_activity"]) >= time() - 300);
        ?>
          <tr class="clickable-row" onclick="window.location='admin_user.php?id=<?php echo $u['id']; ?>'">
            <td><strong><?php echo htmlspecialchars($u["name"]); ?></strong></td>
            <td><?php echo htmlspecialchars($u["email"]); ?></td>
            <td><?php echo (int)$u["book_count"]; ?></td>
            <td>
              <span style="color:<?php echo $isOnline ? '#2ecc71' : '#888'; ?>">
                <?php echo $isOnline ? '● Online' : '○ Offline'; ?>
              </span>
            </td>
            <td><?php echo date("M j, Y", strtotime($u["created_at"])); ?></td>
            <td onclick="event.stopPropagation()">
              <a href="admin_user.php?id=<?php echo $u['id']; ?>" class="btn-sm-action btn-edit">View</a>
              <a href="admin_delete_user.php?id=<?php echo $u['id']; ?>" class="btn-sm-action btn-delete"
                 onclick="return confirm('Delete this user and ALL their books permanently?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h3 class="section-title" style="font-size:1.15rem">All Books</h3>
  <div style="overflow-x:auto">
    <table class="table-dark">
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Genre</th>
          <th>Uploader</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allBooks as $b): ?>
          <tr>
            <td><?php echo htmlspecialchars($b["title"]); ?></td>
            <td><?php echo htmlspecialchars($b["author"]); ?></td>
            <td><?php echo htmlspecialchars($b["genre"]); ?></td>
            <td>
              <a href="admin_user.php?id=<?php echo $b['user_id']; ?>">
                <?php echo htmlspecialchars($b["uploader"]); ?>
              </a>
            </td>
            <td><?php echo date("M j, Y", strtotime($b["created_at"])); ?></td>
            <td>
              <a href="admin_edit_book.php?id=<?php echo $b["id"]; ?>" class="btn-sm-action btn-edit">Edit</a>
              <a href="admin_delete_book.php?id=<?php echo $b["id"]; ?>" class="btn-sm-action btn-delete"
                 onclick="return confirm('Delete this book permanently?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>