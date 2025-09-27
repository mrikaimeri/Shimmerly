<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brandyrose: #B29079;
            --peachcream: #EFE7DA;
            --neutral: #C1B6A4;
            --whitebeige: #F6F5EC;
            --chalkbeige: #E1DACA;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--whitebeige);
            color: #333;
        }

        header {
            background-color: var(--brandyrose);
            padding: 1rem;
            color: white;
            text-align: center;
        }

        nav {
            background-color: var(--chalkbeige);
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        nav a {
            color: var(--brandyrose);
            text-decoration: none;
            font-weight: bold;
        }

        .dashboard {
            max-width: 900px;
            margin: 2rem auto;
            background-color: var(--peachcream);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 2rem;
        }

        .stat {
            background-color: var(--chalkbeige);
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
            width: 30%;
        }

        .stat h3 {
            margin: 0.5rem 0;
            color: var(--brandyrose);
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .actions a {
            background-color: var(--neutral);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
        }

        .logout {
            margin-top: 2rem;
            text-align: center;
        }

        .logout a {
            color: var(--brandyrose);
            text-decoration: none;
            font-style:bold;

        }
        .go-back-home {
    display: inline-block;
    margin-top: 30px;
    padding: 10px 20px;
    background-color: #B29079; 
    color: #fff;
    text-decoration: none;
    font-family: 'DM Serif Text', serif;
    border-radius: 12px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


$products_count = $conn->query("SELECT COUNT(*) AS count FROM products")->fetch_assoc()['count'];
$orders_count = $conn->query("SELECT COUNT(*) AS count FROM orders")->fetch_assoc()['count'];
$users_count = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
?>

<header>
    <h1>Welcome,  <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
</header>

<nav>
    <a href="admin\manage_products.php"><i class="fas fa-box"></i> Products</a>
    <a href="admin\view_orders.php"><i class="fas fa-receipt"></i> Orders</a>
    <a href="admin\view_messages.php"><i class="fas fa-envelope"></i> Messages</a>
</nav>

<div class="dashboard">
    <div class="stats">
        <div class="stat">
            <h3><?= $products_count ?></h3>
            <p>Products</p>
        </div>
        <div class="stat">
            <h3><?= $orders_count ?></h3>
            <p>Orders</p>
        </div>
        <div class="stat">
            <h3><?= $users_count ?></h3>
            <p>Users</p>
        </div>
    </div>

    <div class="actions">
        <a href="admin\manage_products.php">Manage Products</a>
        <a href="admin\view_orders.php">View Orders</a>
        <a href="admin\view_messages.php">View Messages</a>
        <a href="view_users.php">View Users</a>


    </div>

    <div class="logout">
        <a href="admin\logout.php">Log Out</a>
    </div>
    <a href="../index.php" class="go-back-home">← Go Back Home</a></div>

</body>
</html>
