<?php

class Invoice extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $ContactPersonId;
    public $Name;
    public $Description;
    public $DueDate;
    public $AmountPayed;
    public $PayedDate;
    
    public static $orderListBy = 'Id';
    
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
        $stmt = $this->connect()->prepare("UPDATE regsys_invoice SET LARPId=?, ContactPersonId=?, Name=?, Description=?, DueDate=?, AmountPayed=?,
                                                                  PayedDate=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->LARPId, $this->ContactPersonId, $this->Name, $this->Description, $this->DueDate, $this->AmountPayed,
            $this->PayedDate, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();

        $stmt = $connection->prepare("INSERT INTO regsys_invoice (LARPId, ContactPersonId, Name, Description, DueDate, AmountPayed,
            PayedDate) VALUES (?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->ContactPersonId, $this->Name, $this->Description, $this->DueDate, $this->AmountPayed,
            $this->PayedDate))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    
    public static function allBySelectedLARP(LARP $larp) {
        $sql = "SELECT * FROM regsys_invoice WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public function Amount() {
        $sql = "SELECT count(AmountToPay) AS Num FROM regsys_registration, regsys_invoice_participant WHERE ".
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
    
    
    
    
}