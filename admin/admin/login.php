<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/shimmerly/db.php');
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['name'];
                $_SESSION['role'] = $row['role'];

                // Ridrejto ne faqen qe useri tentoj me vizitu
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                    exit;
                }

                // perndryshe ridrejto sipas rolit
                if ($row['role'] === 'admin') {
                    header("Location: ../dashboard.php");
                    exit;
                } else {
                    header("Location: /shimmerly/user_dashboard.php");
                    exit;
                    
                }
            } else {
                $error = "Incorrect password";
            }
        } else {
            $error = "Email not found";
        }
    } else {
        $error = "Please fill in all fields";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<style>

        :root {
            --brandyrose: #B29079;
            --peachcream: #EFE7DA;
            --neutral: #C1B6A4;
            --whitebeige: #F6F5EC;
            --chalkbeige: #E1DACA;
        }

        body {
            background-color: var(--whitebeige);
            font-family: 'DM Serif Text', serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: var(--peachcream);
            padding: 2rem 3rem;
            border-radius: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        h3 {
            color: var(--brandyrose);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .form-label {
            color: var(--neutral);
            font-weight: bold;
        }

        .form-control {
            border-radius: 0.75rem;
            border: 1px solid var(--chalkbeige);
        }

        .btn-primary {
            background-color: var(--brandyrose);
            border: none;
            width: 100%;
            border-radius: 1rem;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #9f7a66;
        }

        .alert {
            border-radius: 1rem;
        }

</style>
<body>
<div class="container mt-5">
    <h3>Login</h3>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="E-mail" required>
        </div>
        <div class="mb-3">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
