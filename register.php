<?php
session_start();
require 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validimi bazik
    if (!empty($name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = "The passwords entered do not match.";
        } 
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
            // Kontrollo nese email ekziston
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "This email is already registered!";
            } 
            else {
                // Insert ne db
                $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashed_password);

                if ($stmt->execute()) {
                    $success = "Registration was successful! You can now log in.";
                } else {
                    $error = "Something went wrong during the registration.";
                }
            }
        }
    } else {
        $error = "Please fill in all fields";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="assets/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #B29079;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 5%;
        }
        .registertxt {
            color: white;
            max-width: 300px;
            margin-right: 50px;
            margin-bottom:6%;
            font-family: "DM Serif Text";
        }
        .form-control {
             background-color: rgba(255, 255, 255, 0.1) !important;
            border: 2px solid white;
            color: white !important;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15) !important;
            box-shadow: none;
            border-color: #EFE7DA;
            color: white;
        }

        .form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px rgba(255,255,255,0.1) inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .form-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
            padding:20px;
        }

        .loginbtn {
            width: 100%;
            background-color: transparent;
            border: 2px solid white;
            color: white;
            padding: 10px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
            list-style: none;
            text-decoration:none;
        }
        #loginbtn{
            display:flex;
            align-items:center;
            justify-content:center;
        }
 
        .loginbtn:hover {
            background-color: white;
            color: black;
        }
        #msg{
            margin-top:5px;
        }
        #carouselExampleCaptions {
            max-width: 800px;
            height: 400px;
            margin-left:50px;
        }
        .carousel-inner {
            height: 100%;
        }
        .carousel-item img {
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="registertxt">
        <h3>Register and become a part of Shimmerly</h3>
        <h5>→ Fill in your details to create an account and get started.</h5>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-2"><?= $error ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success mt-2"><?= $success ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name:</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                </div>
                <div class="mb-3">
    <label for="confirm_password" class="form-label">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
</div>

                <button type="submit" name="register" class="loginbtn">Register</button>
                <div id="msg"><p>If you have an account Log In</p>    </div>
                <a href="admin\admin\login.php" class="loginbtn" id="loginbtn">Log In</a>

            </form>
        </div>
    </div>

    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/photos/aboutimg/Shimmerly.png" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="assets/photos/aboutimg/secabt-img.jpeg" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="assets/photos/aboutimg/thirdabt-img.jpeg" class="d-block w-100" alt="Slide 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
