<?php

class Invoice extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $ContactPersonId;
    public $Name;
    public $Description;
    public $DueDate;
    public $Number;
    public $PaymentReference;
    public $AmountPayed;
    public $PayedDate;
    
    public static $orderListBy = 'Number';
    
    public static function newFromArray($post){
        $payment_information = static::newWithDefault();
        $payment_information->setValuesByArray($post);
        return $payment_information;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['ContactPersonId'])) $this->ContactPersonId = $arr['ContactPersonId'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['DueDate'])) $this->DueDate = $arr['DueDate'];
        if (isset($arr['Number'])) $this->Number = $arr['Number'];
        if (isset($arr['PaymentReference'])) $this->PaymentReference = $arr['PaymentReference'];
        if (isset($arr['AmountPayed'])) $this->AmountPayed = $arr['AmountPayed'];
        if (isset($arr['PayedDate'])) $this->PayedDate = $arr['PayedDate'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $invoice = new self();
        $invoice->LARPId = $current_larp->Id;
        return $invoice;
    }
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_invoice SET LARPId=?, ContactPersonId=?, Name=?, Description=?, DueDate=?, Number=?, PaymentReference=?, AmountPayed=?,
                                                                  PayedDate=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->LARPId, $this->ContactPersonId, $this->Name, $this->Description, $this->DueDate, $this->Number, $this->PaymentReference, $this->AmountPayed,
            $this->PayedDate, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $larp = $this->getLarp();
        $this->PaymentReference = "FAKT" . $larp->PaymentReferencePrefix . $this->LARPId . $this->Id;
        $this->Number = static::getMaxNumberForLarp($this->LARPId) + 1;
        
        $connection = $this->connect();

        $stmt = $connection->prepare("INSERT INTO regsys_invoice (LARPId, ContactPersonId, Name, Description, DueDate, Number, PaymentReference, AmountPayed,
            PayedDate) VALUES (?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->ContactPersonId, $this->Name, $this->Description, $this->DueDate, $this->Number, $this->PaymentReference, $this->AmountPayed,
            $this->PayedDate))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function getMaxNumberForLarp($LarpId) {
        $sql = "SELECT MAX(Number) AS Num FROM regsys_invoice WHERE LarpId=?;";
        return static::countQuery($sql, array($LarpId));
    }
    
    
    
    public static function allBySelectedLARP(LARP $larp) {
        $sql = "SELECT * FROM regsys_invoice WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public function getLarp() {
        return LARP::loadById($this->LARPId);
    }
    
    
    
    public function Amount() {
        $sql = "SELECT sum(AmountToPay) AS Num FROM regsys_registration, regsys_invoice_participant WHERE ".
            "regsys_registration.Id = regsys_invoice_participant.RegistrationId AND ".
            "regsys_invoice_participant.InvoiceId=?";
        return static::countQuery($sql, array($this->Id));
    }
    
    public function hasPayed() {
        if ($this->Amount() <= $this->AmountPayed) {
            return true;
        }
        return false;
        
    }
    
    public function isPastDueDate() {
        $date1=date_create($this->DueDate);
        $date2=date_create();
        $diff=date_diff($date1,$date2);
        if ($diff->days < 0) return true;
        return false;
    }
    
    
    public function getConcerendRegistrations() {
        $sql = "SELECT * FROM regsys_registration WHERE Id IN (".
            "SELECT RegistrationId FROM regsys_invoice_participant WHERE InvoiceId=?)";
        return Registration::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    public function isSent() {
       return false;
    }
    
    public function getSentDate() {
        $now = new Datetime();
        return date_format($now,"Y-m-d");
    }
    
    public function hasContactPerson() {
        if (isset($this->ContactPersonId)) return true;
        return false;
    }
    
    public function getContactPerson() {
        if (isset($this->ContactPersonId)) return Person::loadById($this->ContactPersonId);
        return null;
    }
    
    public function addConcernedPersons($personIds) {
        //Ta reda på vilka som inte redan är kopplade till fakturan
        $exisitingPersonIds = array();
        $concernedRegistrationArr = $this->getConcerendRegistrations();
        foreach ($concernedRegistrationArr as $registration) {
            $exisitingPersonIds[] = $registration->PersonId;
        }
        
        $newPersonIds = array_diff($personIds,$exisitingPersonIds);
        foreach ($newPersonIds as $personId) {
            $this->addConcernedPerson($personId);
         }
    }
    
    private function addConcernedPerson($personId) {
        $registration = Registration::loadByIds($personId, $this->LARPId);
        if (isset($registration)) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_invoice_participant (InvoiceId, RegistrationId) VALUES (?,?);");
            if (!$stmt->execute(array($this->Id, $registration->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
    }
    
    public function removeConcernedRegistration($registrationId) {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_invoice_participant WHERE InvoiceId=? AND RegistrationId=?;");
        if (!$stmt->execute(array($this->Id, $registrationId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public static function delete($id)
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_invoice_participant WHERE InvoiceId=?;");
        if (!$stmt->execute(array($id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        parent::delete($id);
    }
    
    
    
}