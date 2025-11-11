<?php
session_start();
require 'config.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!$name || !$email || !$password || !$role) {
        $error = "Please fill in all fields and select a role.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!in_array($role, ['owner', 'tenant'])) {
        $error = "Invalid role selected.";
    } else {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$name, $email]);
        if ($stmt->fetch()) {
            $error = "Username or Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $hashedPassword, $email, $role])) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed, please try again.";
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
<title>Sign Up</title>
<link rel="stylesheet" href="style_register.css">
</head>
<body>
<div class="container">
  <div class="left-panel"></div>
  <div class="right-panel">
    <h1>Sign Up</h1>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" novalidate>
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

    <a href="login.php" class="login-link">Do you have account ?</a>
  </div>
</div>
</body>
</html>