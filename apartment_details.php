<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header('Location: login.php');
    exit;
}

$owner_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid apartment ID.');
}

$apartment_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM apartments WHERE id = ? AND owner_id = ?");
$stmt->execute([$apartment_id, $owner_id]);
$apartment = $stmt->fetch();

if (!$apartment) {
    die('Apartment not found or you do not have permission to view it.');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    if (in_array($new_status, ['vacant', 'occupied'])) {
        $stmt = $pdo->prepare("UPDATE apartments SET status = ? WHERE id = ? AND owner_id = ?");
        $stmt->execute([$new_status, $apartment_id, $owner_id]);
        // Refresh the page to show updated status
        header("Location: apartment_details.php?id=$apartment_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<title>Apartment Details</title>
<link rel="stylesheet" href="styles/style_details.css">
<style>
    .status-dropdown { padding: 5px; border-radius: 5px; border: 1px solid #ccc; font-weight: bold; }
    .status-vacant { background-color: #28a745; color: white; }
    .status-occupied { background-color: #dc3545; color: white; }
</style>
</head>
<body>

<div class="top-bar" onclick="window.location.href='owner_dashboard.php'">&lt; Back</div>

<div class="content">
  <?php if ($apartment['picture'] && file_exists($apartment['picture'])): ?>
    <img class="apartment-image" src="<?php echo htmlspecialchars($apartment['picture']); ?>" alt="Apartment Image" />
  <?php else: ?>
    <div style="height: 400px; border-radius:25px; background:#ccc; margin-bottom:20px;">No image available</div>
  <?php endif; ?>

  <div class="title"><?php echo htmlspecialchars($apartment['title']); ?></div>

  <p><span class="label">Description:</span><?php echo nl2br(htmlspecialchars($apartment['description'])); ?></p>
  <p><span class="label">Price:</span>₱<?php echo number_format($apartment['price'], 2); ?></p>
  <p>
    <span class="label">Status:</span>
    <form method="POST" style="display: inline;">
      <select name="status" class="status-dropdown" onchange="this.form.submit()">
        <option value="vacant" class="<?php echo $apartment['status'] === 'vacant' ? 'status-vacant' : ''; ?>" <?php echo $apartment['status'] === 'vacant' ? 'selected' : ''; ?>>Vacant</option>
        <option value="occupied" class="<?php echo $apartment['status'] === 'occupied' ? 'status-occupied' : ''; ?>" <?php echo $apartment['status'] === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
      </select>
    </form>
  </p>

  <button class="edit-btn" onclick="window.location.href='edit_apartment.php?id=<?php echo $apartment['id']; ?>'">Edit Apartment</button>
  <br>
  <button class="delete-btn" onclick="if(confirm('Are you sure you want to delete this apartment?')) { window.location.href='delete_apartment.php?id=<?php echo $apartment['id']; ?>'; }" style="background-color:#dc3545; margin-left:20px;">Delete Apartment</button>
</div>

</body>
</html>
