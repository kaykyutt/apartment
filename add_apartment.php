<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header('Location: login.php');
    exit;
}

$error = '';
$room = '';
$price = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $owner_id = $_SESSION['user_id'];
    $room = trim($_POST['room']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    $picturePath = '';
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = basename($_FILES['picture']['name']);
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['picture']['tmp_name'], $targetPath)) {
            $error = "Failed to upload photo.";
        } else {
            $picturePath = $targetPath;
        }
    } else {
        $error = "Please upload a photo.";
    }

    if (!$error && $room && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO apartments (owner_id, title, price, description, picture, status) VALUES (?, ?, ?, ?, ?, 'vacant')");
        if ($stmt->execute([$owner_id, $room, $price, $description, $picturePath])) {
            header('Location: success_add.php');
            exit;
        } else {
            $error = "Failed to save apartment.";
        }
    } elseif (!$error) {
        $error = "Please fill all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Apartment</title>
<link rel="stylesheet" href="style_add.css">
</head>
<body>
  <div class="container">
    <div class="left-panel">
    
      <div class="back-btn" onclick="window.location.href='owner_dashboard.php'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
        </svg>
        Back
      </div>
    </div>
    <div class="right-panel">
      <h1>Add New Apartment</h1>

      <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" novalidate>
        <input type="text" name="room" placeholder="Apartment Room" required value="<?= htmlspecialchars($room) ?>" />
        <input type="number" min="0" step="0.01" name="price" placeholder="Price (P)" required value="<?= htmlspecialchars($price) ?>" />
        <textarea name="description" placeholder="Description" required><?= htmlspecialchars($description) ?></textarea>
        <input type="file" name="picture" accept="image/*" required />
        <button type="submit" class="submit-btn">Confirm</button>
      </form>
    </div>
  </div>
</body>
</html>