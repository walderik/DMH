<?php

class Bookkeeping_Account extends BaseModel{
    
    
    
    public  $Id;
    public  $Name;
    public  $Description;
    public  $Active = 1;
    public  $CampaignId;
    
    const FEES_ACCOUNT = -3;
    const RETURNED_FEES_ACCOUNT = -2;
    const INVOICE_ACCOUNT = -3;
    
    const COMMON_ACCOUNTS = [Bookkeeping_Account::FEES_ACCOUNT, Bookkeeping_Account::RETURNED_FEES_ACCOUNT, Bookkeeping_Account::INVOICE_ACCOUNT];
        
    
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Active'])) {
            if ($arr['Active'] == "on" || $arr['Active'] == 1) {
                $this->Active = 1;
            }
            else {
                $this->Active = 0;
            }
        }
        else {
            $this->Active = 0;
        }
        
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        
        
    }
  
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $obj = new self();
        $obj->CampaignId = $current_larp->CampaignId;
        return $obj;
    }
    
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_bookkeeping_account SET Name=?, Description=?, Active=?, CampaignId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->Active, $this->CampaignId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    
    # Create a new in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_bookkeeping_account (Name, Description, Active, CampaignId) VALUES (?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->Active, $this->CampaignId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public function inUse() {
        $sql = "SELECT Count(*) AS Num FROM regsys_bookkeeping WHERE BookkeepingAccountId=?";
        $count = static::countQuery($sql, array($this->Id));
        if ($count > 0) return true;
        return false;
    }
    
    public static function getAll(LARP $larp) {
        $sql = "SELECT * FROM regsys_bookkeeping_account WHERE CampaignId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function allActive(LARP $larp) {
        $sql = "SELECT * FROM regsys_bookkeeping_account WHERE active = 1 AND CampaignId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }

    public static function allActiveIncludeCommon(LARP $larp) {
         return array_merge(static::getCommon(), static::allActive($larp));
    }

    public static function getCommon() {
        $sql = "SELECT * FROM regsys_bookkeeping_account WHERE active = 1 AND CampaignId IS NULL ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, NULL);
    }
    
}