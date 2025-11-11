<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = $_SESSION['user_id'];


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid apartment ID.');
}

$apartment_id = (int)$_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM apartments WHERE id = ? AND status = 'vacant'");
$stmt->execute([$apartment_id]);
$apartment = $stmt->fetch();

if (!$apartment) {
    die('Apartment not found or not available.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Apartment Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="style_tenant_aptdetails.css">
</head>
<body>

<nav class="navbar">
  <div class="nav-back" onclick="window.location.href='tenant_dashboard.php'">&lt; Back</div>
  <button class="nav-logout" onclick="window.location.href='logout.php'">Logout</button>
</nav>

<div class="content">
  <?php if ($apartment['picture'] && file_exists($apartment['picture'])): ?>
      <img src="<?php echo htmlspecialchars($apartment['picture']); ?>" alt="Apartment Image" class="apartment-image" />
  <?php else: ?>
      <div style="width:100%; height: 350px; background:#d6d6d6; border-radius: 15px; line-height: 350px; color: #777; font-weight: bold; margin-bottom: 20px;">
        No image available
      </div>
  <?php endif; ?>

  <div class="room-title"><?= htmlspecialchars($apartment['title']) ?></div>

  <div class="field-content">
    <span class="field-label">Description:</span> <?= nl2br(htmlspecialchars($apartment['description'])) ?>
  </div>

  <div class="field-content">
    <span class="field-label">Price:</span> ₱<?= number_format($apartment['price'], 2) ?>
  </div>

  <div class="status-pill"><?= ucfirst($apartment['status']) ?></div>
<br>
  <button class="pay-btn" onclick="window.location.href='paymongo_payment.php?apartment_id=<?= $apartment['id'] ?>'">Pay now</button>
</div>

</body>
</html>
