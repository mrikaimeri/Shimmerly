<?php require 'db.php'; ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shimmerly</title>
    <link href="assets/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "header.php"; ?>

<main>
    <!-- Home -->
    <section id="video">
        <video width="100%" height="auto" autoplay loop muted playsinline>
            <source src="assets/photos/shimmerlyDesign.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </section>

    <!-- Shimmerly -->
    <div class="shimmerly" id="about-section">
        <div class="shimm-imgs">
        <video autoplay loop muted playsinline  id="borderimg">
            <source src="assets/photos/WhatsApp Video 2025-04-06 at 11.31.11 AM.mp4" type="video/mp4" >
            Your browser does not support the video tag.
        </video> 
        </div>
        <div class="shimm-2">
            <img src="assets/photos/abttttimg.jpg" alt="Shimmer" id="borderimg">
        </div>
        <div class="shimmerly-text">
            <h2 id="shimmerlyh2">About Shimmerly</h2>
            <p id="shimmerlyp">
                At Shimmerly Bijoux, we believe that every detail has the power to shine. <br>
                Our collections are designed to bring a touch of elegance and brilliance to every moment of your life. <br><br>
                Inspired by delicacy and refined craftsmanship, each piece in our collection is carefully crafted to reflect<br> 
                your unique style, grace, and personality. From everyday accessories to exclusive statement pieces, <br>
                Shimmerly Bijoux is your go-to destination for timeless beauty. <br><br>
                Let your sparkle shine with us! 
            </p>
        </div>
    </div>

    

<!--contact-->
 
<section class="contact-form container py-5">
    <h2 class="mb-4 text-center">Get in touch with us</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $message = htmlspecialchars(trim($_POST['message']));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '<div class="alert alert-danger">This email address is not valid.</div>';
        } else {
            $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $message);

            if ($stmt->execute()) {
                echo '<div class="alert alert-success">Sent successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">An error occurred while sending the message.</div>';
            }

            $stmt->close();
        }
    }
    ?>

    <form method="POST" id="contact-form" class="mt-4">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name:</label>
            <input type="text" id="name" name="name" class="form-control contact-input" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address:</label>
            <input type="email" id="email" name="email" class="form-control contact-input" required>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Your Message:</label>
            <textarea id="message" name="message" rows="4" class="form-control contact-input" required></textarea>
        </div>

        <button type="submit" name="submit" class="btn contact-btn">Send Message</button>
    </form>
</section>


<?php include "footer.php";
?>

</body>
</html>
