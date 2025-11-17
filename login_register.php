<?php
session_start();
require 'config.php';

$login_error = '';
$register_error = '';
$register_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
    
        $email = trim($_POST['email']);
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
            $login_error = "Invalid Email or password";
        }
    } elseif (isset($_POST['register'])) {
      
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        if (!$name || !$email || !$password || !$role) {
            $register_error = "Please fill in all fields and select a role.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_error = "Please enter a valid email address.";
        } elseif (!in_array($role, ['owner', 'tenant'])) {
            $register_error = "Invalid role selected.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$name, $email]);
            if ($stmt->fetch()) {
                $register_error = "Username or Email already registered.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$name, $hashedPassword, $email, $role])) {
                    $register_success = "Registration successful! You can now log in.";
                } else {
                    $register_error = "Registration failed, please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login/Register</title>
<link rel="stylesheet" href="styles/style_login.css">
<script src="script.js"></script>
<style>
    .form-box { display: none; }
    .form-box.active { display: block; }
</style>
</head>
<body>
  <div class="container">
    <div class="left-panel"></div>
    <div class="right-panel">
      <!-- Login Form -->
      <div id="login" class="form-box active">
        <h1>Log in</h1>
        <?php if ($login_error): ?>
          <div class="error-message"><?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login_register.php" novalidate>
          <input type="hidden" name="login" value="1" />
          <input type="text" name="email" placeholder="Email" required />
          <input type="password" name="password" placeholder="Password" required />
          <button type="submit">Log in</button>
        </form>
        <a href="#" onclick="showForm('register')" class="register-link">You don't have account yet?</a>
      </div>

      <!-- Register Form -->
      <div id="register" class="form-box">
        <h1>Sign Up</h1>
        <?php if ($register_error): ?>
          <div class="message error"><?= htmlspecialchars($register_error) ?></div>
        <?php elseif ($register_success): ?>
          <div class="message success"><?= htmlspecialchars($register_success) ?></div>
        <?php endif; ?>
        <form method="POST" action="login_register.php" novalidate>
          <input type="hidden" name="register" value="1" />
          <input type="text" name="name" placeholder="Name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
          <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
          <input type="password" name="password" placeholder="Password" required />
          <select name="role" required>
            <option value="">-Select Role-</option>
            <option value="owner" <?= (($_POST['role'] ?? '') === 'owner' ? 'selected' : '') ?>>Owner</option>
            <option value="tenant" <?= (($_POST['role'] ?? '') === 'tenant' ? 'selected' : '') ?>>Tenant</option>
          </select>
          <button type="submit">Sign up</button>
        </form>
        <a href="#" onclick="showForm('login')" class="login-link">Do you have account?</a>
      </div>
    </div>
  </div>
</body>
</html>