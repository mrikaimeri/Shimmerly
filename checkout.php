<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
    // echo "ID e përdoruesit të kyçur: " . $_SESSION['user_id'];
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = '/shimmerly/checkout.php';
    header('Location: admin/admin/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "<p style='text-align:center; margin-top:50px; font-size:1.2rem; color:#B29079;'>There are no products in the cart.</p>";
    exit;
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, created_at, full_name, address, phone) VALUES (?, ?, NOW(), ?, ?, ?)");
    $stmt->bind_param("idsss", $user_id, $total, $full_name, $address, $phone);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
    }

    unset($_SESSION['cart']);
    $_SESSION['success'] = "Order completed successfully.";
    header("Location: order_history.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8" />
    <title>Checkout - Shimmerly</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text&display=swap" rel="stylesheet">
    <style>
        :root {
            --brandyrose: #B29079;
            --peachcream: #EFE7DA;
            --neutral: #C1B6A4;
            --whitebeige: #F6F5EC;
            --chalkbeige: #E1DACA;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Serif Text', serif;
            background-color: var(--whitebeige);
            color: var(--brandyrose);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        form {
            background-color: var(--peachcream);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            max-width: 450px;
            width: 100%;
        }

        h2 {
            font-weight: 400;
            font-size: 2.5rem;
            margin-bottom: 25px;
            text-align: center;
            color: var(--brandyrose);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 1.1rem;
            color: var(--brandyrose);
        }
        #postal_code{
            font-size: 1.1rem;
            color: var(--brandyrose);
        }

        #city{
            font-size: 1.1rem;
            color: var(--brandyrose)!important;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid var(--neutral);
            border-radius: 8px;
            font-family: 'DM Serif Text', serif;
            font-size: 1rem;
            color: var(--brandyrose);
            background-color: var(--whitebeige);
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: var(--brandyrose);
            outline: none;
        }

        textarea {
            min-height: 80px;
        }

        p {
            font-size: 1.2rem;
            font-weight: 500;
            margin: 20px 0 30px;
            color: var(--brandyrose);
            text-align: center;
            border-top: 1px solid var(--neutral);
            padding-top: 15px;
        }

        button {
            width: 100%;
            background-color: var(--brandyrose);
            border: none;
            padding: 15px 0;
            font-size: 1.2rem;
            color: var(--whitebeige);
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'DM Serif Text', serif;
        }

        button:hover {
            background-color: var(--neutral);
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 10px;
            }
            form {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>

<form method="POST" action="">
    <h2>Checkout</h2>

    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name" required>

    <label for="address">Address:</label>
    <textarea id="address" name="address" required></textarea>

    <label for="city">City:</label>
    <input type="text" id="city" name="city" required>

    <label for="postal_code">Zip Code:</label>
    <input type="text" id="postal_code" name="postal_code" required>

    <label for="phone">Phone Number:</label>
<input type="text" id="phone" name="phone" required>


    <p>Total: <?= number_format($total, 2) ?> €</p>

    <button type="submit">Complete the payment</button>
</form>

</body>
</html>
