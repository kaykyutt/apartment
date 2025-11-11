<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Apartment Success</title>
<link rel="stylesheet" href="style_success.css">
</head>
<body>

<a href="owner_dashboard.php" class="back" aria-label="Back to Dashboard">
   <svg width="30" height="30" viewBox="0 0 20 20" fill="#FF6600" xmlns="http://www.w3.org/2000/svg">
     <path d="M14 18L6 10L14 2" stroke="#FF6600" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
   </svg>
   Back
</a>

<div class="container">
  <div class="checkmark">&#10004;</div>
  <h2>Apartment Successfully Added</h2>
</div>
<div class="orange-curve"></div>

</body>
</html>
