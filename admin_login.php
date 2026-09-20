<?php
session_start();
require_once "db_connect.php";

$errors = [];
$username = "";

if (isset($_SESSION["admin_id"])) {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($username === "") {
        $errors[] = "Username is required.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin["password"])) {
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];
            $_SESSION["admin_last_activity"] = time();

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
  <title>StorySpill Admin - Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <nav class="navbar">
    <div class="logo">StorySpill Admin</div>
  </nav>

  <div class="form-container">
    <h2>Admin Login</h2>

    <?php if (isset($_GET["created"])) { ?>
      <div class="form-message success-message">
        <p>Admin account created successfully! Please login.</p>
      </div>
    <?php } ?>

    <?php if (!empty($errors)) { ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $error) { echo "<p>" . htmlspecialchars($error) . "</p>"; } ?>
      </div>
    <?php } ?>

    <form method="POST" action="admin_login.php">
      <div class="form-group">
        <label for="username">Admin Username</label>
        <input type="text" id="username" name="username" placeholder="Enter admin username" value="<?php echo htmlspecialchars($username); ?>">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter admin password">
      </div>

      <button type="submit" class="submit-btn">Login as Admin</button>
    </form>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>
</body>
</html>
