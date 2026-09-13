<?php
session_start();
require "db_connect.php";

$errors = [];
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($name === "") {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            $errors[] = "An account with this email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $insertStmt->execute([$name, $email, $hashedPassword]);

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
  <title>StorySpill - Sign Up</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <nav class="navbar">
    <div class="logo">StorySpill</div>
    <button class="menu-btn" id="menuBtn">&#9776;</button>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>
      <li><a href="signup.php">Sign Up</a></li>
    </ul>
  </nav>

  <div class="form-container">
    <h2>Create Your Account</h2>

    <?php if (!empty($errors)) { ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $error) { echo "<p>" . htmlspecialchars($error) . "</p>"; } ?>
      </div>
    <?php } ?>

    <form id="registerForm" method="POST" action="signup.php">
      <div class="form-group">
        <label for="regName">Full Name</label>
        <input type="text" id="regName" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($name); ?>">
        <span class="error-text" id="regNameError">Name is required</span>
      </div>

      <div class="form-group">
        <label for="regEmail">Email</label>
        <input type="email" id="regEmail" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
        <span class="error-text" id="regEmailError">Enter a valid email</span>
      </div>

      <div class="form-group">
        <label for="regPassword">Password</label>
        <input type="password" id="regPassword" name="password" placeholder="Create a password (min 6 characters)">
        <span class="error-text" id="regPasswordError">Password must be at least 6 characters</span>
      </div>

      <div class="form-group">
        <label for="regConfirmPassword">Confirm Password</label>
        <input type="password" id="regConfirmPassword" name="confirm_password" placeholder="Re-enter your password">
        <span class="error-text" id="regConfirmError">Passwords do not match</span>
      </div>

      <button type="submit" class="submit-btn">Sign Up</button>
    </form>
    <p class="form-footer">Already have an account? <a href="login.php">Login here</a></p>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>
