<?php


class Login extends Dbh {
    
    protected function getUser($email, $password) {
        $stmt = $this->connect()->prepare("SELECT Id, ActivationCode, IsAdmin , Password from users WHERE Email = ?;");
                

        
        if (!$stmt->execute(array($email))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            header("location: ../index.php?error=userNotFound");
            exit();
        }
        
        //Check password
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!password_verify($password, $user[0]["Password"])) {
            $stmt = null;
            header("location: ../index.php?error=userNotFound!!");
            exit();
        }
            
        //Log in
        session_start();
        session_unset();
        
        //TODO check for activated account
        
        $actiavtionCode = $user[0]["ActivationCode"];
        if ($actiavtionCode !== 'activated') {
            //Kontot är inte aktiverat
            $stmt = null;
            header("location: ../index.php?error=accountNotActivated");
            exit();
        }
        
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user[0]["Id"];
        $isAdmin = $user[0]["IsAdmin"];
        if ($isAdmin == 1) { 
            $_SESSION['admin'] = true; 
        }
        
        
        
        
        $stmt = null;
        
    }
    
 
    
}
