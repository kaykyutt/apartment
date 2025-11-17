<?php
require 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // From Composer

function sendNotification($user_id, $type, $message) {
    global $pdo, $smtpHost, $smtpUsername, $smtpPassword, $smtpPort;

    // Get user email
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $email = $stmt->fetch()['email'];

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtpPort;

        $mail->setFrom($smtpUsername, 'Apartment Rental');
        $mail->addAddress($email);
        $mail->Subject = 'Notification';
        $mail->Body = $message;

        $mail->send();

        // Log notification
        $pdo->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)")->execute([$user_id, $type, $message]);
    } catch (Exception $e) {
        // Log error (in production)
    }
}

// Cron job script to check due dates (run daily via cron: 0 0 * * * php /path/to/check_due_dates.php)
function checkDueDates() {
    global $pdo;
    $today = date('Y-m-d');
    $reminder_date = date('Y-m-d', strtotime('+5 days'));

    $stmt = $pdo->prepare("SELECT r.tenant_id, r.due_date FROM reservations r WHERE r.due_date = ? AND r.status = 'active'");
    $stmt->execute([$reminder_date]);
    $reservations = $stmt->fetchAll();

    foreach ($reservations as $res) {
        sendNotification($res['tenant_id'], 'due_reminder', "Your rent is due on {$res['due_date']}. Please pay soon.");
    }}