<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Nëse shporta është bosh, shfaq këtë faqe dhe ndalo më tej ekzekutimin
    ?>
    <!DOCTYPE html>
    <html lang="sq">
    <head>
        <meta charset="UTF-8">
        <title>Cart</title>
        <style>
            :root {
                --brandyrose: #B29079;
                --peachcream: #EFE7DA;
                --neutral: #C1B6A4;
                --whitebeige: #F6F5EC;
                --chalkbeige: #E1DACA;
            }

            body {
                font-family: 'DM Serif Text', serif;
                background-color: var(--whitebeige);
                color: var(--neutral);
                padding: 40px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }

            h2 {
                font-size: 32px;
                color: var(--brandyrose);
                margin-bottom: 30px;
                text-align: center;
            }

            .button {
                background-color: var(--brandyrose);
                color: var(--whitebeige);
                padding: 12px 20px;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
                font-size: 16px;
                transition: background-color 0.3s ease;
                border: none;
                cursor: pointer;
            }

            .button:hover {
                background-color: var(--neutral);
            }
        </style>
    </head>
    <body>
        <h2>Your cart is empty.</h2>
        <a href="shop.php" class="button">Go to the shop</a>
    </body>
    </html>
    <?php
    exit;  // ndalon më tej ekzekutimin nëse shporta është bosh
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <style>
        /* Këtu vendos stilimet e tua ekzistuese për shportën */
        :root {
            --brandyrose: #B29079;
            --peachcream: #EFE7DA;
            --neutral: #C1B6A4;
            --whitebeige: #F6F5EC;
            --chalkbeige: #E1DACA;
        }

        body {
            font-family: 'DM Serif Text', serif;
            background-color: var(--whitebeige);
            color: var(--neutral);
            padding: 40px;
        }

        h2 {
            text-align: center;
            font-size: 32px;
            color: var(--brandyrose);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--peachcream);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        th, td {
            padding: 16px;
            border-bottom: 1px solid var(--chalkbeige);
            text-align: center;
        }

        th {
            background-color: var(--brandyrose);
            color: white;
            font-size: 18px;
        }

        td {
            font-size: 16px;
        }

        input[type="number"] {
            width: 70px;
            padding: 8px;
            border: 1px solid var(--neutral);
            border-radius: 6px;
            font-family: 'DM Serif Text', serif;
            background-color: var(--whitebeige);
            color: var(--neutral);
        }

        .button {
            background-color: var(--brandyrose);
            color: var(--whitebeige);
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 5px;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .button:hover {
            background-color: var(--neutral);
        }

        .danger {
            background-color: #d9534f;
        }

        .danger:hover {
            background-color: #c9302c;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
        }

        h3 {
            text-align: right;
            margin-top: 20px;
            color: var(--brandyrose);
        }
    </style>
</head>
<body>

<h2>My Cart</h2>
<?php if (isset($_SESSION['success'])): ?>
    <div style="color: green; background-color: #dff0d8; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
    <div style="color: #a94442; background-color: #f2dede; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="list-style-type: none; padding-left: 0;">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li>⚠ <?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<form method="POST" action="update_cart.php">
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_price = 0;
        foreach ($_SESSION['cart'] as $product_id => $item):
            $subtotal = $item['price'] * $item['quantity'];
            $total_price += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= number_format($item['price'], 2) ?> €</td>
            <td>
                <input type="number" name="quantities[<?= (int)$product_id ?>]" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)($item['stock'] ?? 100) ?>">
            </td>
            <td><?= number_format($subtotal, 2) ?> €</td>
            <td>
                <a href="remove_from_cart.php?product_id=<?= (int)$product_id ?>" class="button danger">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>Total: <?= number_format($total_price, 2) ?> €</h3>

<button type="submit" class="button">Update your cart</button>
</form>

<br><br>
<a href="shop.php" class="button">Continue Shopping</a>
<a href="checkout.php" class="button">Checkout</a>

</body>
</html>
