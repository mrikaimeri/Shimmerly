<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Merr të dhënat e produktit
    $stmt = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        $_SESSION['error'] = "Product not found.";
        header('Location: shop.php');
        exit;
    }

    if ($quantity < 1 || $quantity > $product['stock']) {
        $_SESSION['error'] = "The requested quantity is not valid.";
        header('Location: shop.php');
        exit;
    }

    // Shto në SESSION cart për kontroll të shpejtë në frontend
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
        if ($new_quantity > $product['stock']) {
            $_SESSION['error'] = "There is not enough stock for this product.";
            header('Location: shop.php');
            exit;
        }
        $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'stock' => $product['stock']
        ];
    }

    // Ruaj edhe në databazë
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $session_id = session_id();
    $status = 'active';

    // Kontrollo neser ekziston per ket produkt dhe session/user
    if ($user_id) {
        $checkStmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $checkStmt->bind_param("ii", $user_id, $product_id);
    } else {
        $checkStmt = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $checkStmt->bind_param("si", $session_id, $product_id);
    }

    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $existing = $checkResult->fetch_assoc();
        $new_quantity = $existing['quantity'] + $quantity;
        if ($new_quantity > $product['stock']) {
            $new_quantity = $product['stock'];
        }
        $updateStmt = $conn->prepare("UPDATE cart SET quantity = ?, added_at = NOW(), status = ? WHERE id = ?");
        $updateStmt->bind_param("isi", $new_quantity, $status, $existing['id']);
        $updateStmt->execute();
    } else {
        $insertStmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, added_at, session_id, status) VALUES (?, ?, ?, NOW(), ?, ?)");
        $insertStmt->bind_param("iiiss", $user_id, $product_id, $quantity, $session_id, $status);
        $insertStmt->execute();
    }

    $_SESSION['success'] = "The product is added successfully.";
    header('Location: shop.php');
    exit;
}
?>

