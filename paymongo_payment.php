<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$apartment_id = filter_input(INPUT_GET, 'apartment_id', FILTER_VALIDATE_INT);
$is_extension = isset($_GET['extension']) && $_GET['extension'] == 1;

if (!$apartment_id) {
    die('Invalid apartment ID');
}

$stmt = $pdo->prepare("SELECT * FROM apartments WHERE id = ?");
$stmt->execute([$apartment_id]);
$apartment = $stmt->fetch();
if (!$apartment) {
    die('Apartment not found');
}

$amount = (int)($apartment['price'] * 100);
$minAmount = 10000;
if ($amount < $minAmount) {
    die('Error: The rent amount must be at least ₱100.00.');
}

$description = 'Payment for ' . htmlspecialchars($apartment['title']) . ($is_extension ? ' (Extension)' : '');
$tenant_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch();
$tenant_name = $tenant ? $tenant['username'] : 'Unknown';

$_SESSION['apartment_id'] = $apartment_id;
$_SESSION['is_extension'] = $is_extension;  // Store for webhook
$isExtensionFlag = $is_extension ? 1 : 0;
$paymongoSecretKey = $paymongoSecretKey ?? 'sk_test_dY6YCYvsQHrNusqsBCwjhiQn';

$data = [
    "data" => [
        "attributes" => [
            "amount" => $amount,
            "currency" => "PHP",
            "description" => $description,
            "remarks" => "Apartment Rent Payment" . ($is_extension ? ' - Extension' : ''),
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/links");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($paymongoSecretKey . ":")
]);

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($result === false || $http_code !== 200) {
    $error_details = $result ? json_decode($result, true)['errors'][0]['detail'] ?? 'Unknown API error' : 'cURL request failed';
    die('Payment link creation failed. HTTP Code: ' . $http_code . '. Details: ' . htmlspecialchars($error_details));
}
$response = json_decode($result, true);
$link_id = $response['data']['id'];
$checkout_url = $response['data']['attributes']['checkout_url'];

if (!savePayment($tenant_id, $tenant_name, $apartment_id, $link_id, $apartment['price'])) {
    die('Error saving payment record.');
}

header("Location: " . $checkout_url);
exit();
?>