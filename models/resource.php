<?php

class Resource extends BaseModel{
    
    public $Id;
    public $Name;
    public $UnitSingular;
    public $UnitPlural;
    public $PriceSlowRiver = 0;
    public $PriceJunkCity = 0;
    public $IsRare = 0;
    public $CampaignId;
    
    public static $orderListBy = 'Name';
    
    
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
        if (isset($arr['PriceSlowRiver'])) $this->PriceSlowRiver = $arr['PriceSlowRiver'];
        if (isset($arr['PriceJunkCity'])) $this->PriceJunkCity = $arr['PriceJunkCity'];
        if (isset($arr['IsRare'])) $this->IsRare = $arr['IsRare']; 
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
        $stmt = $this->connect()->prepare("UPDATE regsys_resource SET Name=?, 
            UnitSingular=?, UnitPlural=?, PriceSlowRiver=?, PriceJunkCity=?, IsRare=?,
            CampaignId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->UnitSingular, $this->UnitPlural, 
            $this->PriceSlowRiver, $this->PriceJunkCity, $this->IsRare, $this->CampaignId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_resource (Name, UnitSingular, 
            UnitPlural, PriceSlowRiver, PriceJunkCity, IsRare, CampaignId) VALUES (?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->UnitSingular, $this->UnitPlural, 
            $this->PriceSlowRiver, $this->PriceJunkCity, $this->IsRare, $this->CampaignId))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
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
    
    
    public static function TitleDeedProcuces(Titledeed $titledeed) {
        if (is_null($titledeed)) return Array();
        $sql = "SELECT * FROM regsys_resource WHERE Id IN (SELECT ResourceId FROM regsys_resource_titledeed_normally_produces WHERE TitleDeedId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }
    
    public static function TitleDeedRequires(Titledeed $titledeed) {
        if (is_null($titledeed)) return Array();
        $sql = "SELECT * FROM regsys_resource WHERE Id IN (SELECT ResourceId FROM regsys_resource_titledeed_normally_requires WHERE TitleDeedId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }
    
}