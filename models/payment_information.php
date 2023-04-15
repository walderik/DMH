<?php

class PaymentInformation extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $FromDate;
    public $ToDate;
    public $FromAge;
    public $ToAge;
    public $Cost;
    
    public static $orderListBy = 'FromDate';
    
    public static function newFromArray($post){
        $payment_information = static::newWithDefault();
        $payment_information->setValuesByArray($post);
        return $payment_information;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['FromDate'])) $this->FromDate = $arr['FromDate'];
        if (isset($arr['ToDate'])) $this->ToDate = $arr['ToDate'];
        if (isset($arr['FromAge'])) $this->FromAge = $arr['FromAge'];
        if (isset($arr['ToAge'])) $this->ToAge = $arr['ToAge'];
        if (isset($arr['Cost'])) $this->Cost = $arr['Cost'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $payment = new self();
        $payment->LARPId = $current_larp->Id;
        if (is_null($payment->FromDate)) $payment->FromDate = PaymentInformation::nextMissingStartDate();
        if (is_null($payment->ToDate))   $payment->ToDate   = PaymentInformation::nextMissingEndDate();
        if (is_null($payment->FromAge))  $payment->FromAge  = $current_larp->getCampaign()->MinimumAge;
        if (is_null($payment->ToAge))    $payment->ToAge    = 200;
        return $payment;
    }
    
    public static function getPrice($date, $age, $larp) {
        $sql = "SELECT * FROM regsys_paymentinformation WHERE 
                               DATE(FromDate) <= ? AND DATE(ToDate) >= ? AND 
                               FromAge <= ? AND ToAge >= ? AND LARPId = ?";
        $payment_information = static::getOneObjectQuery($sql, array($date, $date, $age, $age, $larp->Id));
        if (empty($payment_information)) return 0;
        return $payment_information->Cost;
        
    }
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_paymentinformation SET LARPId=?, FromDate=?, ToDate=?, FromAge=?, ToAge=?,
                                                                  Cost=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->LARPId, $this->FromDate, $this->ToDate, $this->FromAge, $this->ToAge,
            $this->Cost, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();

        $stmt = $connection->prepare("INSERT INTO regsys_paymentinformation (LARPId, FromDate, ToDate, FromAge, ToAge,
                                                                Cost) VALUES (?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->FromDate, $this->ToDate, $this->FromAge, $this->ToAge,
            $this->Cost))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function allBySelectedLARP(LARP $larp) {
        $sql = "SELECT * FROM regsys_paymentinformation WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function errorReportBySelectedLARP(Larp $larp) {
       
        $errors = "";
        
        $allPaymentInformation = static::allBySelectedLARP($larp);
        if (empty($allPaymentInformation)) {
            if ($larp->RegistrationOpen == 1) $errors .= "<p><b>VIKTIGT Anmälan är öppen och ingen avgift är angiven</b></p>";
            $errors .= "<p>Det finns ännu inget registrerat för vad det kostar att delta på lajvet. Tills vidare är det gratis!</p>\n";
        }
        else { 
            foreach ($allPaymentInformation as $paymentInformation) {
                
            }
        }
        
        if ($errors == '') $errors == "All Fine";
        return $errors;
    }
    
    # Metod som plockar fram nästa datum som inte har någon betalningsinformation
    public static function nextMissingStartDate() {
        $first = date("Y-m-d");
        
        
        return $first;
    }
    
    # Metod som plockar fram sista datum som inte har någon betalningsinformation
    public static function nextMissingEndDate() {
        
        global $current_larp;
       
        
        if (!isset($current_larp)) return date("Y-m-d");
        
        $last = $current_larp->LatestRegistrationDate;
        
        
        return $last;
    }
    
    
}