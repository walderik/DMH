<?php

class NPCGroup extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $Time;
    public $LarpId;
    
    
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        $role->setValuesByArray($post);
        return $role;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['Time'])) $this->Time = $arr['Time'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->LarpId = $current_larp->Id;
        return $newOne;
    }
    
    
    # Update an existing object in db
    public function update() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE `".$tbl_prefix."role` SET Name=?, Description=?,
                                                              Time=?, LarpId=?, WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->Description,
            $this->Time, $this->LarpId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `".$tbl_prefix."role` (Name, Description,
                                                            Time, LarpId) VALUES (?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->Time,
            $this->LarpId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    
    public static function getAllForLARP(LARP $larp) {
        global $tbl_prefix;
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM ".$tbl_prefix."npcgroup WHERE ".
            "LarpId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }
    
    
    
    
    
}