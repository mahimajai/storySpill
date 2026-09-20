<?php
require_once "db_connect.php";

$errors = [];
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($username === "") {
        $errors[] = "Username is required.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $checkStmt->execute([$username]);

        if ($checkStmt->rowCount() > 0) {
            $errors[] = "An admin with this username already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $insertStmt->execute([$username, $hashedPassword]);

            header("Location: admin_login.php?created=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill Admin - Create Admin Account</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <nav class="navbar">
    <div class="logo">StorySpill Admin</div>
  </nav>

  <div class="form-container">
    <h2>Create Admin Account</h2>

    <div class="form-message error-message" style="background-color:#fff3cd;color:#856404;border-color:#ffeeba;">
      <p><strong>Note:</strong> This page is meant to be used once, to create the first admin account. Delete or rename this file after you've created your admin login.</p>
    </div>

    <?php if (!empty($errors)) { ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $error) { echo "<p>" . htmlspecialchars($error) . "</p>"; } ?>
      </div>
    <?php } ?>

    <form method="POST" action="admin_signup.php">
      <div class="form-group">
        <label for="username">Admin Username</label>
        <input type="text" id="username" name="username" placeholder="Choose an admin username" value="<?php echo htmlspecialchars($username); ?>">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password (min 6 characters)">
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password">
      </div>

      <button type="submit" class="submit-btn">Create Admin Account</button>
    </form>
    <p class="form-footer">Already have an admin account? <a href="admin_login.php">Admin Login</a></p>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>
</body>
</html>
