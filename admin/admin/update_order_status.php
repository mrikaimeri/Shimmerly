<?php
session_start();
require '../../db.php';  

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $status = isset($_POST['status']) ? strtolower(trim($_POST['status'])) : '';

    $valid_statuses = ['pending', 'confirmed', 'cancelled'];
    if ($order_id > 0 && in_array($status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
    }
}

header("Location: view_orders.php"); 
exit();
