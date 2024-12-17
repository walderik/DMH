<?php

class Telegram extends BaseModel{
    
    public  $Id;
    public  $Deliverytime;
    public  $Sender;
    public  $SenderCity;
    public  $Reciever;
    public  $RecieverCity;
    public  $Message;
    public  $OrganizerNotes;
    public  $Approved = 0;
    public  $LARPid;
    public  $PersonId;
    
//     public static $tableName = 'telegrams';
    public static $orderListBy = 'Deliverytime';
    
    public static function newFromArray($post) {
        $telegram = static::newWithDefault();
        $telegram->setValuesByArray($post);
        return $telegram;
    }
     
    public function setValuesByArray($arr) {
        if (isset($arr['Deliverytime'])) $this->Deliverytime = $arr['Deliverytime'];
        if (isset($arr['Sender'])) $this->Sender = $arr['Sender'];
        if (isset($arr['SenderCity'])) $this->SenderCity = $arr['SenderCity'];
        if (isset($arr['Reciever'])) $this->Reciever = $arr['Reciever'];
        if (isset($arr['RecieverCity'])) $this->RecieverCity = $arr['RecieverCity'];
        if (isset($arr['Message'])) $this->Message = $arr['Message'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['Approved'])) $this->Approved = $arr['Approved'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['LARPid'])) $this->LARPid = $arr['LARPid'];
        
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_person;
        
        $telegram = new self();
        $telegram->Deliverytime = $current_larp->StartTimeLARPTime;
        $telegram->LARPid = $current_larp->Id;
        $telegram->PersonId = $current_person->Id;
        return $telegram;
    }
    
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_telegram WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allBySelectedPersonIdAndLARP($person_id, Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_telegram WHERE LARPid = ? and PersonId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $person_id));
    }
    
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_telegram WHERE LARPid = ? AND Approved=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allApprovedBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_telegram WHERE LARPid = ? AND Approved=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    
    
    # Update an existing telegram in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_telegram SET Deliverytime=?, Sender=?, SenderCity=?, Reciever=?, RecieverCity=?, Message=?, OrganizerNotes=?, Approved=?, PersonId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Deliverytime, $this->Sender, $this->SenderCity, $this->Reciever, $this->RecieverCity, $this->Message, $this->OrganizerNotes, $this->Approved, $this->PersonId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }
    
    # Create a new telegram in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_telegram (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes, Approved, PersonId, LARPid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Deliverytime, $this->Sender, $this->SenderCity, $this->Reciever, $this->RecieverCity, $this->Message, $this->OrganizerNotes, $this->Approved, $this->PersonId, $this->LARPid))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
       }
       $this->Id = $connection->lastInsertId();
       $stmt = null;
    }
    
    public function getPerson() {
        return Person::loadById($this->PersonId);
    }
    
    public static function getAllCheckinTelegramsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_telegram WHERE Id IN (".
            "SELECT TelegramId FROM regsys_intrigueactor_checkintelegram, regsys_intrigue_telegram WHERE ".
            "regsys_intrigue_telegram.Id = regsys_intrigueactor_checkintelegram.IntrigueTelegramId AND ".
            "regsys_intrigueactor_checkintelegram.IntrigueActorId = ?)  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    
    public static function getCheckinTelegramsForPerson(Person $person, LARP $larp) {
        $sql = "SELECT * FROM regsys_telegram WHERE Id IN (".
            "SELECT TelegramId FROM regsys_intrigueactor_checkintelegram, regsys_intrigue_telegram, regsys_intrigueactor, regsys_intrigue, regsys_role WHERE ".
            "regsys_intrigue_telegram.Id = regsys_intrigueactor_checkintelegram.IntrigueTelegramId AND ".
            "regsys_intrigueactor_checkintelegram.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?".
            ")  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->Id));
        
    }
    
    
    public static function getCheckinTelegramsForGroup(Group $group, LARP $larp) {
        $sql = "SELECT * FROM regsys_telegram WHERE Id IN (".
            "SELECT TelegramId FROM regsys_intrigueactor_checkintelegram, regsys_intrigue_telegram, regsys_intrigueactor, regsys_intrigue WHERE ".
            "regsys_intrigue_telegram.Id = regsys_intrigueactor_checkintelegram.IntrigueTelegramId AND ".
            "regsys_intrigueactor_checkintelegram.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.GroupId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?".
            ")  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
        
    }
    
}
