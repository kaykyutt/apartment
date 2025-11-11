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


$stmt = $pdo->prepare("SELECT * FROM apartments WHERE id = ? AND owner_id = ?");
$stmt->execute([$apartment_id, $owner_id]);
$apartment = $stmt->fetch();

if (!$apartment) {
    die('Apartment not found or no access.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $picturePath = $apartment['picture'];

 
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = basename($_FILES['picture']['name']);
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
            $picturePath = $targetFile;
          
            if ($apartment['picture'] && file_exists($apartment['picture'])) {
                @unlink($apartment['picture']);
            }
        } else {
            $error = "Failed to upload new picture.";
        }
    }

    if (!$error) {
        $updateStmt = $pdo->prepare("UPDATE apartments SET title = ?, description = ?, price = ?, picture = ? WHERE id = ? AND owner_id = ?");
        if ($updateStmt->execute([$title, $description, $price, $picturePath, $apartment_id, $owner_id])) {
            header("Location: apartment_details.php?id=$apartment_id");
            exit;
        } else {
            $error = "Failed to update apartment details.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Apartment</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="style_edit.css">
</head>
<body>

<div class="top-bar" onclick="window.location.href='apartment_details.php?id=<?php echo $apartment_id; ?>'">&lt; Back</div>

<div class="container">
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <label for="title">Apartment Title</label>
        <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($apartment['title']); ?>" />

        <label for="description">Description</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($apartment['description']); ?></textarea>

        <label for="price">Price (₱)</label>
        <input type="number" id="price" name="price" required step="0.01" value="<?php echo htmlspecialchars($apartment['price']); ?>" />

        <label for="picture">Change Picture (Leave empty to keep current)</label>
        <input type="file" id="picture" name="picture" accept="image/*" />

        <p>Current Picture:</p>
        <?php if ($apartment['picture'] && file_exists($apartment['picture'])): ?>
            <img src="<?php echo htmlspecialchars($apartment['picture']); ?>" alt="Current Picture" class="current-picture" />
        <?php else: ?>
            <p><em>No current picture available.</em></p>
        <?php endif; ?>

        <button type="submit" class="save-btn">Save Changes</button>
    </form>
</div>

</body>
</html>