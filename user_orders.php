<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: admin/admin/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

require_once 'db.php';

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>User orders - Shimmerly</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Text&display=swap');

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
            margin: 40px auto;
            max-width: 900px;
            color: var(--neutral);
            padding: 0 20px;
        }

        h2 {
            font-size: 36px;
            font-weight: 800;
            color: var(--brandyrose);
            margin-bottom: 30px;
            text-align: center;
        }

        .order-box {
            background-color: var(--peachcream);
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            padding: 25px 30px;
            margin-bottom: 30px;
            color: var(--neutral);
        }

        .order-box h3 {
            font-size: 24px;
            margin-bottom: 12px;
            color: var(--brandyrose);
        }

        .order-box p {
            font-size: 18px;
            margin: 6px 0;
        }

        .order-box strong {
            color: var(--brandyrose);
        }

        ul {
            margin-top: 15px;
            padding-left: 22px;
            color: var(--neutral);
        }

        li {
            margin-bottom: 8px;
            font-size: 16px;
        }

        .no-orders {
            text-align: center;
            font-style: italic;
            color: var(--neutral);
            font-size: 18px;
            margin-top: 50px;
        }

        a.back-link {
            display: inline-block;
            margin-top: 40px;
            font-weight: 700;
            font-size: 18px;
            color: var(--brandyrose);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a.back-link:hover {
            color: var(--neutral);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2>User orders</h2>

    <?php if ($orders_result->num_rows > 0): ?>
        <?php while ($order = $orders_result->fetch_assoc()): ?>
            <div class="order-box">
                <h3>Order #<?= $order['id']; ?> - <?= date("d M Y, H:i", strtotime($order['created_at'])); ?></h3>
                <p>Status: <strong><?= ucfirst($order['status']); ?></strong></p>

                <p><strong>Products:</strong></p>
                <ul>
                    <?php
                    $order_id = $order['id'];
                    $item_stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
                    $item_stmt->bind_param("i", $order_id);
                    $item_stmt->execute();
                    $items = $item_stmt->get_result();
                    while ($item = $items->fetch_assoc()):
                    ?>
                        <li>
                            <?= htmlspecialchars($item['product_name']); ?> - 
                            <?= (int)$item['quantity']; ?>  x €<?= number_format($item['price'], 2); ?> = 
                            €<?= number_format($item['price'] * $item['quantity'], 2); ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-orders">There is no order.</p>
    <?php endif; ?>

    <a href="index.php" class="back-link">⟵ Go back</a>

</body>
</html>
