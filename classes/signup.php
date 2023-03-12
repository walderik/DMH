<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

class Signup extends Dbh {

    protected function createUser($name, $email, $password) {
        $user = User::newWithDefault();
        $user->Name = $name;
        $user->Email = $email;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $activationCode = uniqid();
        
        $user->Password = $hashedPassword;
        $user->ActivationCode = $activationCode;
        
        $user->create();
        
        $url = $this->activation_url($email, $activationCode);
        
        $text  = "Du har registrerat ett konto för anmälningssystemet.<br>\n";
        $text .= "Nu måste du aktivera ditt konto.<br><br>\n";
        $text .= "<a href='$url'>Allt du behöver göra är att klicka på den här länken.</a><br>\n";
        
        BerghemMailer::send($email, $name, $text, "Aktiveringsbrev");
        
    }
    
    
    protected function checkUserExists($email) {
        $user = User::loadByEmail($email);
        
        if (is_null($user)) return false;
        
        return true;
    }
    
    protected function activation_url($email, $activationCode){
        return sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            "/regsys/includes/activate.php?email=$email&code=$activationCode"
            );
    }
    
    
}
    