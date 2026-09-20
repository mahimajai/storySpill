<?php
require "admin_auth_check.php";


$onlineStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM users WHERE last_activity >= (NOW() - INTERVAL 5 MINUTE)");
$onlineStmt->execute();
$onlineCount = $onlineStmt->fetch(PDO::FETCH_ASSOC)["total"];


$signupStmt = $pdo->query("SELECT COUNT(*) AS total FROM users");
$signupCount = $signupStmt->fetch(PDO::FETCH_ASSOC)["total"];


$booksStmt = $pdo->query("SELECT COUNT(*) AS total FROM books");
$booksCount = $booksStmt->fetch(PDO::FETCH_ASSOC)["total"];

$recentUsersStmt = $pdo->query("SELECT name, email, last_activity, created_at FROM users ORDER BY created_at DESC LIMIT 10");
$recentUsers = $recentUsersStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill Admin - Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <nav class="navbar">
    <div class="logo">StorySpill Admin</div>
    <ul class="nav-links" id="navLinks" style="display:flex;">
      <li style="margin-left:20px; color:#fff;">Hi, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?></li>
      <li style="margin-left:20px;"><a href="admin_logout.php" style="color:#fff;">Logout</a></li>
    </ul>
  </nav>

  <div class="dashboard-section" style="margin-top:20px;">
    <h3>Platform Overview</h3>

    <div class="book-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
      <div class="book-card" style="padding:20px;">
        <h2 style="font-size:36px; color:#27ae60;"><?php echo $onlineCount; ?></h2>
        <p>Currently Online</p>
      </div>

      <div class="book-card" style="padding:20px;">
        <h2 style="font-size:36px; color:#2980b9;"><?php echo $signupCount; ?></h2>
        <p>Total Registered Users (Signups)</p>
      </div>

      <div class="book-card" style="padding:20px;">
        <h2 style="font-size:36px; color:#8e44ad;"><?php echo $booksCount; ?></h2>
        <p>Total Books Uploaded</p>
      </div>
    </div>
  </div>

  <div class="dashboard-section">
    <h3>Recently Registered Users</h3>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Joined On</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentUsers as $u) {
          $isOnline = false;
          if (!empty($u["last_activity"])) {
              $lastActivityTime = strtotime($u["last_activity"]);
              $isOnline = (time() - $lastActivityTime) <= (5 * 60);
          }
        ?>
          <tr>
            <td><?php echo htmlspecialchars($u["name"]); ?></td>
            <td><?php echo htmlspecialchars($u["email"]); ?></td>
            <td><?php echo htmlspecialchars($u["created_at"]); ?></td>
            <td>
              <?php if ($isOnline) { ?>
                <span style="color:#27ae60; font-weight:bold;">&#9679; Online</span>
              <?php } else { ?>
                <span style="color:#999;">&#9679; Offline</span>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill Admin Panel.</p>
  </footer>
</body>
</html>
