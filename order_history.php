<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8" />
    <title>Order History - Shimmerly</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text&display=swap" rel="stylesheet">
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
            color: var(--brandyrose);
            margin: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 700px;
            width: 100%;
        }

        h2 {
            text-align: center;
            font-weight: 400;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }

        .order-card {
            background-color: var(--peachcream);
            border-radius: 12px;
            padding: 20px 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .order-card p {
            margin: 6px 0;
            font-size: 1.1rem;
        }

        .order-card p strong {
            color: var(--neutral);
        }

        ul {
            list-style-type: disc;
            margin-left: 20px;
            margin-top: 10px;
            color: var(--brandyrose);
        }

        ul li {
            margin-bottom: 6px;
            font-size: 1rem;
        }

        @media (max-width: 480px) {
            body {
                margin: 20px 10px;
            }
            .order-card {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order History</h2>

        <?php if ($orders->num_rows === 0): ?>
            <p style="text-align:center; font-size:1.2rem;">Nuk keni asnjë porosi deri tani.</p>
        <?php else: ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-card">
                    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['id']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                    <p><strong>Total:</strong> <?= number_format($order['total'], 2) ?> €</p>

                    <?php
                    $stmt_items = $conn->prepare("SELECT oi.quantity, oi.price, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                    $stmt_items->bind_param("i", $order['id']);
                    $stmt_items->execute();
                    $items = $stmt_items->get_result();
                    ?>

                    <ul>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <li><?= htmlspecialchars($item['name']) ?> - Quantity: <?= $item['quantity'] ?> - Price: <?= number_format($item['price'], 2) ?> €</li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>
