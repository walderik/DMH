<?php


class Registration extends BaseModel{

    
    public $Id;
    public $LARPId;
    public $PersonId;
    public $RegisteredAt;
    public $PaymentReference;
    public $AmountToPay;
    public $AmountPayed;
    public $Payed;
    public $IsMember;
    public $MembershipCheckedAt;
    public $NotComing;
    public $ToBeRefunded;
    public $RefundDate; 
    public $IsOfficial;
    public $NPCDesire;
    public $HousingRequestId;
    
    public static $orderListBy = 'RegisteredAt';
    
    
    public static function newFromArray($post){
        $registration = static::newWithDefault();
        if (isset($post['Id']))   $registration->Id = $post['Id'];
        if (isset($post['LARPId'])) $registration->LARPId = $post['LARPId'];
        if (isset($post['PersonId'])) $registration->PersonId = $post['PersonId'];
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
        $stmt = $this->connect()->prepare("UPDATE registration SET LARPId=?, PersonId=?, RegisteredAt=?, PaymentReference=?, AmountToPay=?,
                Payed=?, IsMember=?, MembershipCheckedAt=?, NotComing=?, ToBeRefunded=?,
                RefundDate=?, IsOfficial=?, NPCDesire=?, HousingRequestId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay, 
            $this->Payed, $this->IsMember, $this->MembershipCheckedAt, $this->NotComing, $this->ToBeRefunded, 
            $this->RefundDate, $this->IsOfficial, $this->NPCDesire, $this->HousingRequestId,$this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    //Id	
    
    
    # Create a new registration in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO registration (LARPId, PersonId, RegisteredAt, 
            PaymentReference, AmountToPay, AmountPayed, Payed, IsMember,
            MembershipCheckedAt, NotComing, ToBeRefunded, RefundDate, IsOfficial, 
            NPCDesire, HousingRequestId) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay,
            $this->Payed, $this->IsMember, $this->MembershipCheckedAt, $this->NotComing, $this->ToBeRefunded,
            $this->RefundDate, $this->IsOfficial, $this->NPCDesire, $this->HousingRequestId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    
    
    static function getAllPersons($larp) {
        //TODO returnera alla personer som är anmälda till lajvet
    }
    
}