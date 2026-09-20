<?php
require "auth_check.php";

$userId = $_SESSION["user_id"];
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

   
    if (!$user || !password_verify($currentPassword, $user["password"])) {
        $errors[] = "Current password is incorrect.";
    }

    if (strlen($newPassword) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = "New passwords do not match.";
    }

    if (empty($errors) && $newPassword === $currentPassword) {
        $errors[] = "New password must be different from the current password.";
    }

    if (empty($errors)) {
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->execute([$newHashedPassword, $userId]);

        session_unset();
        session_destroy();

        header("Location: login.php?passwordchanged=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill - Change Password</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body data-protected="true">

  <nav class="navbar">
    <div class="logo">StorySpill</div>
    <button class="menu-btn" id="menuBtn">&#9776;</button>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>
      <li><a href="dashboard.php">Profile</a></li>
      <li><a href="upload.php">Upload Book</a></li>
      <li><a href="wishlist.php">Wishlist</a></li>
      <li><a href="change_password.php">Change Password</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <div class="form-container">
    <h2>Change Password</h2>

    <?php if (!empty($errors)) { ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $error) { echo "<p>" . htmlspecialchars($error) . "</p>"; } ?>
      </div>
    <?php } ?>

    <form method="POST" action="change_password.php">
      <div class="form-group">
        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" placeholder="Enter your current password">
      </div>

      <div class="form-group">
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" placeholder="Enter a new password (min 6 characters)">
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your new password">
      </div>

      <button type="submit" class="submit-btn">Update Password</button>
    </form>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>
