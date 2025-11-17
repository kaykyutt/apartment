<?php
// ... (rest of your code remains the same)

$data = [
    "data" => [
        "attributes" => [
            "amount" => $amount,
            "currency" => "PHP",
            "description" => $description,
            "remarks" => "Apartment Rent Payment",
            "redirect" => [
                "success" => $successUrl,
                "failed" => $cancelUrl
            ],
            "metadata" => [  // Add this for webhook
                "tenant_id" => $_SESSION['user_id'],
                "apartment_id" => $apartment_id
            ]
        ]
    ]
];

// ... (rest of the cURL and redirect code)
?>