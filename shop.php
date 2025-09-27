<?php
session_start();
require_once 'db.php';


if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}


$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title> Shimmerly Shop</title>
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
            margin: 30px;
            color: var(--neutral);
        }

        h3 {
            font-size: 32px;
            font-weight: 800;
            color: var(--brandyrose);
            margin-bottom: 25px;
            text-align: center;
        }
        #font-aw-icon{
            link-style:none;
            text-decoration:none;
            color:var(--brandyrose);
            font:20px;
        }

        .btn-info {
            background-color: var(--brandyrose);
            border: none;
            color: var(--whitebeige);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }

        .btn-info:hover {
            background-color: var(--neutral);
            color: var(--whitebeige);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background-color: var(--peachcream);
            border-radius: 15px;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin-bottom:20px;
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .no-image {
            height: 200px;
            background: var(--chalkbeige);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--neutral);
            font-style: italic;
        }

        .card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-size: 24px;
            color: var(--brandyrose);
            margin-bottom: 10px;
        }

        .card-text {
            flex-grow: 1;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .price-stock {
            font-weight: 700;
            margin-bottom: 10px;
        }

        input[type="number"] {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid var(--neutral);
            font-family: 'DM Serif Text', serif;
            margin-bottom: 10px;
        }

        button {
            background-color: var(--brandyrose);
            color: var(--whitebeige);
            padding: 12px;
            font-weight: 700;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: var(--neutral);
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['error'])): ?>
    <div style="color: red;">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div style="color: green;">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>

<?php endif; ?>
<a href="view_cart.php" id="font-aw-icon" ><i class="fa-solid fa-cart-shopping"> View Cart</i></a>
<a href="admin\admin\logout.php" class="btn btn-danger" id="font-aw-icon" style="float:right;"><i class="fa-solid fa-right-from-bracket"></i></a>



<h3>Products</h3>


<div class="products-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <?php if ($row['image'] && file_exists("imgs/" . $row['image'])): ?>
                <img src="imgs/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <?php else: ?>
                <div class="no-image">No Image</div>
            <?php endif; ?>
            <div class="card-body">
                <h5><?= htmlspecialchars($row['name']) ?></h5>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <p>Price: <?= number_format($row['price'], 2) ?> €</p>
                <p>Quantity: <?= (int)$row['stock'] ?></p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= (int)$row['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= (int)$row['stock'] ?>">
                    <button type="submit" name="add_to_cart">Add to cart</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>

</div>

<?php
include 'footer.php'
?>

</body>
</html>
