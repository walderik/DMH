<?php

class Advertisment extends BaseModel{
    
    public  $Id;

    public  $ContactInformation;
    public  $Text;
    public  $UserId;
    public  $LarpId;
    public  $AdvertismentTypeId;

    

    public static $orderListBy = 'AdvertismentTypeId, UserId';
    
    public static function newFromArray($post) {
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['ContactInformation'])) $this->ContactInformation = $arr['ContactInformation'];
        if (isset($arr['Text'])) $this->Text = $arr['Text'];

        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['UserId'])) $this->UserId = $arr['UserId'];
        if (isset($arr['LARPid'])) $this->LARPid = $arr['LARPid'];
        if (isset($arr['AdvertismentTypeId'])) $this->AdvertismentTypeId = $arr['AdvertismentTypeId'];
        
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_user;
        
        $obj = new self();
        $obj->LARPid = $current_larp->Id;
        $obj->UserId = $current_user->Id;
        return $obj;
    }
    
    
    
    public static function allBySelectedLARPAndType(Larp $larp, AdvertismentType $advertismentType) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_advertisment WHERE LARPid = ? AND AdvertismentTypeId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $advertismentType->Id));
    }
    
    
     public static function allBySelectedUserIdAndLARP($user_id, Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_advertisment WHERE LARPid = ? and UserId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $user_id));
    }
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_advertisment SET ContactInformation=?, Text=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->ContactInformation, $this->Text, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_advertisment (ContactInformation, Text, UserId, LARPid, AdvertismentTypeId) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->ContactInformation, $this->Text, $this->UserId, $this->LARPid, $this->AdvertismentTypeId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getUser() {
        return User::loadById($this->UserId);
    }
    
}
