<?php

class Titledeed extends BaseModel{
    
    public $Id;
    public $Name;
    public $Location;
    public $Tradeable = 1;
    public $IsTradingPost = 0;
    public $Level;
    public $CampaignId;
    public $Money = 0;
    public $MoneyForUpgrade = 0;
    public $OrganizerNotes;
    public $PublicNotes;
    public $SpecialUpgradeRequirements;
    public $Type;
    public $Size;
    public $IsInUse = 1;
    public $Dividend;
    public $TitledeedPlaceId;
    
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        $role->setValuesByArray($post);
        return $role;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Location'])) $this->Location = $arr['Location'];
        if (isset($arr['Tradeable'])) $this->Tradeable = $arr['Tradeable'];
        if (isset($arr['IsTradingPost'])) $this->IsTradingPost = $arr['IsTradingPost'];
        if (isset($arr['Level'])) $this->Level = $arr['Level'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['Money'])) $this->Money = $arr['Money'];
        if (isset($arr['MoneyForUpgrade'])) $this->MoneyForUpgrade = $arr['MoneyForUpgrade'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['PublicNotes'])) $this->PublicNotes = $arr['PublicNotes'];
        if (isset($arr['SpecialUpgradeRequirements'])) $this->SpecialUpgradeRequirements = $arr['SpecialUpgradeRequirements'];
        if (isset($arr['Type'])) $this->Type = $arr['Type'];
        if (isset($arr['Size'])) $this->Size = $arr['Size'];
        if (isset($arr['IsInUse'])) $this->IsInUse = $arr['IsInUse'];
        if (isset($arr['Dividend'])) $this->Dividend = $arr['Dividend'];
        if (isset($arr['TitledeedPlaceId'])) $this->TitledeedPlaceId = $arr['TitledeedPlaceId'];
        
        if (empty($this->Level)) $this->Level = null;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->CampaignId = $current_larp->CampaignId;
        return $newOne;
    }
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_titledeed SET Name=?, Location=?, Tradeable=?, IsTradingPost=?,
                  Level=?, CampaignId=?, Money=?, MoneyForUpgrade=?, OrganizerNotes=?, PublicNotes=?, SpecialUpgradeRequirements=?, 
                  `Type`=?, Size=?, IsInUse=?, TitledeedPlaceId=?, Dividend=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->Location, $this->Tradeable, $this->IsTradingPost, 
            $this->Level, $this->CampaignId, $this->Money, $this->MoneyForUpgrade, $this->OrganizerNotes, $this->PublicNotes, 
            $this->SpecialUpgradeRequirements, $this->Type, $this->Size, $this->IsInUse, $this->TitledeedPlaceId, $this->Dividend, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_titledeed (Name, Location, Tradeable, IsTradingPost, 
            Level, CampaignId, Money, MoneyForUpgrade, OrganizerNotes, PublicNotes, SpecialUpgradeRequirements, `Type`, Size, IsInUse, TitledeedPlaceId, Dividend) VALUES (?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Location, $this->Tradeable, $this->IsTradingPost, 
            $this->Level, $this->CampaignId, $this->Money, $this->MoneyForUpgrade, $this->OrganizerNotes, $this->PublicNotes,
            $this->SpecialUpgradeRequirements, $this->Type, $this->Size, $this->IsInUse, $this->TitledeedPlaceId, $this->Dividend))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function allByCampaign(LARP $larp, $includeNotInUse) {
        if (is_null($larp)) return Array();
        if ($includeNotInUse) $sql = "SELECT * FROM regsys_titledeed WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        else $sql = "SELECT * FROM regsys_titledeed WHERE CampaignId = ? AND IsInUse = 1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public function isInUse() {
        if ($this->IsInUse == 1) return true;
        return false;
    }
    
    public function getRoleOwners() {
        return Role::getTitledeedOwners($this);
    }
 
    public function getGroupOwners() {
        return Group::getTitledeedOwners($this);
    }
    
    public function numberOfOwners() {
        return count($this->getRoleOwners()) + count($this->getGroupOwners());
    }
    
    public function IsFirstOwnerRole(Role $role) {
        $role_owners = $this->getRoleOwners();
        if (!empty($role_owners) && $role_owners[0] == $role) return true;
        return false;
    }
    
    public function getTitledeedPlaceName() {
        if (isset($this->TitledeedPlaceId)) return TitledeedPlace::loadById($this->TitledeedPlaceId)->Name;
        return "";
    }

    
    public function IsFirstOwnerGroup(Group $group) {
        $role_owners = $this->getRoleOwners();
        if (!empty($role_owners)) return false;
        $group_owners = $this->getGroupOwners();
        if (!empty($group_owners) && $group_owners[0] == $group) return true;       
        return false;
    }
    
    public function deleteRoleOwner($roleId) {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_titledeed_role WHERE RoleId = ? AND TitledeedId = ?;");
        if (!$stmt->execute(array($roleId, $this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
 
    public function deleteGroupOwner($groupId) {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_titledeed_group WHERE GroupId = ? AND TitledeedId = ?;");
        if (!$stmt->execute(array($groupId, $this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function addRoleOwners($roleIds) {
        //Ta reda på vilka som inte redan är kopplade till verksamheten
        $exisitingRoleIds = array();
        $role_owners = $this->getRoleOwners();
        foreach ($role_owners as $role_owner) {
            $exisitingRoleIds[] = $role_owner->Id;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingRoleIds);
        foreach ($newRoleIds as $roleId) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_titledeed_role (RoleId, TitledeedId) VALUES (?,?);");
            if (!$stmt->execute(array($roleId, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
        }
    }
    
    public function addGroupOwners($groupIds) {
        //Ta reda på vilka som inte redan är kopplade till verksamheten
        $exisitingGroupIds = array();
        $group_owners = $this->getGroupOwners();
        foreach ($group_owners as $group_owner) {
            $exisitingGroupIds[] = $group_owner->Id;
        }
        
        $newGroupIds = array_diff($groupIds,$exisitingGroupIds);
        foreach ($newGroupIds as $groupId) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_titledeed_group (GroupId, TitledeedId) VALUES (?,?);");
            if (!$stmt->execute(array($groupId, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
        }
    }
    
    public function getCampaign() {
        return Campaign::loadById($this->CampaignId);
    }
    
    public function ProducesNormally() {
        return Resource::TitleDeedProcucesNormally($this);
    }

    public function RequiresNormally() {
        return Resource::TitleDeedRequiresNormally($this);
    }
    
    
    public function ProducesString() {
        $resource_titledeeds = $this->Produces();
        $resStringArr = array();
        if ($this->Money > 0) $resStringArr[] = abs($this->Money) . " " . $this->getCampaign()->Currency;
        foreach ($resource_titledeeds as $resource_titledeed) {
            $resource = $resource_titledeed->getResource();
            if ($resource_titledeed->Quantity == 1) {
                $resStringArr[] = "1 $resource->UnitSingular";
            } else {
                $resStringArr[] = "$resource_titledeed->Quantity $resource->UnitPlural";      
            }
        }
        return implode(', ',$resStringArr);
    }
    
    public function RequiresString() {
        $resource_titledeeds = $this->Requires();
        $resStringArr = array();
        if ($this->Money < 0) $resStringArr[] = abs($this->Money) . " " . $this->getCampaign()->Currency;
        foreach ($resource_titledeeds as $resource_titledeed) {
            $resource = $resource_titledeed->getResource();
            $quantity = abs($resource_titledeed->Quantity);
            if ($quantity == 1) {
                $resStringArr[] = "1 $resource->UnitSingular";
            } else {
                $resStringArr[] = "$quantity $resource->UnitPlural";
            }
        }
        return implode(', ',$resStringArr);
    }
    
    public function RequiresForUpgradeString() {
        $resource_titledeeds = $this->RequiresForUpgrade();
        $resStringArr = array();
        if ($this->MoneyForUpgrade > 0) $resStringArr[] = $this->MoneyForUpgrade . " " . $this->getCampaign()->Currency;
        foreach ($resource_titledeeds as $resource_titledeed) {
            $resource = $resource_titledeed->getResource();
            $quantity = abs($resource_titledeed->QuantityForUpgrade);
            if ($quantity == 1) {
                $resStringArr[] = "1 $resource->UnitSingular";
            } else {
                $resStringArr[] = "$quantity $resource->UnitPlural";
            }
        }
        if (!empty($this->SpecialUpgradeRequirements)) $resStringArr[] = $this->SpecialUpgradeRequirements;
        return implode(', ',$resStringArr);
    }
    
    public function Produces() {
        return Resource_Titledeed::TitleDeedProcuces($this);
    }
    
    public function Requires() {
        return Resource_Titledeed::TitleDeedRequires($this);
    }
    
    public function RequiresForUpgrade() {
        return Resource_Titledeed::TitleDeedRequiresForUpgrade($this);
    }
    
    public function deleteAllProduces() {   
        $stmt = $this->connect()->prepare("DELETE FROM regsys_resource_titledeed_normally_produces WHERE TitleDeedId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
        
     
    public function deleteAllRequires() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_resource_titledeed_normally_requires WHERE TitleDeedId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function setProduces($resourceIds) {
        foreach($resourceIds as $resourceId) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_resource_titledeed_normally_produces (ResourceId, TitleDeedId) VALUES (?,?);");
            if (!$stmt->execute(array($resourceId, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function setRequires($resourceIds) {
        foreach($resourceIds as $resourceId) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_resource_titledeed_normally_requires (ResourceId, TitleDeedId) VALUES (?,?);");
            if (!$stmt->execute(array($resourceId, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    
    public function getSelectedProducesResourcesIds() {
        $stmt = $this->connect()->prepare("SELECT ResourceId FROM  regsys_resource_titledeed_normally_produces WHERE TitleDeedId = ? ORDER BY ResourceId;");
        
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
            $resultArray[] = $row['ResourceId'];
        }
        $stmt = null;
        
        return $resultArray;
    }

    public function getSelectedRequiresResourcesIds() {
        $stmt = $this->connect()->prepare("SELECT ResourceId FROM  regsys_resource_titledeed_normally_requires WHERE TitleDeedId = ? ORDER BY ResourceId;");
        
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
            $resultArray[] = $row['ResourceId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    public function calculateResult() {
        $resource_titledeeds = Resource_Titledeed::allForTitledeed($this);
        $res = $this->Money;
        foreach ($resource_titledeeds as $resource_titledeed) {
            $resource = $resource_titledeed->getResource();
            $res = $res + $resource->Price * $resource_titledeed->Quantity;
        }
        return $res;
    }

    
    public function calculateProduces() {
        $resource_titledeeds = Resource_Titledeed::allForTitledeed($this);
        $res = 0;
        foreach ($resource_titledeeds as $resource_titledeed) {
            if ($resource_titledeed->Quantity > 0) {
                $resource = $resource_titledeed->getResource();
                $res = $res + $resource->Price * $resource_titledeed->Quantity;
            }
        }
        return $res;
    }
  
    public function calculateNeeds() {
        $resource_titledeeds = Resource_Titledeed::allForTitledeed($this);
        $res = 0;
        foreach ($resource_titledeeds as $resource_titledeed) {
            if ($resource_titledeed->Quantity < 0) {
                $resource = $resource_titledeed->getResource();
                $res = $res + $resource->Price * $resource_titledeed->Quantity;
            }
        }
        return $res;
    }
    

    public function calculateUpgrade() {
        $resource_titledeeds = Resource_Titledeed::allForTitledeed($this);
        $res = 0;
        foreach ($resource_titledeeds as $resource_titledeed) {
            if ($resource_titledeed->QuantityForUpgrade > 0) {
                $resource = $resource_titledeed->getResource();
                $res = $res + $resource->Price * $resource_titledeed->QuantityForUpgrade;
            }
        }
        return $res;
    }
    
    
    
    public function moneySum(LARP $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT Sum(regsys_titledeed.Money) as Num FROM regsys_titledeed WHERE ".
            "regsys_titledeed.CampaignId = ? AND IsInUse=1";
        $count = static::countQuery($sql, array($larp->CampaignId));
        if (isset($count)) return $count;
        return 0;
        
    }
        
    public function moneySumUpgrade(LARP $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT Sum(regsys_titledeed.MoneyForUpgrade) as Num FROM regsys_titledeed WHERE ".
            "regsys_titledeed.CampaignId = ?  AND IsInUse = 1";
        $count = static::countQuery($sql, array($larp->CampaignId));
        if (isset($count)) return $count;
        return 0;
        
    }
    
    
    public static function getAllForRole(Role $role) {
        $sql = "SELECT * FROM regsys_titledeed WHERE Id IN (".
            "SELECT TitledeedId FROM regsys_titledeed_role WHERE RoleId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($role->Id));
    }
    

    public static function getAllForGroup(Group $group) {
        $sql = "SELECT * FROM regsys_titledeed WHERE Id IN (".
            "SELECT TitledeedId FROM regsys_titledeed_group WHERE GroupId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id));
    }
    
    public static function delete($titledeedId) {
        //Ta bort tidigare resultat
        //TODO ska göras snyggare när vi får till sparning av resultat
        $sql = "DELETE FROM regsys_titledeedresult WHERE TitledeedId=?";
        $connection = static::connectStatic();
        $stmt = $connection->prepare($sql);
        if (!$stmt->execute(array($titledeedId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
                
        parent::delete($titledeedId);
        
    }
    
    public function mayDelete() {
        //Finns det roller som ägare
        $sql = "SELECT COUNT(*) AS Num FROM regsys_titledeed_role WHERE TitledeedId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;

        //Finns det grupper som ägare
        $sql = "SELECT COUNT(*) AS Num FROM regsys_titledeed_group WHERE TitledeedId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        //Finns det normal produktion
        $sql = "SELECT COUNT(*) AS Num FROM regsys_resource_titledeed_normally_produces WHERE TitledeedId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        //Finns det normala behov
        $sql = "SELECT COUNT(*) AS Num FROM regsys_resource_titledeed_normally_requires WHERE TitledeedId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        //Finns det produktion/behov
        $sql = "SELECT COUNT(*) AS Num FROM regsys_resource_titledeed WHERE TitledeedId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        return true; 
    }
    
    public function getResult(LARP $larp) {
        return TitledeedResult::getResultForTitledeed($this, $larp);
    }
    
}