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
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE `".$tbl_prefix."resource` SET Name=?, 
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
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `".$tbl_prefix."resource` (Name, UnitSingular, 
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
    
    public static function allByCampaign() {
        global $tbl_prefix, $current_larp;
        
        $sql = "SELECT * FROM ".$tbl_prefix.strtolower(static::class)." WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($current_larp->CampaignId))) {
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
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    
    
    
}