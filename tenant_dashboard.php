<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = $_SESSION['user_id'];


$search = $_GET['search'] ?? '';
$searchParam = "%$search%";


$stmt = $pdo->prepare("SELECT * FROM apartments WHERE status = 'vacant' AND title LIKE ?");
$stmt->execute([$searchParam]);
$apartments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Tenant Dashboard</title>
<link rel="stylesheet" href="style_tenant_db.css">
</head>
<body>

<nav class="navbar">
    <div class="tenant-title">TENANT</div>
  <div class="nav-search">
    <form method="GET" action="tenant_dashboard.php" style="display: flex;">
      <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>" />
      <button type="submit">Search</button>
    </form>
  </div>

  <button class="nav-logout" onclick="window.location.href='logout.php'">Logout</button>
</nav>

<main class="apartment-grid">

<?php if (count($apartments) === 0): ?>
    <p style="grid-column: 1/-1; text-align:center; font-weight: bold;">No vacant apartments found.</p>
<?php else: ?>
    <?php foreach ($apartments as $apt): ?>
        <div class="apartment-card">
            <?php if ($apt['picture'] && file_exists($apt['picture'])): ?>
                <img src="<?php echo htmlspecialchars($apt['picture']); ?>" alt="Apartment Image" class="apartment-image" />
            <?php else: ?>
                <div class="apartment-image placeholder"></div>
            <?php endif; ?>
            <div class="apartment-buttons">
<button onclick="window.location.href='tenant_apartment_details.php?id=<?= $apt['id'] ?>'">See Details</button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</main>

</body>
</html>
