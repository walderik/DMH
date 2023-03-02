<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
echo "Root = " . $root . "<br>";
// Now we check if the data was submitted, isset() function will check if the data exists.
if (isset($_POST['submit'])) {
    
    //Grabbing the data
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    //Instantiate SignupController class
    include "../classes/dbh.php";
    include "../classes/login_controller.php";
    $login = new LoginController($email, $password);
    
    //Running error handlers and user signup
    $login->loginUser();
    
    //Go into system
    header("location: ../participant/index.php");
}
else {
    //Going back to front page
    header("location: ../index.php?error=noSubmit");
}

