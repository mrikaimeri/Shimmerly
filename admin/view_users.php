<?php
session_start();
include '../db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$query = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>View Users</title>
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
        background-color: var(--peachcream);
        color: white;
        font-family: 'DM Serif Text', serif;
        margin: 0;
        padding: 40px 20px;
    }

    h2 {
        text-align: center;
        color: var(--brandyrose);
        margin-bottom: 30px;
        font-size: 2.8rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--brandyrose);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid var(--chalkbeige);
    }

    th {
        background-color: var(--neutral);
        color: var(--whitebeige);
        font-size: 1.1rem;
        letter-spacing: 0.05em;
    }

    tbody tr:hover {
        background-color: var(--chalkbeige);
        color: var(--brandyrose);
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    a {
        display: inline-block;
        margin-top: 25px;
        color: var(--brandyrose);
        font-weight: bold;
        text-decoration: none;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }

    a:hover {
        color: var(--neutral);
    }
</style>
</head>
<body>
    <h2>Registered Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered On</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
