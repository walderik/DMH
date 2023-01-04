<?php


class LoginController extends Login{
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

        
        $this->getUser($this->email, $this->password);
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
    


    
}