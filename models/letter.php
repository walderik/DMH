<?php

class Letter extends BaseModel{
    
    public  $Id;

    public  $WhenWhere;
    public  $Greeting;
    public  $Message;
    public  $Signature;
    public  $OrganizerNotes;
    public  $Approved = 1;
    public  $LARPid;
    public  $Font;
    public  $UserId; 

    

    public static $orderListBy = 'WhenWhere';
    
    public static function newFromArray($post) {
        $telegram = static::newWithDefault();
        $telegram->setValuesByArray($post);
        return $telegram;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['WhenWhere'])) $this->WhenWhere = $arr['WhenWhere'];
        if (isset($arr['Greeting'])) $this->Greeting = $arr['Greeting'];
        if (isset($arr['Message'])) $this->Message = $arr['Message'];
        if (isset($arr['Signature'])) $this->Signature = $arr['Signature'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['Approved'])) $this->Approved = $arr['Approved'];
        if (isset($arr['Font'])) $this->Font = $arr['Font'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['UserId'])) $this->UserId = $arr['UserId'];
        if (isset($arr['LARPid'])) $this->LARPid = $arr['LARPid'];
        
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_user;
        
        $letter = new self();
        $letter->Deliverytime = $current_larp->StartTimeLARPTime;
        $letter->LARPid = $current_larp->Id;
        $letter->UserId = $current_user->Id;
        return $letter;
    }
    
    
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_letter WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function allApprovedBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_letter WHERE LARPid = ? AND Approved=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allBySelectedUserIdAndLARP($user_id, Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_letter WHERE LARPid = ? and UserId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $user_id));
    }
    
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_letter WHERE LARPid = ? AND Approved=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_letter SET WhenWhere=?, Greeting=?, Signature=?, Message=?, Font=?, OrganizerNotes=?, Approved=?, UserId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->WhenWhere, $this->Greeting, $this->Signature, $this->Message, $this->Font, $this->OrganizerNotes, $this->Approved, $this->UserId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_letter (WhenWhere, Greeting, Signature, Message, Font, OrganizerNotes, Approved, UserId, LARPid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->WhenWhere, $this->Greeting, $this->Signature, $this->Reciever, $this->RecieverCity, $this->Message, $this->Font, $this->OrganizerNotes, $this->Approved, $this->UserId, $this->LARPid))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getUser() {
        return User::loadById($this->UserId);
    }
    
    
}
