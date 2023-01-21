<?php

// Now we check if the data was submitted, isset() function will check if the data exists.
if (isset($_POST['submit'])) {
    
    //Grabbing the data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordrepeat = $_POST['passwordrepeat'];
    
    //Instantiate SignupController class
    include "../classes/dbh.php";
    include "../classes/signup.php";
    include "../classes/signup_controller.php";
    $signup = new SignupController($email, $password, $passwordrepeat);
        
    //Running error handlers and user signup
    $signup->signupUser();
    
    //Going back to front page
    header("location: ../index.php?message=user_created");  
}
else {
    //Going back to front page
    header("location: ../index.php?error=noSubmit");
}


  
     /*
    $from    = 'noreply@yourdomain.com';
    $subject = 'Aktivera konto';
    $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    // Update the activation variable below
    $activate_link = 'http://http://localhost/includes/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
    $message = '<p>Please click the following link to activate your account: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
    mail($_POST['email'], $subject, $message, $headers);
    
    echo 'Please check your email to activate your account!';
    */

