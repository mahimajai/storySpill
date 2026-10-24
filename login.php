<?php
session_start();
require "db_connect.php";

$errors = [];
$email = "";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["last_activity"] = time();

            $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?")
                ->execute([$user["id"]]);

            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Incorrect email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page">
  <div class="form-card">
    <h2>Login</h2>
    <p class="form-sub">Welcome back to the shelf</p>

    <?php if (isset($_GET["registered"])): ?>
      <div class="form-message success-message"><p>Account created! Please login.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET["timeout"])): ?>
      <div class="form-message error-message"><p>Logged out due to inactivity. Please login again.</p></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Your password" required>
      </div>
      <button type="submit" class="submit-btn">Login</button>
    </form>
    <p class="form-footer">Don't have an account? <a href="signup.php">Sign Up</a></p>
  </div>
</div>
<script>
  document.getElementById('navToggle')?.addEventListener('click', () =>
    document.getElementById('navLinks').classList.toggle('open'));
</script>
</body>
</html>
