<?php

class Letter extends BaseModel{
    
    public  $Id;

    public  $WhenWhere;
    public  $Greeting;
    public  $Message;
    public  $EndingPhrase;
    public  $Signature;
    public  $OrganizerNotes;
    public  $Approved = 1;
    public  $LARPid;
    public  $Font;
    public  $PersonId; 
    public  $Recipient;

    

    public static $orderListBy = 'WhenWhere';
    
    public static function newFromArray($post) {
        $letter = static::newWithDefault();
        $letter->setValuesByArray($post);
        return $letter;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['WhenWhere'])) $this->WhenWhere = $arr['WhenWhere'];
        if (isset($arr['Greeting'])) $this->Greeting = $arr['Greeting'];
        if (isset($arr['Message'])) $this->Message = $arr['Message'];
        if (isset($arr['EndingPhrase'])) $this->EndingPhrase = $arr['EndingPhrase'];
        if (isset($arr['Signature'])) $this->Signature = $arr['Signature'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['Approved'])) $this->Approved = $arr['Approved'];
        if (isset($arr['Font'])) $this->Font = $arr['Font'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['LARPid'])) $this->LARPid = $arr['LARPid'];
        if (isset($arr['Recipient'])) $this->Recipient = $arr['Recipient'];
        
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_person;
        
        $letter = new self();
        $letter->Deliverytime = $current_larp->StartTimeLARPTime;
        $letter->LARPid = $current_larp->Id;
        $letter->PersonId = $current_person->Id;
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
    
    public static function allBySelectedPersonIdAndLARP($person_id, Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_letter WHERE LARPid = ? and PersonId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $person_id));
    }
    
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_letter WHERE LARPid = ? AND Approved=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_letter SET WhenWhere=?, Greeting=?, EndingPhrase=?, Signature=?, Message=?, Font=?, OrganizerNotes=?, Approved=?, PersonId=?, Recipient=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->WhenWhere, $this->Greeting, $this->EndingPhrase, $this->Signature, $this->Message, $this->Font, $this->OrganizerNotes, $this->Approved, $this->PersonId, $this->Recipient, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_letter (WhenWhere, Greeting, EndingPhrase, Signature, Message, Font, OrganizerNotes, Approved, PersonId, LARPid, Recipient) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->WhenWhere, $this->Greeting, $this->EndingPhrase, $this->Signature, $this->Message, $this->Font, $this->OrganizerNotes, $this->Approved, $this->PersonId, $this->LARPid, $this->Recipient))) {
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
    
    public static function getAllCheckinLettersForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_letter WHERE Id IN (".
            "SELECT LetterId FROM regsys_intrigueactor_checkinletter, regsys_intrigue_letter WHERE ".
            "regsys_intrigue_letter.Id = regsys_intrigueactor_checkinletter.IntrigueLetterId AND ".
            "regsys_intrigueactor_checkinletter.IntrigueActorId = ?)  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
 
    
    public static function getCheckinLettersForPerson(Person $person, LARP $larp) {
        $sql = "SELECT * FROM regsys_letter WHERE Id IN (".
            "SELECT LetterId FROM regsys_intrigueactor_checkinletter, regsys_intrigue_letter, regsys_intrigueactor, regsys_intrigue, regsys_role WHERE ".
            "regsys_intrigue_letter.Id = regsys_intrigueactor_checkinletter.IntrigueLetterId AND ".
            "regsys_intrigueactor_checkinletter.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?".
            ")  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->Id));
        
    }
    
    
    public static function getCheckinLettersForGroup(Group $group, LARP $larp) {
        $sql = "SELECT * FROM regsys_letter WHERE Id IN (".
            "SELECT LetterId FROM regsys_intrigueactor_checkinletter, regsys_intrigue_letter, regsys_intrigueactor, regsys_intrigue WHERE ".
            "regsys_intrigue_letter.Id = regsys_intrigueactor_checkinletter.IntrigueLetterId AND ".
            "regsys_intrigueactor_checkinletter.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.GroupId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?".
            ")  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
        
    }
    
    
    
}
