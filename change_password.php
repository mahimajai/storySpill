<?php
require "includes/auth_check.php";
require "includes/password_rules.php";

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current = $_POST["current_password"] ?? "";
    $new = $_POST["new_password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current, $user["password"])) {
        $errors[] = "Current password is incorrect.";
    }
    $errors = array_merge($errors, validate_password_strength($new));
    if ($new !== $confirm) $errors[] = "New passwords do not match.";
    if ($current !== "" && $new === $current) $errors[] = "New password must be different from current password.";

    if (empty($errors)) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
            ->execute([$hash, $_SESSION["user_id"]]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page">
  <div class="form-card">
    <h2>Change Password</h2>
    <p class="form-sub">Keep your account secure</p>

    <?php if ($success): ?>
      <div class="form-message success-message"><p>Password updated successfully.</p></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="current_password" required autocomplete="current-password">
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" required autocomplete="new-password">
        <?php echo password_requirements_html(); ?>
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required autocomplete="new-password">
      </div>
      <button type="submit" class="submit-btn">Update Password</button>
    </form>
    <p style="text-align:center; margin-top:14px;">
      <a href="dashboard.php" style="color:#888;">Back to Profile</a>
    </p>
  </div>
</div>

<footer class="site-footer">
  <p>© 2026 StorySpill · A personal digital bookshelf</p>
</footer>

</body>
</html>