<?php


class Registration extends BaseModel{

    
    public $Id;
    public $LARPId;
    public $PersonId;
    public $Approved; //Date
    public $RegisteredAt;
    public $PaymentReference;
    public $AmountToPay;
    public $AmountPayed = 0;
    public $Payed; //Datum
    public $IsMember; 
    public $MembershipCheckedAt;
    public $NotComing = 0;
    public $ToBeRefunded;
    public $RefundDate; 
    public $IsOfficial = 0;
    public $NPCDesire;
    public $HousingRequestId;
    
    public static $orderListBy = 'RegisteredAt';
    
    
    public static function newFromArray($post){
        $registration = static::newWithDefault();
        if (isset($post['Id']))   $registration->Id = $post['Id'];
        if (isset($post['LARPId'])) $registration->LARPId = $post['LARPId'];
        if (isset($post['PersonId'])) $registration->PersonId = $post['PersonId'];
        if (isset($post['Approved'])) $larp_role->Approved = $post['Approved'];
        if (isset($post['RegisteredAt'])) $registration->RegisteredAt = $post['RegisteredAt'];
        if (isset($post['PaymentReference'])) $registration->PaymentReference = $post['PaymentReference'];
        if (isset($post['AmountToPay'])) $registration->AmountToPay = $post['AmountToPay'];
        if (isset($post['AmountPayed'])) $registration->AmountPayed = $post['AmountPayed'];
        if (isset($post['Payed'])) $registration->Payed = $post['Payed'];
        if (isset($post['IsMember'])) $registration->IsMember = $post['IsMember'];
        if (isset($post['MembershipCheckedAt'])) $registration->MembershipCheckedAt = $post['MembershipCheckedAt'];
        if (isset($post['NotComing'])) $registration->NotComing = $post['NotComing'];
        if (isset($post['ToBeRefunded'])) $registration->ToBeRefunded = $post['ToBeRefunded'];
        if (isset($post['RefundDate'])) $registration->RefundDate = $post['RefundDate'];
        if (isset($post['IsOfficial'])) $registration->IsOfficial = $post['IsOfficial'];
        if (isset($post['NPCDesire'])) $registration->NPCDesire = $post['NPCDesire'];
        if (isset($post['HousingRequestId'])) $registration->HousingRequestId = $post['HousingRequestId'];
        
        return $registration;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }

    # Update an existing registration in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE registration SET LARPId=?, PersonId=?, Approved=?, RegisteredAt=?, PaymentReference=?, AmountToPay=?,
                Payed=?, IsMember=?, MembershipCheckedAt=?, NotComing=?, ToBeRefunded=?,
                RefundDate=?, IsOfficial=?, NPCDesire=?, HousingRequestId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->Approved, $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay, 
            $this->Payed, $this->IsMember, $this->MembershipCheckedAt, $this->NotComing, $this->ToBeRefunded, 
            $this->RefundDate, $this->IsOfficial, $this->NPCDesire, $this->HousingRequestId,$this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    	
    
    
    # Create a new registration in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO registration (LARPId, PersonId, Approved, RegisteredAt, 
            PaymentReference, AmountToPay, AmountPayed, Payed, IsMember,
            MembershipCheckedAt, NotComing, ToBeRefunded, RefundDate, IsOfficial, 
            NPCDesire, HousingRequestId) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->Approved, $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay,
            $this->AmountPayed, $this->Payed, $this->IsMember, $this->MembershipCheckedAt, $this->NotComing, $this->ToBeRefunded,
            $this->RefundDate, $this->IsOfficial, $this->NPCDesire, $this->HousingRequestId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function isApproved() {
        if (isset($this->Approved)) {
            return false;
        }
        return true;
    }
 
    public static function loadByIds($personId, $larpId)
    {
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $stmt = static::connectStatic()->prepare("SELECT * FROM `registration` WHERE PersonId = ? AND LARPId = ?");
        
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
    
    
    
    
    static function getAllPersons($larp) {
        //TODO returnera alla personer som är anmälda till lajvet
        return Array();
    }
    
}