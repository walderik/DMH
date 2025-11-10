<?php

class LARP_Group extends BaseModel{
    
    public $Id;
    public $GroupId;
    public $LARPId;
    public $WantIntrigue = true;
    public $Intrigue;
    public $IntrigueIdeas;
    public $ApproximateNumberOfMembers;
    public $HousingRequestId;
    public $TentType;
    public $TentSize;
    public $TentHousing;
    public $TentPlace;
    public $NeedFireplace = 0;
    public $RemainingIntrigues;
    public $UserMayEdit = 0;
    public $StartingMoney;
    public $EndingMoney;
    public $Result;
    public $WhatHappenedSinceLastLarp;
    public $WhatHappened;
    public $WhatHappendToOthers;
    public $WhatHappensAfterLarp;
    
    public static $orderListBy = 'GroupId';
    
    public static function newFromArray($post){
        $larp_group = static::newWithDefault();
        $larp_group->setValuesByArray($post);
        return $larp_group;
    }
    
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['NeedFireplace'])) $this->NeedFireplace = $arr['NeedFireplace'];
        if (isset($arr['WantIntrigue'])) $this->WantIntrigue = $arr['WantIntrigue'];
        if (isset($arr['Intrigue'])) $this->Intrigue = $arr['Intrigue'];
        if (isset($arr['IntrigueIdeas'])) $this->IntrigueIdeas = $arr['IntrigueIdeas'];
        if (isset($arr['HousingRequestId'])) $this->HousingRequestId = $arr['HousingRequestId'];
        if (isset($arr['TentType'])) $this->TentType = $arr['TentType'];
        if (isset($arr['TentSize'])) $this->TentSize = $arr['TentSize'];
        if (isset($arr['TentHousing'])) $this->TentHousing = $arr['TentHousing'];
        if (isset($arr['TentPlace'])) $this->TentPlace = $arr['TentPlace'];
        if (isset($arr['ApproximateNumberOfMembers'])) $this->ApproximateNumberOfMembers = $arr['ApproximateNumberOfMembers'];
        if (isset($arr['RemainingIntrigues'])) $this->RemainingIntrigues = $arr['RemainingIntrigues'];          
        if (isset($arr['UserMayEdit'])) $this->UserMayEdit = $arr['UserMayEdit'];
        if (isset($arr['StartingMoney'])) $this->StartingMoney = $arr['StartingMoney'];
        if (isset($arr['EndingMoney'])) $this->EndingMoney = $arr['EndingMoney'];
        if (isset($arr['Result'])) $this->Result = $arr['Result'];
        if (isset($arr['WhatHappenedSinceLastLarp'])) $this->WhatHappenedSinceLastLarp = $arr['WhatHappenedSinceLastLarp'];
        if (isset($arr['WhatHappened'])) $this->WhatHappened = $arr['WhatHappened'];
        if (isset($arr['WhatHappendToOthers'])) $this->WhatHappendToOthers = $arr['WhatHappendToOthers'];
        if (isset($arr['WhatHappensAfterLarp'])) $this->WhatHappensAfterLarp = $arr['WhatHappensAfterLarp'];
        
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function isRegistered($groupId, $larpId) {
        $sql = "SELECT count(*) as Num FROM regsys_larp_group WHERE GroupId = ? AND LARPId = ? ORDER BY ".static::$orderListBy.";";
        return static::existsQuery($sql, array($groupId, $larpId));
     }
    
    public static function loadByIds($groupId, $larpId)
    {
        if (!isset($groupId) or !isset($larpId)) return null;
        
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_larp_group WHERE GroupId = ? AND LARPId = ?";
        return static::getOneObjectQuery($sql, array($groupId, $larpId));
    }
    
        
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_larp_group SET WantIntrigue=?, Intrigue=?, IntrigueIdeas=?, HousingRequestId=?, 
            TentType=?, TentSize=?, TentHousing=?, TentPlace=?, RemainingIntrigues=? , 
            ApproximateNumberOfMembers=?, NeedFireplace=?, UserMayEdit=?, StartingMoney=?, EndingMoney=?, Result=?,
            WhatHappenedSinceLastLarp=?, WhatHappened=?, WhatHappendToOthers=?, WhatHappensAfterLarp=?
            WHERE Id=?;");
        
        if (!$stmt->execute(array($this->WantIntrigue, $this->Intrigue, $this->IntrigueIdeas, $this->HousingRequestId, 
            $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace, $this->RemainingIntrigues, 
            $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->UserMayEdit, $this->StartingMoney, $this->EndingMoney, $this->Result,
            $this->WhatHappenedSinceLastLarp, $this->WhatHappened, $this->WhatHappendToOthers, $this->WhatHappensAfterLarp,
            $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;    
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_larp_group (GroupId, LARPId, WantIntrigue, Intrigue, IntrigueIdeas,
            HousingRequestId, TentType, TentSize, TentHousing, TentPlace, RemainingIntrigues, ApproximateNumberOfMembers, NeedFireplace, UserMayEdit, 
            StartingMoney, EndingMoney, Result, WhatHappenedSinceLastLarp, WhatHappened, WhatHappendToOthers, WhatHappensAfterLarp) VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->GroupId, $this->LARPId, $this->WantIntrigue, $this->Intrigue, $this->IntrigueIdeas,
            $this->HousingRequestId, $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace, 
            $this->RemainingIntrigues, $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->UserMayEdit, 
            $this->StartingMoney, $this->EndingMoney, $this->Result, $this->WhatHappenedSinceLastLarp, $this->WhatHappened, $this->WhatHappendToOthers, $this->WhatHappensAfterLarp))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    

    
    public static function userMayEdit($groupId, $larpId) {
        $larp_group = static::loadByIds($groupId, $larpId);
        if (empty($larp_group)) return false;
        if ($larp_group->UserMayEdit == 1) return true;
        return false;
    }
    
    public function getGroup() {
        return Group::loadById($this->GroupId);
    }
    
    public function getLarp() {
        return LARP::loadById($this->LARPId);
    }
    
    public function getHousingRequest() {
        if (is_null($this->HousingRequestId)) return null;
        return HousingRequest::loadById($this->HousingRequestId);
    }
    
    public function saveAllIntrigueTypes($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_group (IntrigueTypeId, GroupId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllIntrigueTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_group WHERE GroupId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getSelectedIntrigueTypeIds() {
        $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM regsys_intriguetype_group WHERE GroupId = ? ORDER BY IntrigueTypeId;");
        
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
    
    
    
}