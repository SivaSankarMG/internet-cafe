<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    
</head>

<style>
    /* style.css */

/* General styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background-color: #fff;
    padding: 20px;
    margin: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    border-radius: 8px;
}

/* Form header */
h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

/* Input fields styling */
.input-data {
    margin-bottom: 15px;
}

.input-data label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.input-data input[type="text"],
.input-data input[type="email"],
.input-data input[type="password"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

/* Button styling */
.btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.btn:hover {
    background-color: #45a049;
}

/* Links section styling */
.links {
    text-align: center;
    margin-top: 20px;
}

.links span {
    display: block;
    margin-bottom: 10px;
}

.links button {
    background-color: transparent;
    border: none;
    color: #4CAF50;
    cursor: pointer;
    text-decoration: underline;
    font-size: 1em;
}

.links button:hover {
    color: #45a049;
}

</style>

<body>
    <div class="container" id="signup" style="display:none;">
        <h1>Register</h1>
        <form method="post" action="register.php">
            <div class="input-data">
                <label for="name">Name</label><br>
                <input type="text" id="name" name="name"><br>

                <label for="email">Email</label><br>
                <input type="email" id="email" name="email"><br>

                <label for="pass">Password</label><br>
                <input type="password" id="pass" name="pass"><br>

                <input type="submit" value="Sign Up" name="signUp" class="btn">   
            </div>
        </form>

        <div class="links">
            <span>Already Have Account ?</span>
            <button id="signInButton">Sign In</button>
        </div>

    </div>

    <div class="container" id="signin">
        <h1>Sign in</h1>
        <form method="post" action="register.php">
            <div class="input-data">
                <label for="email">Email</label><br>
                <input type="email" id="email" name="email"><br>

                <label for="pass">Password</label><br>
                <input type="text" id="pass" name="pass"><br>

                <input type="submit" value="Sign In" name="signIn" class="btn">   
            </div>
        </form>

        <div class="links">
            <span>Don't have account yet?</span>
          <button id="signUpButton" >Sign Up</button>
        </div>

    </div>

    <script src="script.js"></script>    
</body>
</html>