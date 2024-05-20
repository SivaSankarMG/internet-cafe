<?php

    include "connect.php";

    function validateName($name) {
        // Name can only contain letters and numbers
        return preg_match('/^[a-zA-Z0-9]+$/', $name);
    }
    
    function validateEmail($email) {
        // Email should match a standard email pattern
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
   
    function validatePassword($password) {
        // Password should be at least 8 characters long, containing at least one uppercase letter, one lowercase letter, one number, and one special character
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

   

    if(isset($_POST["signUp"]))
    {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $pass = $_POST["pass"];

        if(!validateName($name)) {
            echo "Invalid name format.<br>";
            exit();
        }
    
        if(!validateEmail($email)) {
            echo "Invalid email format.<br>";
            exit();
        }
    
        if(!validatePassword($pass)) {
            echo "Invalid password format.<br>";
            exit();
        }

        $check = "SELECT * FROM users WHERE Email = '$email' ";
        $result = $conn->query($check);

        if($result->num_rows > 0)
        {
            echo "Email already exists";
        }
        else
        {
            $insert = "INSERT INTO users (Name,Email,Password) VALUES ('$name','$email','$pass')";
            if($conn->query($insert) == TRUE )
            {
                header("Location: index.php");
                exit();
            }
            else
            {
                echo "Error : ".$conn->error;
            }
        }
    }

    if(isset($_POST["signIn"]))
    {
        session_start();
        
        $email = $_POST["email"];
        $pass = $_POST["pass"];

        $check = "SELECT * FROM users WHERE Email = '$email' AND Password='$pass' ";
        $result = $conn->query($check);

        if($result->num_rows > 0)
        {
        //    session_start();
            $row = $result->fetch_assoc();
            $_SESSION["Email"] = $row["Email"];
            header("Location: dashboard.php");
            exit();
        }
        else
        {
            echo "Invalid username or password";
        }   
    }

?>