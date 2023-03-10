<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once $root . '/includes/berghem_mailer.php';

class Signup extends Dbh {

    protected function createUser($email, $password) {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("INSERT INTO ".$tbl_prefix."user (Email, Password, ActivationCode) VALUES (?, ?, ?);");

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $activationCode = uniqid();
                
        
        if (!$stmt->execute(array($email, $hashedPassword, $activationCode))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        $url = $this->activation_url($email, $activationCode);
        
        $text  = "Du har registrerat ett login för lajvet.<br>\n";
        $text .= "Nu måste du aktivera ditt login.<br><br>\n";
        $text .= "<a href='$url'>Allt du behöver göra är att klicka på den här länken.</a><br>\n";
        
        BerghemMailer::send($email, 'Stranger', $text, "Aktiveringsbrev");
        
    }
    
    
    protected function checkUserExists($email) {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("SELECT id FROM ".$tbl_prefix."user WHERE email = ?;");
        
        
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
    
    protected function activation_url($email, $activationCode){
        return sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            "/regsys/includes/activate.php?email=$email&code=$activationCode"
            );
    }
    
    
}
    