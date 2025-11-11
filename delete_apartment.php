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


$stmt = $pdo->prepare("SELECT picture FROM apartments WHERE id = ? AND owner_id = ?");
$stmt->execute([$apartment_id, $owner_id]);
$apartment = $stmt->fetch();

if (!$apartment) {
    die('Apartment not found or you do not have permission to delete it.');
}


if ($apartment['picture'] && file_exists($apartment['picture'])) {
    @unlink($apartment['picture']);
}


$deleteStmt = $pdo->prepare("DELETE FROM apartments WHERE id = ? AND owner_id = ?");
$deleteStmt->execute([$apartment_id, $owner_id]);


header('Location: owner_dashboard.php');
exit;
?>
