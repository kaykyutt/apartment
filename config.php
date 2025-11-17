<?php

$host = 'localhost';
$dbname = 'apartment_rental';
$username = 'root'; 
$password = ''; 
$dev_mode = true; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}


$smtpHost = 'smtp.gmail.com';
$smtpUsername = 'preinkyle@gmail.com';
$smtpPassword = 'rizd loje apzt bsgi';
$smtpPort = 587;

$paymongoSecretKey = 'sk_test_dY6YCYvsQHrNusqsBCwjhiQn';
$paymongoPublicKey = 'pk_test_ocZqoTLgmHFzM7nQ2z6EaGyc';
$paymongoWebhookSecret = 'whsk_Zi6PzGwPv5aG1Dr2aJeCHm63'; 

function savePayment($tenant_id, $tenant_name, $apartment_id, $link_id, $amount, $status = 'paid') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO payments (tenant_id, tenant_name, apartment_id, paymongo_link_id, amount, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tenant_id, $tenant_name, $apartment_id, $link_id, $amount, $status]);
        return true;
    } catch (PDOException $e) {
        error_log("Payment save error: " . $e->getMessage());
        return false;
    }
}


function updatePaymentStatus($link_id, $status, $reference = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE payments SET status = ?, paymongo_reference = ?, updated_at = NOW() WHERE paymongo_link_id = ?");
        $stmt->execute([$status, $reference, $link_id]);
        return true;
    } catch (PDOException $e) {
        error_log("Payment update error: " . $e->getMessage());
        return false;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body) {
    global $smtpHost, $smtpUsername, $smtpPassword, $smtpPort;
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtpPort;
        
        $mail->setFrom($smtpUsername, 'Apartment Rental System');
        $mail->addAddress($to);
        
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email send failed: ' . $mail->ErrorInfo);
        return false;
    }
}
?>