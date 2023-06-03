<?php

class IntrigueActor extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $RoleId;
    public $GroupId;
    public $IntrigueText;
    public $OffInfo;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['IntrigueText'])) $this->IntrigueText = $arr['IntrigueText'];
        if (isset($arr['OffInfo'])) $this->OffInfo = $arr['OffInfo'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor SET IntrigueId=?, RoleId=?, GroupId=?, IntrigueText=?, OffInfo=? WHERE Id = ?");
        if (!$stmt->execute(array($this->IntrigueId, $this->RoleId, $this->GroupId, $this->IntrigueText, $this->OffInfo, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor (IntrigueId, RoleId, GroupId, IntrigueText, OffInfo) VALUES (?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->RoleId, $this->GroupId, $this->IntrigueText, $this->OffInfo))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getRole() {
        if (empty($this->RoleId)) return null;
        return Role::loadById($this->RoleId);
    }
    
    public function getGroup() {
        if (empty($this->GroupId)) return null;
        return Group::loadById($this->GroupId);
    }

    
    public function getIntrigue() {
        return Intrigue::loadById($this->IntrigueId);
    }
    
    public static function getAllGroupActorsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE GroupId IS NOT NULL AND IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }

    public static function getGroupActorForIntrigue(Intrigue $intrigue, Group $group) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE GroupId = ? AND IntrigueId = ? ORDER BY Id";
        return static::getOneObjectQuery($sql, array($group->Id, $intrigue->Id));
    }
    
    public static function getAllRoleActorsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE RoleId IS NOT NULL AND IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public static function getRoleActorForIntrigue(Intrigue $intrigue, Role $role) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE RoleId = ? AND IntrigueId = ? ORDER BY Id";
        return static::getOneObjectQuery($sql, array($role->Id, $intrigue->Id));
    }
    
    public function getAllIntrigues() {
        return Intrigue::getAllIntriguesForIntrigueActor($this);
    }
    
    
    public static function delete($id)
    {
        //TODO ta bort alla länkar
        parent::delete($id);
     }
    
    
}
