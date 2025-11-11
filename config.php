<?php

$host = 'localhost';
$dbname = 'apartment_rental';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Email config (PHPMailer)
$smtpHost = 'smtp.gmail.com'; // Or your SMTP
$smtpUsername = 'your-email@gmail.com';
$smtpPassword = 'your-app-password'; // Use app password for Gmail
$smtpPort = 587;

// PayMongo config (replace with your keys)
$paymongoSecretKey = 'sk_test_dY6YCYvsQHrNusqsBCwjhiQn'; // From PayMongo dashboard
$paymongoPublicKey = 'pk_test_ocZqoTLgmHFzM7nQ2z6EaGyc';
?>