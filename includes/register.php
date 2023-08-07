<?php
// Now we check if the data was submitted, isset() function will check if the data exists.
if (isset($_POST['submit'])) {
    
    //Grabbing the data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordrepeat = $_POST['passwordrepeat'];
    
    //Instantiate SignupController class
    include "../classes/signup_controller.php";
    $signup = new SignupController($name, $email, $password, $passwordrepeat);
    
//     //Running error handlers and user signup
    $signup->signupUser();

    if (!handleEmailQueue()) {
        //     echo "<h1>Failing sending Email</h1>"; # Vad gör vi nu? Skicka felnotering till admin?
    }
    
    
    //Going back to front page
    header("location: ../index.php?message=user_created"); 
    exit;
}
// else {
    //Going back to front page
    header("location: ../index.php?error=noSubmit");
// }