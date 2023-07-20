<?php

class Timeline extends BaseModel{
    
    
    
    public  $Id;
    public  $Description;
    public  $When;
    public  $LarpId;
    public  $IntrigueId;
    
    public static $orderListBy = '`When`';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['When'])) $this->When = $arr['When'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        
        if (isset($this->IntrigueId) && $this->IntrigueId=='null') $this->IntrigueId = null;
        
    }
  
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $obj = new self();
        $obj->LarpId = $current_larp->Id;
        $obj->When = $current_larp->StartDate;
        return $obj;
    }
    
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_timeline SET Description=?, `When`=?, IntrigueId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Description, $this->When, $this->IntrigueId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    
    # Create a new in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_timeline (Description, `When`, IntrigueId, LarpId) VALUES (?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Description, $this->When, $this->IntrigueId, $this->LarpId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function getAll(LARP $larp) {
        $sql = "SELECT * FROM regsys_timeline WHERE LarpId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_timeline WHERE IntrigueId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    
    
    public function getIntrigue() {
        return Intrigue::loadById($this->IntrigueId);
    }
    
    
}