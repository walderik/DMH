<?php

class Rumour extends BaseModel{
    
    public  $Id;
    public  $Text;
    public  $Approved = 0;
    public  $LARPid;
    public  $UserId;
    public  $IntrigueId;
    
//     public static $tableName = 'telegrams';
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post) {
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
     
    public function setValuesByArray($arr) {
        if (isset($arr['Text'])) $this->Text = $arr['Text'];
        if (isset($arr['Approved'])) $this->Approved = $arr['Approved'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['UserId'])) $this->UserId = $arr['UserId'];
        if (isset($arr['LARPid'])) $this->LARPid = $arr['LARPid'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        
        if (isset($this->IntrigueId) && $this->IntrigueId=='null') $this->IntrigueId = null;
        
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_user;
        
        $telegram = new self();
        $telegram->LARPid = $current_larp->Id;
        $telegram->UserId = $current_user->Id;
        return $telegram;
    }
    
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_rumour WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allBySelectedUserIdAndLARP($user_id, Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_rumour WHERE LARPid = ? and UserId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $user_id));
    }
    
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_rumour WHERE LARPid = ? AND Approved=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allApprovedBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_rumour WHERE LARPid = ? AND Approved=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_rumour WHERE IntrigueId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    
    
    # Update an existing telegram in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_rumour SET Text=?, Approved=?, IntrigueId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Text, $this->Approved, $this->IntrigueId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }
    
    # Create a new telegram in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_rumour (Text, Approved, UserId, LARPid, IntrigueId) VALUES (?,?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Text, $this->Approved, $this->UserId, $this->LARPid,$this->IntrigueId))) {
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
    
    public function getIntrigue() {
        return Intrigue::loadById($this->IntrigueId);
    }
    
    public function getConcerns() {
        return Rumour_concerns::getAllForRumour($this);
    }
    
    public function getKnows() {
        return Rumour_knows::getAllForRumour($this);
    }
    
    
    public function addGroupConcerns($groupIds) {
        //Ta reda på vilka som inte redan är kopplade till ryktet
        $exisitingGroupIds = array();
        $concernsArr = $this->getConcerns();
        foreach ($concernsArr as $concerns) {
            if (isset($concerns->GroupId)) $exisitingGroupIds[] = $concerns->GroupId;
        }
        
        $newGroupIds = array_diff($groupIds,$exisitingGroupIds);
        foreach ($newGroupIds as $groupId) {
            $concerned = Rumour_concerns::newWithDefault();
            $concerned->GroupId = $groupId;
            $concerned->RumourId = $this->Id;
            $concerned->create();
        }
    }
    
    public function addRoleConcerns($roleIds) {
        //Ta reda på vilka som inte redan är kopplade till ryktet
        $exisitingRoleIds = array();
        $concernsArr = $this->getConcerns();
        foreach ($concernsArr as $concerns) {
            if (isset($concerns->RoleId)) $exisitingRoleIds[] = $concerns->RoleId;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingRoleIds);
        foreach ($newRoleIds as $roleId) {
            $concerned = Rumour_concerns::newWithDefault();
            $concerned->RoleId = $roleId;
            $concerned->RumourId = $this->Id;
            $concerned->create();
        }
    }
    
    public function addGroupKnows($groupIds) {
        //Ta reda på vilka som inte redan är kopplade till ryktet
        $exisitingGroupIds = array();
        $knowsArr = $this->getKnows();
        foreach ($knowsArr as $knows) {
            if (isset($knows->GroupId)) $exisitingGroupIds[] = $knows->GroupId;
        }
        
        $newGroupIds = array_diff($groupIds,$exisitingGroupIds);
        foreach ($newGroupIds as $groupId) {
            $knows = Rumour_knows::newWithDefault();
            $knows->GroupId = $groupId;
            $knows->RumourId = $this->Id;
            $knows->create();
        }
    }
    
    public function addRoleKnows($roleIds) {
        //Ta reda på vilka som inte redan är kopplade till ryktet
        $exisitingRoleIds = array();
        $knowsArr = $this->getKnows();
        foreach ($knowsArr as $knows) {
            if (isset($knows->RoleId)) $exisitingRoleIds[] = $knows->RoleId;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingRoleIds);
        foreach ($newRoleIds as $roleId) {
            $knows = Rumour_knows::newWithDefault();
            $knows->RoleId = $roleId;
            $knows->RumourId = $this->Id;
            $knows->create();
        }
    }
    
    
}