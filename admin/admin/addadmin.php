
<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/shimmerly/db.php');
$error = '';
$conn = new mysqli($host, $db_user, $db_pass, $db_name);


// Username i adminit
$adminUsername = "Admin"; // ose "admin", varësisht si e ke në tabelë

// Password i ri që do përdoret
$newPassword = "admin12345"; // vendos password të ri të sigurt

// Gjenero hash me bcrypt
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

// Update password në databazë
$sql = "UPDATE users SET password='$hashedPassword' WHERE name='$adminUsername' AND role='admin'";

if ($conn->query($sql) === TRUE) {
    echo "Password i adminit u ndryshua me sukses!";
} else {
    echo "Gabim: " . $conn->error;
}

$conn->close();
?>
