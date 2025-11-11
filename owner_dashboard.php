<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header('Location: login.php');
    exit;
}

$owner_id = $_SESSION['user_id'];


$search = $_GET['search'] ?? '';
$searchParam = "%$search%";

$stmt = $pdo->prepare("SELECT * FROM apartments WHERE owner_id = ? AND status = 'vacant' AND title LIKE ?");
$stmt->execute([$owner_id, $searchParam]);
$apartments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Owner Dashboard</title>
<link rel="stylesheet" href="style_owner_db.css">
</head>
<body>

<nav class="navbar">
    <div class="owner-title">OWNER</div>
    <div class="right-section">
        <form method="GET" action="owner_dashboard.php" style="display: flex;">
            <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>" />
            <button type="submit" class="search-btn">Search</button>
        </form>
        <button onclick="window.location.href='add_apartment.php'">Add Apartment</button>
        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>
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
                <button class="see-details-btn" onclick="window.location.href='apartment_details.php?id=<?php echo $apt['id']; ?>'">See details</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>
