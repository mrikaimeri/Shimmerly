<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
    $errors = [];

    foreach ($_POST['quantities'] as $product_id => $qty) {
        $product_id = (int)$product_id;
        $qty = (int)$qty;

        if (!isset($_SESSION['cart'][$product_id])) {
            $errors[] = "Product with ID $product_id is not found at the cart.";
            continue;
        }

       
        $_SESSION['cart'][$product_id]['quantity'] = $qty;
    }

    if (empty($errors)) {
        $_SESSION['success'] = "The cart is updated successfully";
    } else {
        $_SESSION['errors'] = $errors;
    }
}

header("Location: view_cart.php");
exit;
