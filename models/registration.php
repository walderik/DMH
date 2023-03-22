<?php


class Registration extends BaseModel{

    
    public $Id;
    public $LARPId;
    public $PersonId;
    public $Approved; //Date
    public $RegisteredAt;
    public $PaymentReference;
    public $AmountToPay = 0;
    public $AmountPayed = 0;
    public $Payed; //Datum
    public $IsMember; 
    public $MembershipCheckedAt;
    public $NotComing = 0;
    public $ToBeRefunded = 0;
    public $RefundDate; 
    public $IsOfficial = 0;
    public $NPCDesire;
    public $HousingRequestId;
    public $Guardian;
    public $NotComingReason;
    
    public static $orderListBy = 'RegisteredAt';
    
    
    public static function newFromArray($post){
        $registration = static::newWithDefault();
        $registration->setValuesByArray($post);
        return $registration;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['Approved'])) $this->Approved = $arr['Approved'];
        if (isset($arr['RegisteredAt'])) $this->RegisteredAt = $arr['RegisteredAt'];
        if (isset($arr['PaymentReference'])) $this->PaymentReference = $arr['PaymentReference'];
        if (isset($arr['AmountToPay'])) $this->AmountToPay = $arr['AmountToPay'];
        if (isset($arr['AmountPayed'])) $this->AmountPayed = $arr['AmountPayed'];
        if (isset($arr['Payed'])) $this->Payed = $arr['Payed'];
        if (isset($arr['IsMember'])) $this->IsMember = $arr['IsMember'];
        if (isset($arr['MembershipCheckedAt'])) $this->MembershipCheckedAt = $arr['MembershipCheckedAt'];
        if (isset($arr['NotComing'])) $this->NotComing = $arr['NotComing'];
        if (isset($arr['ToBeRefunded'])) $this->ToBeRefunded = $arr['ToBeRefunded'];
        if (isset($arr['RefundDate'])) $this->RefundDate = $arr['RefundDate'];
        if (isset($arr['IsOfficial'])) $this->IsOfficial = $arr['IsOfficial'];
        if (isset($arr['NPCDesire'])) $this->NPCDesire = $arr['NPCDesire'];
        if (isset($arr['HousingRequestId'])) $this->HousingRequestId = $arr['HousingRequestId'];
        if (isset($arr['Guardian'])) $this->Guardian = $arr['Guardian'];
        if (isset($arr['NotComingReason'])) $this->NotComingReason = $arr['NotComingReason'];
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }

    public static function allBySelectedLARP(?LARP $selectedLarp = NULL) {
        global $tbl_prefix, $current_larp;
        
        if (!isset($selectedLarp) || is_null($selectedLarp)) $selectedLarp = $current_larp;
        
        $sql = "SELECT * FROM `".$tbl_prefix."registration` WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($current_larp->Id))) {
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
    
    
    public static function countAllNonOfficials(?LARP $selectedLarp = NULL) {
        global $tbl_prefix, $current_larp;
        
        if (!isset($selectedLarp) || is_null($selectedLarp)) $selectedLarp = $current_larp;
        
        $sql = "SELECT COUNT(*) AS Num FROM `".$tbl_prefix."registration` WHERE LARPid = ? AND IsOfficial=0;";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($current_larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        return $rows[0]['Num'];
    }
    
    # Update an existing registration in db
    public function update() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE ".$tbl_prefix."registration SET LARPId=?, PersonId=?, Approved=?, 
                RegisteredAt=?, PaymentReference=?, AmountToPay=?, AmountPayed=?,
                Payed=?, IsMember=?, MembershipCheckedAt=?, NotComing=?, ToBeRefunded=?,
                RefundDate=?, IsOfficial=?, NPCDesire=?, HousingRequestId=?, Guardian=?, NotComingReason=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->Approved, 
            $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay, $this->AmountPayed, 
            $this->Payed, $this->IsMember, $this->MembershipCheckedAt, $this->NotComing, $this->ToBeRefunded, 
            $this->RefundDate, $this->IsOfficial, $this->NPCDesire, $this->HousingRequestId, $this->Guardian, $this->NotComingReason, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    	
    
    
    # Create a new registration in db
    public function create() {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO ".$tbl_prefix."registration (LARPId, PersonId, Approved, RegisteredAt, 
            PaymentReference, AmountToPay, AmountPayed, Payed, IsMember,
            MembershipCheckedAt, NotComing, ToBeRefunded, RefundDate, IsOfficial, 
            NPCDesire, HousingRequestId, Guardian, NotComingReason) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->Approved, $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay,
            $this->AmountPayed, $this->Payed, $this->IsMember, $this->MembershipCheckedAt, $this->NotComing, $this->ToBeRefunded,
            $this->RefundDate, $this->IsOfficial, $this->NPCDesire, $this->HousingRequestId, $this->Guardian, $this->NotComingReason))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function isApproved() {
        if (isset($this->Approved)) {
            return true;
        }
        return false;
    }
 
    public static function loadByIds($personId, $larpId)
    {
        global $tbl_prefix;
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $stmt = static::connectStatic()->prepare("SELECT * FROM `".
            $tbl_prefix."registration` WHERE PersonId = ? AND LARPId = ?");
        
        if (!$stmt->execute(array($personId, $larpId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return null;
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0];
        $stmt = null;
        
        return static::newFromArray($row);
    }
    
    public function getPerson() {
        return Person::loadById($this->PersonId);
    }
    
    public function getGuardian() {
        return Person::loadById($this->Guardian);
    }
    
    public function getLARP() {
        return LARP::loadById($this->LARPId);
    }

    public function getOfficialTypes() {
        global $tbl_prefix;
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT * FROM ".
            $tbl_prefix."officialtype_person where RegistrationId = ? ORDER BY OfficialTypeId;");
        
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
    
    # Spara den här relationen
    public function saveAllOfficialTypes($post) {
        global $tbl_prefix;
        if (!isset($post['OfficialTypeId'])) {
            return;
        }
        foreach($post['OfficialTypeId'] as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                $tbl_prefix."officialtype_person (OfficialTypeId, RegistrationId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    
    public function deleteAllOfficialTypes() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("DELETE FROM ".
            $tbl_prefix."officialtype_person WHERE RegistrationId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getSelectedOfficialTypeIds() {
        global $tbl_prefix;
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT OfficialTypeId FROM ".
            $tbl_prefix."officialtype_person where RegistrationId = ? ORDER BY OfficialTypeId;");
        
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
            $resultArray[] = $row['OfficialTypeId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    
}