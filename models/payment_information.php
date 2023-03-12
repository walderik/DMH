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
        if (isset($post['Id']))   $payment_information->Id = $post['Id'];
        if (isset($post['LARPId'])) $payment_information->LARPId = $post['LARPId'];
        if (isset($post['FromDate'])) $payment_information->FromDate = $post['FromDate'];
        if (isset($post['ToDate'])) $payment_information->ToDate = $post['ToDate'];
        if (isset($post['FromAge'])) $payment_information->FromAge = $post['FromAge'];
        if (isset($post['ToAge'])) $payment_information->ToAge = $post['ToAge'];
        if (isset($post['Cost'])) $payment_information->Cost = $post['Cost'];
        return $payment_information;
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
    
    public static function getPrice($date, $age) {
        global $tbl_prefix, $current_larp;
        $stmt = static::connectStatic()->prepare("SELECT * FROM `".$tbl_prefix."paymentinformation` WHERE 
                               DATE(FromDate) <= ? AND DATE(ToDate) >= ? AND 
                               FromAge <= ? AND ToAge >= ? AND LARPId = ?");
        
        if (!$stmt->execute(array($date, $date, $age, $age, $current_larp->Id))) {
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
        
        return static::newFromArray($row)->Cost;
        
    }
    # Update an existing object in db
    public function update() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE `".$tbl_prefix."paymentinformation` SET LARPId=?, FromDate=?, ToDate=?, FromAge=?, ToAge=?,
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
        global $tbl_prefix;
        $connection = $this->connect();

        $stmt = $connection->prepare("INSERT INTO `".$tbl_prefix."paymentinformation` (LARPId, FromDate, ToDate, FromAge, ToAge,
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
    
    public static function allBySelectedLARP() {
        global $tbl_prefix, $current_larp;
        
        $sql = "SELECT * FROM `".$tbl_prefix."paymentinformation` WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
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
    
    public static function errorReportBySelectedLARP() {
        global $current_larp;
        
        $errors = "";
        
        $allPaymentInformation = static::allBySelectedLARP();
        if (empty($all)) {
            if ($current_larp->RegistrationOpen == 1) $errors .= "<p><b>VIKTIGT Anmälan är öppen och ingen avgift är angiven</b></p>";
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