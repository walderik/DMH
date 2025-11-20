<?php

class LARP_Role extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $RoleId;
    public $Intrigue;
    public $IntrigueIdeas;
    public $WhatHappened;
    public $WhatHappendToOthers;
    public $WhatHappensAfterLarp;
    public $StartingMoney;
    public $EndingMoney;
    public $Result;
    public $IsMainRole = 0;
    public $PersonId; //Vem som spelar rollen på det här lajvet


    public static $orderListBy = 'RoleId';
    
    public static function newFromArray($post){
        $larp_role = static::newWithDefault();
        if (isset($post['Id']))   $larp_role->Id = $post['Id'];
        if (isset($post['LARPId'])) $larp_role->LARPId = $post['LARPId'];
        if (isset($post['RoleId'])) $larp_role->RoleId = $post['RoleId'];
        if (isset($post['Intrigue'])) $larp_role->Intrigue = $post['Intrigue']; 
        if (isset($post['IntrigueIdeas'])) $larp_role->IntrigueIdeas = $post['IntrigueIdeas'];
        if (isset($post['WhatHappened'])) $larp_role->WhatHappened = $post['WhatHappened']; 
        if (isset($post['WhatHappendToOthers'])) $larp_role->WhatHappendToOthers = $post['WhatHappendToOthers']; 
        if (isset($post['WhatHappensAfterLarp'])) $larp_role->WhatHappensAfterLarp = $post['WhatHappensAfterLarp'];
        if (isset($post['StartingMoney'])) $larp_role->StartingMoney = $post['StartingMoney']; 
        if (isset($post['EndingMoney'])) $larp_role->EndingMoney = $post['EndingMoney']; 
        if (isset($post['Result'])) $larp_role->Result = $post['Result'];
        if (isset($post['IsMainRole'])) $larp_role->IsMainRole = $post['IsMainRole']; 
        if (isset($post['PersonId'])) $larp_role->PersonId = $post['PersonId'];
        return $larp_role;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function isRegistered($roleId, $larpId) {
        $sql = "SELECT * FROM regsys_larp_role WHERE RoleId = ? AND LARPId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($roleId, $larpId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            return false;
        }
        
        return true;
    }
 
    
    
    # Hämta relationen baserat på en roll på ett visst lajv
    //     public static function getByLarpAndRole($larpId, $roleId){
    public static function loadByIds($roleId, $larpId)
    {

        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_larp_role WHERE RoleId = ? AND LARPId = ?";
        return static::getOneObjectQuery($sql, array($roleId, $larpId));
    }
    
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_larp_role SET Intrigue=?, IntrigueIdeas=?, WhatHappened=?,
                                                                  WhatHappendToOthers=?, WhatHappensAfterLarp=?, StartingMoney=?, EndingMoney=?, Result=?, 
                                                                  IsMainRole=?, PersonId=? WHERE Id=?;");
        
        if (!$stmt->execute(array($this->Intrigue, $this->IntrigueIdeas, $this->WhatHappened, 
            $this->WhatHappendToOthers, $this->WhatHappensAfterLarp, $this->StartingMoney, $this->EndingMoney, $this->Result, 
            $this->IsMainRole, $this->PersonId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;    
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_larp_role (LARPId, RoleId, Intrigue, IntrigueIdeas, WhatHappened,
                                                                WhatHappendToOthers, WhatHappensAfterLarp, StartingMoney, EndingMoney, Result, 
                                                                IsMainRole, PersonId) VALUES (?,?,?,?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->RoleId, $this->Intrigue, $this->IntrigueIdeas, $this->WhatHappened,
            $this->WhatHappendToOthers, $this->WhatHappensAfterLarp, $this->StartingMoney, $this->EndingMoney, $this->Result,
                                    $this->IsMainRole, $this->PersonId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    # returnera en array med alla karaktärer som är anmälda till lajvet
    public static function getRegisteredRoles($larpId) {
        if (is_null($larpId)) return Array();
        $sql = "SELECT * FROM regsys_larp_role WHERE LARPId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larpId));
    }

    # returnera en array med alla karaktärer som är anmälda till lajvet
    public static function getRegisteredRolesForPerson($larpId, $personId) {
        if (is_null($larpId)) return Array();
        $sql = "SELECT * FROM regsys_larp_role WHERE LARPId = ? AND RoleId IN ".
            "(SELECT Id FROM regsys_role WHERE PersonId =?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larpId, $personId));
    }
    
    public function getPerson() {
        return Person::loadById($this->PersonId);
    }
    
    
    # Hämta intrigtyperna
    public function getIntrigueTypes(){
        return IntrigueType::getIntrigeTypesForRole($this->Id);
    }
    
    
    
    
    public function getSelectedIntrigueTypeIds() {
        $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM  regsys_intriguetype_role WHERE LarpRoleId = ? ORDER BY IntrigueTypeId;");
        
        if (!$stmt->execute(array($this->Id))) {
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
    
    public function saveAllIntrigueTypes($idArr) {
        if (!isset($idArr)) {
            return;
        }

        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_role (IntrigueTypeId, LarpRoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllIntrigueTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_role WHERE LarpRoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public static function delete($id)
    {
        $larp_role = static::loadById($id);
        
        $larp_role->deleteAllIntrigueTypes();
         
        parent::delete($id);
    }
    
    
   
}