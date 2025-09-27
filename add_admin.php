<?php
require_once('db.php');

$newPassword = "726899";
$hashed = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$email = "admin@shimmerly.com"; 
$stmt->bind_param('ss', $hashed, $email);

if ($stmt->execute()) {
    echo "Password is updated.";
} else {
    echo "Something went wrong: " . $stmt->error;
}

$stmt->close();
?>
