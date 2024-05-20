<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    
</head>
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