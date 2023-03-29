<?php

class NPC extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $Time;
    public $PersonId;
    public $NPCGroupId;
    public $LarpId;
    public $ImageId;
    
    
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
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['NPCGroupId'])) $this->NPCGroupId = $arr['NPCGroupId'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        
        if (isset($this->GroupId) && $this->GroupId=='null') $this->GroupId = null;
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        
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
                                                              Time=?, PersonId=?, NPCGroupId=?, LarpId=?, ImageId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->Description,
            $this->Time, $this->PersonId,
            $this->NPCGroupId, $this->LarpId, $this->ImageId, $this->Id))) {
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
                                                            Time, PersonId,
                                                            NPCGroupId, LarpId, ImageId) VALUES (?,?,?,?,?, ?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->Time,
            $this->PersonId, $this->NPCGroupId, $this->LarpId, $this->ImageId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
   
    public static function getAllAssigned(LARP $larp) {
        global $tbl_prefix;
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM `".$tbl_prefix."npc` WHERE `".
            "PersonId IS NOT NULL AND LarpId = ? ORDER BY ".static::$orderListBy.";";
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
     
    public static function getAllUnassigned(LARP $larp) {
        global $tbl_prefix;
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM `".$tbl_prefix."npc` WHERE `".
            "PersonId IS NULL AND LarpId = ? ORDER BY ".static::$orderListBy.";";
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
    
    # Hämta NPCer i en grupp
    public static function getNPCsInGroup(NPCGroup $npc_group, LARP $larp) {
        global $tbl_prefix;
        if (is_null($npc_group) || is_null($larp)) return Array();
        $sql = "SELECT * FROM `".$tbl_prefix."npc` WHERE `".
            "GroupId = ? AND LarpId = ? ORDER BY ".static::$orderListBy.";";
            $stmt = static::connectStatic()->prepare($sql);
            
            if (!$stmt->execute(array($npc_group->Id, $larp->Id))) {
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