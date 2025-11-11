<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tenant') {
    header('Location: login.php');
    exit;
}

$apartment_id = $_GET['apartment_id'] ?? null;
if (!$apartment_id) die('Invalid apartment ID');

$stmt = $pdo->prepare("SELECT * FROM apartments WHERE id = ?");
$stmt->execute([$apartment_id]);
$apartment = $stmt->fetch();
if (!$apartment) die('Apartment not found');

$amount = (int)($apartment['price'] * 100); // In cents
$description = 'Payment for ' . $apartment['title'];

$paymongo_secret_key = 'sk_test_dY6YCYvsQHrNusqsBCwjhiQn'; // Your secret key

$data = [
    'data' => [
        'attributes' => [
            'amount' => $amount,
            'currency' => 'PHP',
            'description' => $description,
            'payment_method_allowed' => ['card'],
            'payment_method_options' => [
                'card' => ['request_three_d_secure' => 'any']
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/payment_intents');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($paymongo_secret_key . ':'),
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 201) {
    echo 'Payment setup failed. Response: ' . $response;
    exit;
}

$result = json_decode($response, true);
$payment_intent_id = $result['data']['id'];
$client_key = $result['data']['attributes']['client_key'];


header('Location: https://checkout.paymongo.com/' . $client_key);
exit;
?>
