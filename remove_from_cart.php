<?php
session_start();

if (isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['success'] = "Product is removed from the cart.";
    }
}

header("Location: view_cart.php");
exit;
