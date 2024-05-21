<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internet Cafe Management</title>
    
</head>

<style>
    /* style.css */

/* General body styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.6;
}

/* Navigation bar styling */
.navbar {
    background-color: #333;
    overflow: hidden;
}

.navbar a {
    float: left;
    display: block;
    color: #f2f2f2;
    text-align: center;
    padding: 14px 20px;
    text-decoration: none;
}

.navbar a:hover {
    background-color: #ddd;
    color: black;
}

/* Main content styling */
h1 {
    text-align: left;
    color: #333;
    margin: 20px;
}

p {
    text-align: left;
    margin: 0 20px 20px 20px;
}

h2 {
    text-align: left;
    color: #333;
    margin: 20px 0 10px 20px;
}

/* Sections styling */
section {
    margin: 20px;
}

ul {
    list-style-type: none;
    padding: 0;
    margin: 20px;
}

ul li {
    margin: 5px 0;
}

/* About Us section styling */
#about {
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Contact Us section styling */
#contact {
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

</style>
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
