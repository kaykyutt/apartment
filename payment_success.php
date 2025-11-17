<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header('Location: login.php');
    exit;
}

$tenant_id = $_SESSION['user_id'];
$apartment_id = $_SESSION['apartment_id'] ?? null;

if (!$apartment_id) {
    die('No apartment selected for payment.');
}




$stmt = $pdo->prepare("UPDATE apartments SET status = 'occupied' WHERE id = ?");
$stmt->execute([$apartment_id]);


$stmt = $pdo->prepare("SELECT owner_id FROM apartments WHERE id = ?");
$stmt->execute([$apartment_id]);
$apartment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$apartment) {
    die('Apartment not found.');
}

$owner_id = $apartment['owner_id'];
$payment_date = date('Y-m-d');
$due_date = date('Y-m-d', strtotime('+30 days'));


$stmt = $pdo->prepare("INSERT INTO tenants (owner_id, tenant_id, apartment_id, payment_date, due_date, status) VALUES (?, ?, ?, ?, ?, 'active')");
$stmt->execute([$owner_id, $tenant_id, $apartment_id, $payment_date, $due_date]);


unset($_SESSION['apartment_id']);


header('Location: tenant_dashboard.php?message=Payment successful! You are now renting this apartment.');
exit;
?>