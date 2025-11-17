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
$stmt = $pdo->prepare("
    SELECT 
        p.tenant_name,
        u.email AS tenant_email,
        a.title AS apartment_title, 
        p.amount,
        p.created_at AS payment_date,
        DATE_ADD(p.created_at, INTERVAL 30 DAY) AS due_date  -- Example due date (adjust as needed)
    FROM payments p
    JOIN users u ON p.tenant_id = u.id
    JOIN apartments a ON p.apartment_id = a.id
    WHERE a.owner_id = ? AND p.status = 'paid'
    AND (p.tenant_name LIKE ? OR u.email LIKE ?)
    ORDER BY p.created_at DESC
");

try {
    $stmt->execute([$owner_id, $searchParam, $searchParam]);
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Tenants</title>
    <link rel="stylesheet" href="styles/style_owner_db.css">
    <style>
        .tenant-table { width: 200%; border-collapse: collapse; margin-top: 20px; }
        .tenant-table th, .tenant-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .tenant-table th { background-color: #f2f2f2; }
        .no-tenants { text-align: center; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="owner-title">View My Tenants</div>
    <div class="right-section">
        <form method="GET" action="view_tenant.php" style="display: flex;">
            <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>" />
            <button type="submit" class="search-btn">Search</button>
        </form>
        <button onclick="window.location.href='owner_dashboard.php'">Back to Dashboard</button>
        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>
</nav>

<main class="apartment-grid">
    <?php if (count($tenants) === 0): ?>
        <p class="no-tenants" style="grid-column: 1/-1;">No paid tenants found.</p>
    <?php else: ?>
        <table class="tenant-table">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Email</th>
                    <th>Apartment Room</th>
                    <th>Amount Paid</th>
                    <th>Payment Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $tenant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tenant['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['tenant_email']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['apartment_title']); ?></td>
                        <td>₱<?php echo number_format($tenant['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($tenant['payment_date']))); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($tenant['due_date']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

</body>
</html>