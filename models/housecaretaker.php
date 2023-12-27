<?php

class Housecaretaker extends BaseModel{
    
    public $PersonId;
    public $HouseId;
    public $IsApproved = 0;
    public $ContractSignedDate;
    public $IsMember = 0;
    public $MembershipCheckedAt;
    
    public static $orderListBy = 'HouseId';
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    
    public function setValuesByArray($arr) {
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['HouseId'])) $this->HouseId = $arr['HouseId'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
        if (isset($arr['ContractSignedDate'])) $this->ContractSignedDate = $arr['ContractSignedDate'];
        if (isset($arr['IsMember'])) $this->IsMember = $arr['IsMember'];
        if (isset($arr['MembershipCheckedAt'])) $this->MembershipCheckedAt = $arr['MembershipCheckedAt'];
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    
    public static function loadByIds($houseId, $personId)
    {
        if (!isset($houseId) or !isset($personId)) return null;
        
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_housecaretaker WHERE HouseId = ? AND PersonId = ?";
        return static::getOneObjectQuery($sql, array($houseId, $personId));
    }
    
        
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_housecaretaker SET IsApproved=?, 
            ContractSignedDate=?, IsMember=?, MembershipCheckedAt=?
            WHERE PersonId=? AND HouseId=?;");
        
        if (!$stmt->execute(array($this->IsApproved, $this->ContractSignedDate, $this->IsMember, $this->MembershipCheckedAt, $this->PersonId, 
            $this->HouseId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;    
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_housecaretaker (PersonId, HouseId, IsApproved, ContractSignedDate, IsMember, MembershipCheckedAt) 
                VALUES (?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->PersonId, $this->HouseId, $this->IsApproved, $this->ContractSignedDate, $this->IsMember, $this->MembershipCheckedAt))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    

    
    public static function delete_housecaretaker($houseId, $personId) {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_housecaretaker WHERE HouseId = ? AND PersonId = ?");
        
        if (!$stmt->execute(array($houseId, $personId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    # Icke statisk version av delete
    public function destroy() {
        static::delete_housecaretaker($this->HouseId, $this->PersonId);
    }
    
    public function getPerson() {
        return Person::loadById($this->PersonId);
    }
    
    public function getHouse() {
        return House::loadById($this->HouseId);
    }
    
    # Kolla om husförvaltaren är medlem
    public function isMember() {
        //Vi har fått svar på att man har betalat medlemsavgift för året. Behöver inte kolla fler gånger.
        if ($this->IsMember == 1) return true;
        
        //Kolla inte oftare än en gång per kvart
        if (isset($this->MembershipCheckedAt) && (time()-strtotime($this->MembershipCheckedAt) < 15*60)) return false; # < 15*60
        
        $year = date("Y");
        
        
        $val = check_membership($this->getPerson()->SocialSecurityNumber, $year);
        
        
        if ($val == 1) {
            $this->IsMember=1;
        } else {
            $this->IsMember = 0;
        }
        $now = new Datetime();
        $this->MembershipCheckedAt = date_format($now,"Y-m-d H:i:s");
        $this->update();
        
        if ($this->IsMember == 1) return true;
        return false;
        
    }
    
}