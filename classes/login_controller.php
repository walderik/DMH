<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
echo "Root = " . $root . "<br>";
include_once $root . '/includes/all_includes.php';

class LoginController {
    private $email;
    private $password;

    
    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }
    
    public function loginUser() {
        if ($this->emptyInput()) {
            header("location: ../index.php?error=emptyInput");
            exit();
        }

        
        $this->doLogin($this->email, $this->password);
    }
    
    private function emptyInput() {
        $result;
        if (empty($this->email) or empty($this->password)) {
            $result = true;
        }
        else {
            $result = false;
        }
        return $result;
    }
    

    protected function doLogin($email, $password) {
        $user = User::loadByEmail($email);
        if (!isset($user)) {
            header("location: ../index.php?error=userNotFound");
            exit();
        }
        
        //Check password
        if (!password_verify($password, $user->Password)) {
            header("location: ../index.php?error=userNotFound");
            exit();
        }
        
        
        if (!$user->isActivated()) { #          ActivationCode !== 'activated') {
            //Kontot är inte aktiverat
            header("location: ../index.php?error=accountNotActivated");
            exit();
        }
        
        //Log in
        session_start();
        session_unset();
        
        
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user->Id;
        $isAdmin = $user->IsAdmin;
        
        if ($isAdmin == 1) {
            $_SESSION['admin'] = true;
        }
    }
    
    
    
}