<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = '/shimmerly/user_dashboard.php';
    header('Location: /shimmerly/admin/admin/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$result = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$result->bind_param("i", $user_id);
$result->execute();
$orders = $result->get_result();
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8" />
    <title>Order History</title>
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
            max-width: 900px;
            margin: auto;
        }
        h2 {
            color: var(--brandyrose);
            font-size: 36px;
            text-align: center;
            margin-bottom: 40px;
        }
        .order {
            background-color: var(--peachcream);
            border-radius: 12px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .order h4 {
            margin: 0 0 15px 0;
            color: var(--brandyrose);
            font-size: 24px;
        }
        ul.items-list {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }
        ul.items-list li {
            padding: 10px 15px;
            border-bottom: 1px solid var(--chalkbeige);
            font-size: 16px;
            color: var(--neutral);
        }
        ul.items-list li:last-child {
            border-bottom: none;
        }
        .product-name {
            font-weight: bold;
            color: var(--brandyrose);
        }
        .product-desc {
            font-style: italic;
            color: var(--neutral);
            margin-left: 5px;
        }
    </style>
</head>
<body>

<h2>Order History</h2>

<?php if ($orders->num_rows === 0): ?>
    <p style="text-align:center; color: var(--brandyrose); font-size:18px;">You haven't ordered yet.</p>
<?php else: ?>

    <?php while ($order = $orders->fetch_assoc()): ?>
        <div class="order">
            <h4>Order #<?= htmlspecialchars($order['id']) ?> - <?= htmlspecialchars($order['created_at']) ?> - Total: <?= number_format($order['total'], 2) ?> €</h4>
            <?php
            $stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt_items->bind_param("i", $order['id']);
            $stmt_items->execute();
            $items = $stmt_items->get_result();
            ?>
            <ul class="items-list">
                <?php while ($item = $items->fetch_assoc()): ?>
                    <?php
                    $stmt_product = $conn->prepare("SELECT name, description FROM products WHERE id = ?");
                    $stmt_product->bind_param("i", $item['product_id']);
                    $stmt_product->execute();
                    $product_result = $stmt_product->get_result();
                    $product = $product_result->fetch_assoc();
                    ?>
                    <li>
                        <span class="product-name"><?= htmlspecialchars($product['name']) ?></span>
                        <span class="product-desc"> - <?= htmlspecialchars($product['description']) ?></span><br>
                        Quantity: <?= (int)$item['quantity'] ?>, 
                        Price: <?= number_format($item['price'], 2) ?> €
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endwhile; ?>

<?php endif; ?>

</body>
</html>
