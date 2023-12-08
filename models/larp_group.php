<?php

class LARP_Group extends BaseModel{
    
    public $GroupId;
    public $LARPId;
    public $WantIntrigue = true;
    public $Intrigue;
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
    public $WhatHappened;
    public $WhatHappendToOthers;
    
    public static $orderListBy = 'GroupId';
    
    public static function newFromArray($post){
        $larp_group = static::newWithDefault();
        $larp_group->setValuesByArray($post);
        return $larp_group;
    }
    
    
    public function setValuesByArray($arr) {
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['NeedFireplace'])) $this->NeedFireplace = $arr['NeedFireplace'];
        if (isset($arr['WantIntrigue'])) $this->WantIntrigue = $arr['WantIntrigue'];
        if (isset($arr['Intrigue'])) $this->Intrigue = $arr['Intrigue'];
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
        if (isset($post['WhatHappened'])) $larp_role->WhatHappened = $post['WhatHappened'];
        if (isset($post['WhatHappendToOthers'])) $larp_role->WhatHappendToOthers = $post['WhatHappendToOthers'];
        
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
        $stmt = $this->connect()->prepare("UPDATE regsys_larp_group SET WantIntrigue=?, Intrigue=?, HousingRequestId=?, 
            TentType=?, TentSize=?, TentHousing=?, TentPlace=?, RemainingIntrigues=? , 
            ApproximateNumberOfMembers=?, NeedFireplace=?, UserMayEdit=?, StartingMoney=?, EndingMoney=?, Result=?,
            WhatHappened=?, WhatHappendToOthers=?
            WHERE GroupId=? AND LARPId=?;");
        
        if (!$stmt->execute(array($this->WantIntrigue, $this->Intrigue, $this->HousingRequestId, 
            $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace, $this->RemainingIntrigues, 
            $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->UserMayEdit, $this->StartingMoney, $this->EndingMoney, $this->Result,
            $this->WhatHappened, $this->WhatHappendToOthers,
            $this->GroupId, $this->LARPId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;    
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_larp_group (GroupId, LARPId, WantIntrigue, Intrigue, 
            HousingRequestId, TentType, TentSize, TentHousing, TentPlace, RemainingIntrigues, ApproximateNumberOfMembers, NeedFireplace, UserMayEdit, 
            StartingMoney, EndingMoney, Result, WhatHappened, WhatHappendToOthers) VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->GroupId, $this->LARPId, $this->WantIntrigue, $this->Intrigue, 
            $this->HousingRequestId, $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace, 
            $this->RemainingIntrigues, $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->UserMayEdit, 
            $this->StartingMoney, $this->EndingMoney, $this->Result, $this->WhatHappened, $this->WhatHappendToOthers))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    

    
    public static function userMayEdit($groupId, $larpId) {
        $larp_group = static::loadByIds($groupId, $larpId);
        if (empty($larp_group)) return false;
        if ($larp_group->UserMayEdit == 1) return true;
        return false;
    }
    
    public static function delete_larp_group($groupId, $larpId) {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_larp_group WHERE GroupId = ? AND LarpId = ?");
        
        if (!$stmt->execute(array($groupId, $larpId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    
}