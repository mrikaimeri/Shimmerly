<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/shimmerly/db.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /shimmerly/login.php");
    exit;
}

$error = "";
$success = "";
$product = null;

function uploadImage($file) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/shimmerly/imgs/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = uniqid() . "_" . basename($file["name"]);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowedTypes)) {
        return [false, "Only JPG, JPEG, PNG  GIF allowed"];
    }

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return [true, $fileName];
    } else {
        return [false, "Something went wrong"];
    }
}

// Shto produkt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $image_path = "";

    if (!$name || !$description || !$price || $stock === '') {
        $error = "All fields are required";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            list($ok, $res) = uploadImage($_FILES['image']);
            if ($ok) {
                $image_path = $res;
            } else {
                $error = $res;
            }
        } else {
            $error = "Product image is required";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssdsi", $name, $description, $price, $image_path, $stock);
        if ($stmt->execute()) {
            $success = "Product is added successfully";
        } else {
            $error = "Something went wrong";
        }
        $stmt->close();
    }
}

// Perditso produkt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $image_path = null;

    if (!$name || !$description || !$price || $stock === '') {
        $error = "All fields are required";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            list($ok, $res) = uploadImage($_FILES['image']);
            if ($ok) {
                $image_path = $res;
            } else {
                $error = $res;
            }
        }
    }

    if (!$error) {
        if ($image_path) {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?");
            $stmt->bind_param("ssdssi", $name, $description, $price, $stock, $image_path, $id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=?");
            $stmt->bind_param("ssdsi", $name, $description, $price, $stock, $id);
        }
        if ($stmt->execute()) {
            $success = "Product updated successfully";
        } else {
            $error = "Something went wrong";
        }
        $stmt->close();
    }
}
// Fshi produkt
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $conn->query("DELETE FROM order_items WHERE product_id = $id");

    $res = $conn->query("SELECT image FROM products WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['image']) {
            $imgPath = $_SERVER['DOCUMENT_ROOT'] . "/shimmerly/imgs/" . $row['image'];
            if (file_exists($imgPath)) unlink($imgPath);
        }
    }

    $conn->query("DELETE FROM products WHERE id = $id");

    header("Location: manage_products.php");
    exit;
}

// Merr produkt per update
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

// Merr  produktet
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8" />
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text&display=swap" rel="stylesheet" />
    <style>
   :root {
    --brandyrose: #B29079;
    --peachcream: #EFE7DA;
    --neutral: #C1B6A4;
    --whitebeige: #F6F5EC;
    --chalkbeige: #E1DACA;
}

body {
    font-family: "DM Serif Text", serif; 
    background-color: var(--whitebeige);
    color: var(--brandyrose); 
    padding: 20px 15px;
}

h3, h5 {
    font-weight: 800;
    color: var(--brandyrose);
    margin-bottom: 20px;
}

h3 { font-size: 2rem; }
h5 { font-size: 1.5rem; }

table {
    background-color: var(--peachcream);
    border-collapse: collapse;
    box-shadow: 0 0 10px rgba(178, 144, 121, 0.2);
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    font-size: 14px;
    color: var(--neutral);
}

table th, table td {
    border: 1px solid var(--neutral);
    padding: 10px 12px;
    vertical-align: middle;
}

table th {
    background-color: var(--brandyrose);
    color: var(--whitebeige);
    font-weight: 700;
}

.form-control {
    font-family: "DM Serif Text", serif;
    font-size: 15px;
    border: 1px solid var(--neutral);
    border-radius: 6px;
    padding: 8px 12px;
    box-shadow: none;
    transition: border-color 0.3s ease, background-color 0.3s ease;
    background-color: transparent; 
    color: var(--brandyrose);
}

.form-control::placeholder {
    color: rgba(178, 144, 121, 0.6); 
    font-style: italic;
}

.form-control:focus {
    border-color: var(--brandyrose);
    box-shadow: 0 0 8px rgba(178, 144, 121, 0.5);
    outline: none;
    background-color: var(--peachcream); 
    color:var(--brandyrose);
}
.form-label{
    color:var(--brandyrose);
}
.btn-primary {
    background-color: var(--brandyrose);
    border-color: var(--neutral);
    font-weight: 700;
    padding: 8px 20px;
}

.btn-primary:hover {
    background-color: #a67f61;
    border-color: #8a7058;
}

.btn-success {
    background-color: var(--neutral);
    border-color: var(--brandyrose);
    font-weight: 700;
    padding: 8px 20px;
    color: #3c2f20;
}

.btn-success:hover {
    background-color: #b1a38a;
    border-color: #997b64;
    color: #2d2418;
}

.btn-secondary {
    background-color: var(--chalkbeige);
    border-color: var(--neutral);
    color: #5c4a3c;
    font-weight: 600;
    padding: 8px 20px;
}

.btn-secondary:hover {
    background-color: var(--neutral);
    border-color: var(--brandyrose);
    color: #4a3b2d;
}

.alert-success {
    background-color: #dfcbb7;
    color: #6d553f;
    border-color: #c2ab8f;
}

.alert-danger {
    background-color: #f4d5c9;
    color: #9c5744;
    border-color: #dca691;
}

img.product-img {
    max-width: 80px;
    border-radius: 6px;
    object-fit: cover;
}

.actions a {
    margin-right: 10px;
}
     
    </style>
</head>
<body>
    <div class="container">
        <h3>Manage Products</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="card mb-4 p-3" style="background: var(--chalkbeige); border-radius: 8px;">
            <h5><?= $product ? "Update product" : "Add a new product" ?></h5>
            <form method="post" enctype="multipart/form-data" novalidate>
                <?php if ($product): ?>
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>" />
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Product</label>
                    <input type="text" class="form-control" id="name" name="name" required
                        value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price (€)</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required
                        value="<?= htmlspecialchars($product['price'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" min="0" class="form-control" id="stock" name="stock" required
                        value="<?= htmlspecialchars($product['stock'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Products image <?= $product ? "(Leave blank to keep unchanged)" : "" ?></label>
                    <input type="file" class="form-control" id="image" name="image" <?= $product ? '' : 'required' ?> accept=".jpg,.jpeg,.png,.gif" >
                    <?php if ($product && $product['image']): ?>
                        <div class="mt-2">
                            <img src="/shimmerly/imgs/<?= htmlspecialchars($product['image']) ?>" alt="Product img" class="product-img" />
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" name="<?= $product ? 'edit_product' : 'add_product' ?>" class="btn btn-primary">
                    <?= $product ? 'Update Product' : 'Add Product' ?>
                </button>

                <?php if ($product): ?>
                    <a href="manage_products.php" class="btn btn-secondary ms-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <h5>Products List</h5>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Price (€)</th>
                    <th>Stock</th>
                    <th>Photo</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                            <td><?= number_format($row['price'], 2) ?></td>
                            <td><?= (int)$row['stock'] ?></td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="/shimmerly/imgs/<?= htmlspecialchars($row['image']) ?>" alt="Foto" class="product-img" />
                                <?php else: ?>
                                    No photo
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="?edit=<?= (int)$row['id'] ?>" class="btn btn-success btn-sm">Change</a>
                                <a href="?delete=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">There are no products registered yet!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
