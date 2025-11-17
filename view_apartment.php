<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT p.id AS payment_id, a.id AS apartment_id, a.title, a.description, a.price, p.created_at AS payment_date, p.amount
        FROM payments p
        JOIN apartments a ON p.apartment_id = a.id
        WHERE p.tenant_id = ? AND p.status = 'paid'
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$tenant_id]);
    $apartments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Apartments</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0;
         }
        .navbar { 
            background-color: #FF6600; 
            color: white; padding: 10px;
             display: flex;
              justify-content: space-between;
               align-items: center;
             }
        .tenant-title { 
            font-size: 24px; 
            font-weight: bold;
         }
        .right-section {
             display: flex; 
             gap: 10px; 
            }
        .right-section button {
             background-color: white;
              color: white;
               border: none;
                padding: 8px 12px;
                 cursor: pointer; 
                }
        .right-section button:hover {
             background-color: #e65500;
                color: white;
                
             }
        main { 
            padding: 20px;
         }
        .apartment-card { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin: 10px 0;
             background-color: #f9f9f9; 
            }
        .pay-btn {
             background: #28a745; 
             color: white;
              padding: 10px;
               border: none; 
               cursor: pointer; 
            }
        .pay-btn:hover {
             background: #218838;
             }
.nav-logout {
    background-color: white;
    color: #FF6600;
    font-weight: bold;
    padding: 8px 16px;
    border-radius:8px;
    cursor: pointer;
    border:none;
transition: background-color 0.3s ease;
}
 .nav-logout:hover {
    background-color: #e65500;
    color: white;
  }
 
    </style>
</head>
<body>

<nav class="navbar">
    <div class="tenant-title">My Apartments</div>
    <div>
        <button class="nav-logout" onclick="window.location.href='tenant_dashboard.php'">Back to Dashboard</button>
        <button class="nav-logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</nav>

<main>
    <h2>My Paid Apartments</h2>
    <?php if (count($apartments) === 0): ?>
        <p>No paid apartments found.</p>
    <?php else: ?>
        <?php foreach ($apartments as $apt): ?>
            <div class="apartment-card">
                <h3><?php echo htmlspecialchars($apt['title']); ?></h3>
                <p><?php echo htmlspecialchars($apt['description']); ?></p>
                <p>Price: ₱<?php echo number_format($apt['price'], 2); ?>/month</p>
                <p>Paid On: <?php echo htmlspecialchars(date('Y-m-d', strtotime($apt['payment_date']))); ?></p>
                <p>Amount Paid: ₱<?php echo number_format($apt['amount'], 2); ?></p>
                <button class="pay-btn" onclick="window.location.href='paymongo_payment.php?apartment_id=<?php echo $apt['apartment_id']; ?>&extension=1'">Pay Extension</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>