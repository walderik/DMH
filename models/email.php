<?php

class Email extends BaseModel{
    
    public  $Id;
    public  $LarpId;
    public  $From;
    public  $To;
    public  $CC;
    public  $Subject;
    public  $Text;
    public  $CreatedAt;
    public  $SentAt;
    public  $ErrorMessage;

    

    public static $orderListBy = 'CreatedAt';
    
    public static function newFromArray($post) {
        $email = static::newWithDefault();
        $email->setValuesByArray($post);
        return $email;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];

        if (isset($arr['From'])) $this->From = $arr['From'];
        if (isset($arr['To'])) $this->Greeting = $arr['To'];
        if (isset($arr['CC'])) $this->CC = $arr['CC'];
        if (isset($arr['Subject'])) $this->Subject = $arr['Subject'];
        if (isset($arr['Text'])) $this->Text = $arr['Text'];
//         if (isset($arr['CreatedA'])) $this->CreatedA = $arr['CreatedA'];
        if (isset($arr['SentAt'])) $this->SentAt = $arr['SentAt'];
        if (isset($arr['$ErrorMessage'])) $this->$ErrorMessage = $arr['$ErrorMessage'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];  
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $email = new self();
        
        $email->From = 'info@berghemsvanner.se';
        $myName = "Berghems vänner";
        
        if (!is_null($current_larp)) {
            $email->LarpId = $current_larp->Id;
            $campaign = $current_larp->getCampaign();
            if (!is_null($campaign)) {
                $from = $campaign->Email;
                $myName = $campaign->Name;
            }
        }
        $email->Subject = "Meddelande från $myName"; 
        return $email;
    }
    
    public static function normalCreate($To, $Subject, $Text) {
        $email = self::newWithDefault();
        $email->To = $To;
        $email->Subject = $Subject;
        $email->Text = $Text; 
        $email->create();
    }
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_letter WHERE LarpId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }

    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_email SET LarpId=?, From=?, To=?, CC=?, Subject=?, Text=?, ErrorMessage=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->LarpId, $this->From, $this->To, $this->CC, $this->Subject, $this->Text, $this->ErrorMessage, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $now = date_format(new Datetime(),"Y-m-d H:i:s");
        print_r($this);
        $stmt =  $connection->prepare("INSERT INTO regsys_email (LarpId, From, To, CC, Subject, Text, CreatedAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->LarpId, $this->From, $this->To, $this->CC, $this->Subject, $this->Text, $now))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
 
    
}
