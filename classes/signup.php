<?php


class Signup extends Dbh {

    protected function createUser($email, $password) {
        $stmt = $this->connect()->prepare("INSERT INTO user (Email, Password, ActivationCode) VALUES (?, ?, ?);");

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $activationCode = uniqid();
        
        //TODO fixa epostning
        $activationCode = 'activated';
        
        
        if (!$stmt->execute(array($email, $hashedPassword, $activationCode))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    
    protected function checkUserExists($email) {
        $stmt = $this->connect()->prepare("SELECT id FROM user WHERE email = ?;");
        
        
        if (!$stmt->execute(array($email))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $resultCheck;
        if ($stmt->rowCount() > 0) {
            $resultCheck = true;   
        }
        else {
            $resultCheck = false;
        }
        return $resultCheck;
    }
    
    
}
    