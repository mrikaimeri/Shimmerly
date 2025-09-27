<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;

// Llogarit totalin
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Shto porosine
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, order_date) VALUES (?, ?, NOW())");
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;

// Shto artikujt e porosise
$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($_SESSION['cart'] as $item) {
    $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
    $stmt_item->execute();
}

// Pastro cart
unset($_SESSION['cart']);

header("Location: thankyou.php");
exit();
?>
