<?php
session_start();
require_once '../../db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Messages</title>
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
            color: var(--brandyrose);
            padding: 30px 20px;
            font-weight: 400;        
            line-height: 1.6; 
            margin: 0;
        }

        h1 {
            color: var(--brandyrose);
           
            text-align: center;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            margin-bottom: 25px;
            color: var(--brandyrose);
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        a:hover {
            color: var(--neutral);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--peachcream);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--chalkbeige);
            color: var(--brandyrose);
        }

        th {
            background-color: var(--neutral);
            color: var(--whitebeige);
            letter-spacing: 0.05em;
        }

        tbody tr:nth-child(even) {
            background-color: var(--whitebeige);
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

        tbody td {
            vertical-align: top;
            white-space: pre-wrap;
        }

     
    </style>
</head>
<body>

    <h1>Sent Messages</h1>
    <a href="../dashboard.php">← Go back to dashboard</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM messages ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr><td colspan="4" style="text-align:center; color: var(--neutral); font-style: italic;">There are no messages yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
