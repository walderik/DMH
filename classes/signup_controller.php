<?php


class SignupController extends Signup{
    private $email;
    private $password;
    private $passwordrepeat;
    
    public function __construct($email, $password, $passwordrepeat) {
        $this->email = $email;
        $this->password = $password;
        $this->passwordrepeat = $passwordrepeat;
    }
    
    public function signupUser() {
        if ($this->emptyInput()) {
            header("location: ../index.php?error=emptyInput");
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
        
        $this->createUser($this->email, $this->password);
    }
    
    private function emptyInput() {
        $result;
        if (empty($this->email) or empty($this->password) or empty($this->passwordrepeat)) {
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
    
}