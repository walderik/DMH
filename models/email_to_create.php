<?php

use PHPMailer\PHPMailer\PHPMailer;

class Email_To_Create extends BaseModel{
    
    const INTRIGUE = 0;
    
    
    public  $Id;
    public  $LarpId;
    public  $EmailType;
    public  $SenderPersonId;
    public  $CreatedAt;
    public  $Subject;
    public  $Greeting;
    public  $Text;
    public  $SenderText;
    public  $RegistrationId;


    public static $orderListBy = 'CreatedAt';
    
    public static function newFromArray($post) {
        $email = static::newWithDefault();
        $email->setValuesByArray($post);
        return $email;
    }
    
    public function setValuesByArray($arr) {
        if (array_key_exists('LarpId', $arr)) $this->LarpId = $arr['LarpId'];
        if (array_key_exists('SenderPersonId', $arr)) $this->SenderPersonId = $arr['SenderPersonId'];
        if (isset($arr['EmailType'])) $this->EmailType = $arr['EmailType'];
        if (isset($arr['CreatedAt'])) $this->CreatedAt = $arr['CreatedAt'];
        if (isset($arr['Subject'])) $this->Subject = $arr['Subject'];
        if (isset($arr['Greeting'])) $this->Greeting = $arr['Greeting'];
        if (isset($arr['Text'])) $this->Text = $arr['Text'];
        if (isset($arr['SenderText'])) $this->SenderText = $arr['SenderText'];
        if (isset($arr['RegistrationId'])) $this->RegistrationId = $arr['RegistrationId'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];    
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $email = new self();
        return $email;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_email_to_create (LarpId, SenderPersonId, EmailType, CreatedAt, Subject, 
                Greeting, Text, SenderText, RegistrationId) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->LarpId, $this->SenderPersonId, $this->EmailType, date_format(new Datetime(),"Y-m-d H:i:s"), 
            $this->Subject, $this->Greeting, $this->Text, $this->SenderText, $this->RegistrationId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public static function getOldest50() {
        $sql = "SELECT * FROM regsys_email_to_create ORDER BY CreatedAt LIMIT 50;";
        return static::getSeveralObjectsqQuery($sql, array());
    }
    
    public function createEmail() {
        switch ($this->EmailType) {
            case static::INTRIGUE:
                $larp = LARP::loadById($this->LarpId);
                BerghemMailer::sendIntrigue($this->Greeting, $this->Subject, $this->Text, $this->SenderText, $larp, $this->SenderPersonId, $this->RegistrationId);
                break;
                
        }
        static::delete($this->Id);
    }
}
