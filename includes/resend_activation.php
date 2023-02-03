<?php

// Now we check if the data was submitted, isset() function will check if the data exists.
if (isset($_POST['submit']) && isset($_POST['email'])) {
    
    //Grabbing the data
    $email = $_POST['email'];
    $user = User::loadByEmail($email);
    if (is_null($user)) {
        header("location: ../index.php?error=noSubmit");
    }
    if ($user->isActivated()) {
        header("location: ../index.php?error=noSubmit");
    }
   
    $mail = $user->Email;
    $code = $user->ActivationCode;
    
    $url = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            "/includes/activate.php?email=$mail&code=$code"
            );
    
    $text  = "Du har begärt en ny aktiveringslänk för att kunna aktivera kontot.<br>\n";
    $text .= "<a href='$url'>Allt du behöver göra är att klicka på den här länken och sedan kan du logga in.</a><br>\n";
    
    DmhMailer::send($mail, 'Mate', $text, "Aktiveringsbrev");
    
    //Going back to front page
    header("location: ../index.php?message=user_created");  
} else {
    //Going back to front page
    header("location: ../index.php?error=noSubmit");
}
