<?php

class Resource extends BaseModel{
    
    public $Id;
    public $Name;
    public $UnitSingular;
    public $UnitPlural;
    public $Price = 0;
    public $IsRare = 0;
    public $ImageId;
    public $CampaignId;
    
    public static $orderListBy = 'IsRare, Name';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        $role->setValuesByArray($post);
        return $role;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['UnitSingular'])) $this->UnitSingular = $arr['UnitSingular'];
        if (isset($arr['UnitPlural'])) $this->UnitPlural = $arr['UnitPlural'];
        if (isset($arr['Price'])) $this->Price = $arr['Price'];
        if (isset($arr['IsRare'])) $this->IsRare = $arr['IsRare']; 
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        
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
        if ($this->IsRare == 1) {
            $this->Price = 0;
        }
        $stmt = $this->connect()->prepare("UPDATE regsys_resource SET Name=?, 
            UnitSingular=?, UnitPlural=?, Price=?, IsRare=?, ImageId=?, 
            CampaignId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->UnitSingular, $this->UnitPlural, 
            $this->Price, $this->IsRare, $this->ImageId, $this->CampaignId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        if ($this->isRare()) {
            $this->Price = 0;
        }
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_resource (Name, UnitSingular, 
            UnitPlural, Price, IsRare, ImageId, CampaignId) VALUES (?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->UnitSingular, $this->UnitPlural, 
            $this->Price, $this->IsRare, $this->ImageId, $this->CampaignId))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    
    public function isRare() {
        if ($this->IsRare == 1) return true;
        return false;
    }
    
    public function hasImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
    
    public function getImage() {
        if (empty($this->ImageId)) return null;
        return Image::loadById($this->ImageId);
    }
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_resource WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
   
    
    public static function allNormalByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_resource WHERE CampaignId = ? AND IsRare=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function allRareByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_resource WHERE CampaignId = ? AND IsRare=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    public static function TitleDeedProcucesNormally(Titledeed $titledeed) {
        if (is_null($titledeed)) return Array();
        $sql = "SELECT * FROM regsys_resource WHERE Id IN (SELECT ResourceId FROM regsys_resource_titledeed_normally_produces WHERE TitleDeedId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }
    
    public static function TitleDeedRequiresNormally(Titledeed $titledeed) {
        if (is_null($titledeed)) return Array();
        $sql = "SELECT * FROM regsys_resource WHERE Id IN (SELECT ResourceId FROM regsys_resource_titledeed_normally_requires WHERE TitleDeedId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }
    
    public function countNumberOfCards(LARP $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT Sum(regsys_resource_titledeed.Quantity) as Num FROM regsys_resource_titledeed, regsys_titledeed WHERE ".
            "regsys_resource_titledeed.ResourceId = ? AND ".
            "regsys_resource_titledeed.TitleDeedId = regsys_titledeed.Id AND ".
            "regsys_titledeed.IsInUse = 1 AND ".
            "regsys_titledeed.CampaignId = ? AND ".
            "regsys_resource_titledeed.Quantity > 0 ";
        $count = static::countQuery($sql, array($this->Id, $larp->CampaignId));
        if (isset($count)) return $count;
        return 0;
    }
    
    public function countBalance(LARP $larp) {
        if (is_null($larp)) return 0;
        
        if ($this->isRare()) return $this->countBalanceRare($larp);
        return $this->countBalanceNormal($larp);
    }

    
    private function countBalanceNormal(LARP $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT Sum(regsys_resource_titledeed.Quantity) as Num FROM regsys_resource_titledeed, regsys_titledeed WHERE ".
            "regsys_resource_titledeed.ResourceId = ? AND ".
            "regsys_resource_titledeed.TitleDeedId = regsys_titledeed.Id AND ".
            "regsys_titledeed.IsInUse = 1 AND ".
            "regsys_titledeed.CampaignId = ?";
        $count = static::countQuery($sql, array($this->Id, $larp->CampaignId));
        if (isset($count)) return $count;
        return 0;
    }
    
    
    private function countBalanceRare(LARP $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT Sum(regsys_resource_titledeed.QuantityForUpgrade) as Num FROM regsys_resource_titledeed, regsys_titledeed WHERE ".
            "regsys_resource_titledeed.ResourceId = ? AND ".
            "regsys_resource_titledeed.TitleDeedId = regsys_titledeed.Id AND ".
            "regsys_titledeed.IsInUse = 1 AND ".
            "regsys_titledeed.CampaignId = ? AND ".
            "regsys_resource_titledeed.QuantityForUpgrade > 0 ";
        $countForUpgrade = static::countQuery($sql, array($this->Id, $larp->CampaignId));
        if (!isset($countForUpgrade)) $countForUpgrade = 0;
        $countProduces = $this->countNumberOfCards($larp);
        return $countProduces - $countForUpgrade;
    }
    
}