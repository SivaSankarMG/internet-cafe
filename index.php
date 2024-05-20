<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internet Cafe Management</title>
    <link rel="stylesheet"href="style.css">
</head>
<body>
    <!-- Navigation bar -->
    <div class="navbar">
        
        <a href="#about">About Us</a>
        <a href="#contact">Contact</a>
        <a href="SignIn.php">Sign In</a>
        <a href="book.php">Book Now</a>
    </div>

    <!-- Cafe name with a quote -->
    <h1>Welcome to Our Internet Cafe</h1>
    <p>"The perfect place to surf the web and relax."</p>

    <!-- About Us section -->
    <section id="about">
        <h2>About Us</h2>
        <p>Insert your about us content here...</p>
    </section>

    <!-- Type of systems -->
    <h2>Type of Systems Available</h2>
    <ul>
        <li>Browsing Systems</li>
        <li>Gaming Systems</li>
        <li>Academic Purpose Systems</li>
    </ul>

    <!-- Contact info -->
    <section id="contact">
        <h2>Contact Us</h2>
        <p>Email: info@internetcafe.com<br>Phone: +1234567890</p>
    </section>

    <!-- PHP code for handling navigation to specific sections -->
    <?php
    if(isset($_GET['section'])) {
        $section = $_GET['section'];
        echo "<script>location.href='#$section';</script>";
    }
    ?>

</body>
</html>
