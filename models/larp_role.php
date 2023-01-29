<?php

class LARP_Role extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $RoleId;
    public $Approved = false;
    public $Intrigue;
    public $WhatHappened;
    public $WhatHappendToOthers;
    public $StartingMoney;
    public $EndingMoney;
    public $Result;
    public $IsMainRole = false;

    public static $orderListBy = 'RoleId';
    
    public static function newFromArray($post){
        $larp_group = static::newWithDefault();
        if (isset($post['Id']))   $larp_group->Id = $post['Id'];
        if (isset($post['LARPId'])) $larp_group->LARPId = $post['LARPId'];
        if (isset($post['RoleId'])) $larp_group->RoleId = $post['RoleId'];
        if (isset($post['Approved'])) $larp_group->Approved = $post['Approved'];
        if (isset($post['Intrigue'])) $larp_group->Intrigue = $post['Intrigue']; 
        if (isset($post['WhatHappened'])) $larp_group->WhatHappened = $post['WhatHappened']; 
        if (isset($post['WhatHappendToOthers'])) $larp_group->WhatHappendToOthers = $post['WhatHappendToOthers']; 
        if (isset($post['StartingMoney'])) $larp_group->StartingMoney = $post['StartingMoney']; 
        if (isset($post['EndingMoney'])) $larp_group->EndingMoney = $post['EndingMoney']; 
        if (isset($post['Result'])) $larp_group->Result = $post['Result'];
        if (isset($post['IsMainRole'])) $larp_group->IsMainRole = $post['IsMainRole']; 
        return $larp_group;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE `larp_role` SET LARPId=?, RoleId=?, Approved=?, Intrigue=?, WhatHappened=?,
                                                                  WhatHappendToOthers=?, StartingMoney=?, EndingMoney=?, Result=?, 
                                                                  IsMainRole=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->LARPId, $this->RoleId, $this->Approved, $this->Intrigue, $this->WhatHappened, 
                                    $this->WhatHappendToOthers, $this->StartingMoney, $this->EndingMoney, $this->Result, 
                                    $this->IsMainRole, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;    
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `larp_role` (LARPId, RoleId, Approved, Intrigue, WhatHappened,
                                                                WhatHappendToOthers, StartingMoney, EndingMoney, Result, 
                                                                IsMainRole) VALUES (?,?,?,?,?, ?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->RoleId, $this->Approved, $this->Intrigue, $this->WhatHappened,
                                    $this->WhatHappendToOthers, $this->StartingMoney, $this->EndingMoney, $this->Result,
                                    $this->IsMainRole))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    # returnera en array med alla roller som är anmälda till lajvet
    public static function getRegisteredRoles($larpId) {
        if (is_null($larpId)) return Array();
        $sql = "SELECT * FROM `larp_role` WHERE LARPId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larpId))) {
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
    
    # Hämta relationen baserat på en roll på ett visst lajv
    public static function getByLarpAndRole($larpId, $roleId){
        if (is_null($larpId) || is_null($roleId)) return null;

        $sql = "SELECT * FROM `larp_role` WHERE LARPId = ? and RoleId = ? LIMIT 1;";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larpId, $roleId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return null;
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0];
        $stmt = null;
        
        return static::newFromArray($row);
    }

    # Hämta intrigtyperna
    public function getIntrigueTypes(){
        return IntrigueType::getIntrigeTypesForLarpAndRole($this->LARPId, $this->RoleId);
    }
    
    
    public function getSelectedIntrigueTypeIds() {
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM  `intriguetype_larp_role` WHERE LARP_RoleLARPid = ? AND LARP_RoleRoleId = ? ORDER BY IntrigueTypeId;");
        
        if (!$stmt->execute(array($this->LARPId, $this->RoleId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = $row['IntrigueTypeId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
}