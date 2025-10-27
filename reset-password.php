<?php
require_once "connect.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = trim($_POST["email"]);

    if(empty($email)){
        echo "Please enter your email address.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Please enter a valid email address.";
    } else{
        // In a real application, you would generate a reset token, store it in the database,
        // and send an email to the user with a link containing the token.
        // For this example, we'll just simulate the success.
        echo "If an account with that email exists, a password reset link has been sent.";
    }
}

$conn->close();
?>