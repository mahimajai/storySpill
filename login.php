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
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

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
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

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
  <title>StorySpill - Login</title>
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
    <h2>Login</h2>

    <?php if (isset($_GET["registered"])) { ?>
      <div class="form-message success-message">
        <p>Account created successfully! Please login.</p>
      </div>
    <?php } ?>

    <?php if (!empty($errors)) { ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $error) { echo "<p>" . htmlspecialchars($error) . "</p>"; } ?>
      </div>
    <?php } ?>

    <form id="loginForm" method="POST" action="login.php">
      <div class="form-group">
        <label for="loginEmail">Email</label>
        <input type="email" id="loginEmail" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
        <span class="error-text" id="loginEmailError">Enter a valid email</span>
      </div>

      <div class="form-group">
        <label for="loginPassword">Password</label>
        <input type="password" id="loginPassword" name="password" placeholder="Enter your password">
        <span class="error-text" id="loginPasswordError">Password is required</span>
      </div>

      <button type="submit" class="submit-btn">Login</button>
    </form>
    <p class="form-footer">Don't have an account? <a href="signup.php">Sign up here</a></p>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>
