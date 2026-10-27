<?php
session_start();
require "db_connect.php";

$errors = [];
$username = "";

if (isset($_SESSION["admin_id"])) {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "") $errors[] = "Username is required.";
    if ($password === "") $errors[] = "Password is required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin["password"])) {
            session_regenerate_id(true);
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $errors[] = "Incorrect admin username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page">
  <div class="form-card">
    <h2>Admin Login</h2>
    <p class="form-sub">Restricted access</p>

    <?php if (isset($_GET["created"])): ?>
      <div class="form-message success-message"><p>Admin account created. Please login.</p></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="submit-btn">Login as Admin</button>
    </form>
  </div>
</div>

<footer class="site-footer">
  <p>© 2026 StorySpill · A personal digital bookshelf</p>
</footer>

</body>
</html>
