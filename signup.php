<?php
session_start();
require "db_connect.php";
require "includes/password_rules.php";

$errors = [];
$name = $email = "";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($name === "") $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
    $errors = array_merge($errors, validate_password_strength($password));
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)")
                ->execute([$name, $email, $hash]);
            header("Location: login.php?registered=1");
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
  <title>Sign Up – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page">
  <div class="form-card">
    <h2>Create Account</h2>
    <p class="form-sub">Join the personal library</p>

    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="signup.php">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Your name" value="<?php echo htmlspecialchars($name); ?>" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="new-password">
        <?php echo password_requirements_html(); ?>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required autocomplete="new-password">
      </div>
      <button type="submit" class="submit-btn">Sign Up</button>
    </form>
    <p style="text-align:center; margin-top:16px; color:#888; font-size:0.85rem;">
      Already have an account? <a href="login.php">Login</a>
    </p>
  </div>
</div>

<footer class="site-footer">
  <p>© 2026 StorySpill · A personal digital bookshelf</p>
</footer>

</body>
</html>