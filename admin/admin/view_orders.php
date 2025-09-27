<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/shimmerly/db.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {

    header("Location: login.php");
    exit;
}


// Merr te dhënat e porosive dhe produktit
$sql = "SELECT 
    orders.id AS order_id, 
    orders.full_name AS order_full_name,
    orders.address, 
    orders.phone,
    orders.total, 
    orders.status, 
    order_items.quantity, 
    products.description, 
    users.email
FROM orders
LEFT JOIN order_items ON orders.id = order_items.order_id
LEFT JOIN products ON order_items.product_id = products.id
LEFT JOIN users ON orders.user_id = users.id
ORDER BY orders.created_at DESC";


$result = $conn->query($sql);


if (!$result) {
    die("Something went wrong: " . $conn->error);
}

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orderId = $row['order_id'];

    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'id' => $orderId,
            'full_name' => $row['order_full_name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'total_price' => $row['total'],
            'status' => $row['status'],
            'email' => $row['email'],
            'items' => [],
        ];
        
        
    }

    $orders[$orderId]['items'][] = [
        'description' => $row['description'],
        'quantity' => $row['quantity'],
    ];
}
?>


<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8" />
    <title>Orders | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --brandyrose: #B29079;
            --peachcream: #EFE7DA;
            --neutral: #C1B6A4;
            --whitebeige: #F6F5EC;
            --chalkbeige: #E1DACA;
        }
        body {
            background-color: var(--peachcream);
            font-family: 'DM Serif Text', serif;
        }
        .orders-container {
            background: var(--whitebeige);
            border-radius: 12px;
            padding: 30px;
            max-width: 1000px;
            margin: 40px auto;
            box-shadow: 0 4px 20px rgba(178, 144, 121, 0.3);
        }
        h3 {
            color: var(--brandyrose);
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5rem;
        }
        table {
            background: var(--chalkbeige);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(178, 144, 121, 0.2);
        }
        .table-hover thead th {
            background-color: var(--brandyrose);
            color: var(--whitebeige);
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
        }
        tbody td{
            color:var(--brandyrose)!important;
            vertical-align: middle !important;
        }
        tbody tr:hover {
            background-color: var(--neutral);
        }
   
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            color: var(--whitebeige);
            display: inline-block;
        }
        .status.pending {
            background-color: #C1B6A4;
            color: #4b3f35;
        }
        .status.completed {
            background-color: var(--brandyrose);
        }
        .status.cancelled {
            background-color: #a6755a;
        }
    </style>
</head>
<body>
<div class="orders-container">
    <h3>View Orders</h3>

    <table class="table table-hover">
    <thead>
    <tr>
        <th># Order</th>
        <th>Name</th>
        <th>Email</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total price</th>
        <th>Status</th>
    </tr>
</thead>

        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $orderId => $order): ?>
                    <?php 
                        $firstRow = true;
                        $itemsCount = count($order['items'] ?? []);
                        foreach ($order['items'] as $item): 
                    ?>
                        <tr>
                            <?php if ($firstRow): ?>
                                <td rowspan="<?= $itemsCount ?>"><?= htmlspecialchars($orderId) ?></td>
                                <td rowspan="<?= $itemsCount ?>"><?= htmlspecialchars($order['full_name'] ?? '') ?></td>
                                <td rowspan="<?= $itemsCount ?>"><?= htmlspecialchars($order['email'] ?? '') ?></td>
                                <td rowspan="<?= $itemsCount ?>"><?= htmlspecialchars($order['address'] ?? '') ?></td>
                                <td rowspan="<?= $itemsCount ?>"><?= htmlspecialchars($order['phone'] ?? '') ?></td>
                                <?php if ($firstRow): ?>
   
<?php endif; ?>

                            <?php endif; ?>
                            <td><?= htmlspecialchars($item['description'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['quantity'] ?? '') ?></td>
                            <?php if ($firstRow): ?>
                                <td rowspan="<?= $itemsCount ?>">$<?= number_format($order['total_price'] ?? 0, 2) ?></td>
                                <td rowspan="<?= $itemsCount ?>">
                                    <form method="POST" action="update_order_status.php" style="margin:0;">
                                        <input type="hidden" name="order_id" value="<?= isset($order['id']) ? (int)$order['id'] : 0 ?>">
                                        <select name="status" onchange="this.form.submit()" class="status <?= strtolower($order['status'] ?? '') ?>">
                                            <?php
                                            $statuses = ['pending', 'confirmed', 'cancelled'];
                                            foreach ($statuses as $statusOption):
                                                $selected = (strcasecmp($order['status'] ?? '', $statusOption) === 0) ? 'selected' : '';
                                            ?>
                                                <option value="<?= $statusOption ?>" <?= $selected ?>><?= ucfirst($statusOption) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php $firstRow = false; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center" style="color: var(--brandyrose); font-weight: 600;">
                        There are no orders!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>

</html>
