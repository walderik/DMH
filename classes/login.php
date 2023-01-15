<?php


class Login extends Dbh {
    
    protected function getUser($email, $password) {
        $stmt = $this->connect()->prepare("SELECT Id, ActivationCode, IsAdmin , Password from user WHERE Email = ?;");
                

        
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
        $userRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!password_verify($password, $userRows[0]["Password"])) {
            $stmt = null;
            header("location: ../index.php?error=userNotFound!!");
            exit();
        }
            
        //Log in
        session_start();
        session_unset();
        
        //TODO check for activated account
        
        $actiavtionCode = $userRows[0]["ActivationCode"];
        if ($actiavtionCode !== 'activated') {
            //Kontot är inte aktiverat
            $stmt = null;
            header("location: ../index.php?error=accountNotActivated".print_r($userRows[0]));
            exit();
        }
        
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $userRows[0]["Id"];
        $isAdmin = $userRows[0]["IsAdmin"];
        
       
        
        if ($isAdmin == 1) { 
            $_SESSION['admin'] = true; 
        }
        
        
        
        
        $stmt = null;
        
    }
    
 
    
}
