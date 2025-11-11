<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email= trim($_POST['email']);
    $password = $_POST['password'];


    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === 'owner') {
            header('Location: owner_dashboard.php');
        } else {
            header('Location: tenant_dashboard.php');
        }
        exit;
    } else {
        $error = "Invalid Email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login</title>
<link rel="stylesheet" href="style_login.css">
</head>
<body>
  <div class="container">
    <div class="left-panel"></div>
    <div class="right-panel">
      <h1>Log in</h1>

      <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php" novalidate>
        <input type="text" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />

        <button type="submit">Log in</button>
      </form>

      <a href="register.php" class="register-link">You don’t have account yet?</a>
    </div>
  </div>
</body>
</html>
