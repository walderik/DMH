<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

include_once $root . '/includes/all_includes.php';

class SignupController {
    private $name;
    private $email;
    private $password;
    private $passwordrepeat;
    
    public function __construct($name, $email, $password, $passwordrepeat) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->passwordrepeat = $passwordrepeat;
    }
    
    public function signupUser() {
        if ($this->emptyInput()) {
            header("location: ../index.php?error=emptyInput");
            exit();
        }
        if ($this->oneOneName()) {
            header("location: ../index.php?error=enterBothNames");
            exit();
        }
        if ($this->invalidEmail()) {
            header("location: ../index.php?error=invalidEmail");
            exit();            
        }
        if ($this->passwordNotMatch()) {
            header("location: ../index.php?error=passwordNotMatch");
            exit();
        }
        if ($this->invalidPasswordLength()) {
            header("location: ../index.php?error=invalidPasswordLength");
            exit();
        }
        if ($this->userExists()) {
            header("location: ../index.php?error=userExists");
            exit();
        }
        
        $this->createUser($this->name, $this->email, $this->password);
    }
    
    
    private function oneOneName() {
        $result;
        if (str_word_count($this->name) < 2) {
            $result = true;
        }
        else {
            $result = false;
        }
        return $result;
    }
    
    private function emptyInput() {
        $result;
        if (empty($this->name) or empty($this->email) or empty($this->password) or empty($this->passwordrepeat)) {
            $result = true;
        }
        else {
            $result = false;
        }
        return $result;
    }
    
    private function invalidEmail() {
        $result;
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = false;
        }
        else {
            $result = true;
        }
        return $result;
    }
    
    private function passwordNotMatch() {
        $result;
        if ($this->password == $this->passwordrepeat) {
            $result = false;
        }
        else {
            $result = true;
        }
        return $result;
    }
    
    private function invalidPasswordLength() {
        $result;
        if (strlen($this->password) < 5 || strlen($this->password) > 20) {
            $result = true;
        }
        else {
            $result = false;
        }
        return $result;
    }    
 
    private function userExists() {
        $result;
        if ($this->checkUserExists($this->email)) {
            $result = true;
        }
        else {
            $result = false;
        }
        return $result;
    }
    
    private function createUser($name, $email, $password) {
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
        
        BerghemMailer::sendSimpleEmail(null, null, $email, $name, "Hej ".$name, $text, "Aktiveringsbrev", "Omnes Mundi", BerghemMailer::DaysAutomatic);
        
    }
    
    
    private function checkUserExists($email) {
        $user = User::loadByEmail($email);
        
        if (is_null($user)) return false;
        
        return true;
    }
    
    private function activation_url($email, $activationCode){
        return sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            "/includes/activate.php?email=$email&code=$activationCode"
            );
    }
    
    
}