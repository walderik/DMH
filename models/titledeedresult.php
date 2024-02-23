<?php

class TitledeedResult extends BaseModel{
    
    public $Id;
    public $TitledeedId;
    public $LARPId;
    public $NeedsMet = 0;
    public $Money = 0;
    public $Notes;
    
    public static $orderListBy = 'TitledeedId';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        $role->setValuesByArray($post);
        return $role;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['TitledeedId'])) $this->TitledeedId = $arr['TitledeedId'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['NeedsMet'])) $this->NeedsMet = $arr['NeedsMet'];
        if (isset($arr['Money'])) $this->Money = $arr['Money'];
        if (isset($arr['Notes'])) $this->Notes = $arr['Notes'];
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->LARPId = $current_larp->Id;
        return $newOne;
    }
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_titledeedresult SET TitledeedId=?, NeedsMet=?, Money=?, Notes=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->TitledeedId, $this->NeedsMet, $this->Money, $this->Notes, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_titledeedresult (TitledeedId, LARPId, NeedsMet, Money, 
            Notes) VALUES (?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->TitledeedId, $this->LARPId, $this->NeedsMet, $this->Money, 
            $this->Notes))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public function getTitledeed() {
        return Titledeed::loadById($this->TitledeedId);
    }

    public static function getResultForTitledeed(Titledeed $titledeed, LARP $larp) {
        $sql = "SELECT * FROM regsys_titledeedresult WHERE TitledeedId=? AND LARPId = ?";
        return static::getOneObjectQuery($sql,array($titledeed->Id, $larp->Id));
    }

    public static function getAllResultsForTitledeed(Titledeed $titledeed) {
        $sql = "SELECT * FROM regsys_titledeedresult WHERE TitledeedId=? ORDER BY LARPId";
        return static::getSeveralObjectsqQuery($sql,array($titledeed->Id,));
    }
    
    public function getAllUpgradeResults() {
        if (is_null($this->Id)) return array();
        $stmt = $this->connect()->prepare("SELECT * FROM regsys_titledeedresult_upgrade WHERE TitledeedResultId = ?;");
        
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
            $resultArray[] = OfficialType::loadById($row['OfficialTypeId']);
        }
        $stmt = null;
        return $resultArray;
    }
    
    public function deleteAllUpgradeResults() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_titledeedresult_upgrade WHERE TitledeedResultId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function createMoneyUpgradeResult($amount, $isMet) {
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_titledeedresult_upgrade (TitledeedResultId, ResourceId, QuantityForUpgrade, NeedsMet) VALUES (?,?,?,?);");
        if (!$stmt->execute(array($this->id, null, $amount, $isMet))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    
    }

    public function createUpgradeResult($resouceId, $amount, $isMet) {
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_titledeedresult_upgrade (TitledeedResultId, ResourceId, QuantityForUpgrade, NeedsMet) VALUES (?,?,?,?);");
        if (!$stmt->execute(array($this->id, $resouceId, $amount, $isMet))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    
}