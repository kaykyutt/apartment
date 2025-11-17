<?php
require 'config.php';

$webhookSecret = $paymongoWebhookSecret ?? 'whsk_Zi6PzGwPv5aG1Dr2aJeCHm63';

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';

error_log('Webhook payload: ' . $payload);

if (!verifySignature($payload, $signature, $webhookSecret)) {
    error_log('Webhook: Invalid signature');
    http_response_code(400);
    die('Invalid signature');
}

$data = json_decode($payload, true);

if ($data['data']['attributes']['type'] === 'link.payment.paid') {
    $linkId = $data['data']['id'];
    $reference = $data['data']['attributes']['reference_number'] ?? null;
    
    error_log('Processing payment for link: ' . $linkId);
    
    try {
        $stmt = $pdo->prepare("SELECT tenant_id, apartment_id, is_extension FROM payments WHERE paymongo_link_id = ?");
        $stmt->execute([$linkId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            error_log('Webhook: Payment not found for link ' . $linkId);
            http_response_code(404);
            die('Payment not found');
        }
        
        $tenantId = $payment['tenant_id'];
        $apartmentId = $payment['apartment_id'];
        $isExtension = $payment['is_extension'] ?? 0;
        
        updatePaymentStatus($linkId, 'paid', $reference);
        
        // Check if rental already exists
        if ($data['data']['attributes']['type'] === 'link.payment.paid') {
        $stmt = $pdo->prepare("SELECT id FROM rentals WHERE tenant_id = ? AND apartment_id = ?");
        $stmt->execute([$tenantId, $apartmentId]);
        $existingRental = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingRental && $isExtension) {
            // Extension: Update due date +30 days
            $stmt = $pdo->prepare("UPDATE rentals SET due_date = DATE_ADD(due_date, INTERVAL 30 DAY) WHERE id = ?");
            $stmt->execute([$existingRental['id']]);
            error_log('Extension: Due date updated for rental ' . $existingRental['id']);
        } elseif (!$existingRental && !$isExtension) {
            // Initial payment: Insert new rental
            $stmt = $pdo->prepare("INSERT INTO rentals (tenant_id, apartment_id, status, payment_id, due_date) VALUES (?, ?, 'paid', ?, DATE_ADD(NOW(), INTERVAL 30 DAY))");
            $stmt->execute([$tenantId, $apartmentId, $linkId]);
            error_log('Rental inserted for tenant ' . $tenantId);
        } else {
            error_log('Webhook: Invalid state - extension without rental or initial with existing rental');
        }
        
        // Update apartment status
        $stmt = $pdo->prepare("UPDATE apartments SET status = 'occupied' WHERE id = ?");
        $stmt->execute([$apartmentId]);
    
        // Send email to owner
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = (SELECT owner_id FROM apartments WHERE id = ?)");
        $stmt->execute([$apartmentId]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($owner) {
            $subject = 'Tenant Payment Notification';
            $message = "A tenant has paid for an apartment.";
            sendEmail($owner['email'], $subject, $message);
        }}
        
    } catch (PDOException $e) {
        error_log('Webhook DB Error: ' . $e->getMessage());
        http_response_code(500);
        die('Database error');
    }
}

http_response_code(200);
echo 'OK';

function verifySignature($payload, $signature, $secret) {
    $expectedSignature = hash_hmac('sha256', $payload, $secret);
    return hash_equals($expectedSignature, $signature);
}
?>
