<?php
session_start();
require "db_connect.php";

$errors = [];
$username = "";
$company_code = "";

// Company code required to create admin
define("ADMIN_COMPANY_CODE", "Mahi052005");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username     = trim($_POST["username"] ?? "");
    $password     = $_POST["password"] ?? "";
    $confirm      = $_POST["confirm_password"] ?? "";
    $company_code = trim($_POST["company_code"] ?? "");

    if ($company_code !== ADMIN_COMPANY_CODE) {
        $errors[] = "Invalid company code. Admin signup is restricted.";
    }
    if ($username === "") $errors[] = "Username is required.";
    if (strlen($password) < 4) $errors[] = "Password must be at least 4 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $errors[] = "This admin username already exists.";
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)")
            ->execute([$username, $hash]);
        header("Location: admin_login.php?created=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Admin – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page">
  <div class="form-card">
    <h2>Create Admin Account</h2>
    <p class="form-sub">Company code required</p>

    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="admin_signup.php">
      <div class="form-group">
        <label><i class="fas fa-key"></i> Company Code *</label>
        <input type="text" name="company_code" placeholder="Enter company code"
               value="<?php echo htmlspecialchars($company_code); ?>" required
               style="letter-spacing:1px; font-weight:600;">
        <small style="color:#666; font-size:0.72rem; display:block; margin-top:4px;">
          Only authorized members with the company code can create an admin account.
        </small>
      </div>

      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
      </div>
      <button type="submit" class="submit-btn">Create Admin</button>
    </form>

    <p style="text-align:center; margin-top:16px; color:#888; font-size:0.85rem;">
      Already admin? <a href="admin_login.php">Login here</a>
    </p>
  </div>
</div>
</body>
</html>