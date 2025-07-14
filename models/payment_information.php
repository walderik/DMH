<?php

class PaymentInformation extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $FromDate;
    public $ToDate;
    public $FromAge;
    public $ToAge;
    public $Cost;
    public $FoodDescription;
    public $FoodCost;
    
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
        if (!empty($arr['FoodDescription'])) {
            if (is_array($arr['FoodDescription'])) $this->FoodDescription = $arr['FoodDescription'];
            else $this->FoodDescription = explode(";",$arr['FoodDescription']);
        }
        if (isset($arr['FoodCost'])) {
            if (is_array($arr['FoodCost'])) $this->FoodCost = $arr['FoodCost'];
            else $this->FoodCost = explode(";",$arr['FoodCost']);
        }
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
    
    # Update an existing object in db
    public function update() {
        $foodDescription = "";
        $foodCost = "";
        if (is_array($this->FoodDescription)) $foodDescription = implode(";", $this->FoodDescription);
        if (is_array($this->FoodCost)) $foodCost = implode(";", $this->FoodCost);
        
        $stmt = $this->connect()->prepare("UPDATE regsys_paymentinformation SET LARPId=?, FromDate=?, ToDate=?, FromAge=?, ToAge=?,
                                                                  Cost=?, FoodDescription=?, FoodCost=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->LARPId, $this->FromDate, $this->ToDate, $this->FromAge, $this->ToAge,
            $this->Cost, $foodDescription, $foodCost, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $foodDescription = "";
        $foodCost = "";
        if (is_array($this->FoodDescription)) $foodDescription = implode(";", $this->FoodDescription);
        if (is_array($this->FoodCost)) $foodCost = implode(";", $this->FoodCost);

        $stmt = $connection->prepare("INSERT INTO regsys_paymentinformation (LARPId, FromDate, ToDate, FromAge, ToAge,
            Cost, FoodDescription, FoodCost) VALUES (?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->FromDate, $this->ToDate, $this->FromAge, $this->ToAge,
            $this->Cost, $foodDescription, $foodCost))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    public static function get($date, $age, $larp) {
        $sql = "SELECT * FROM regsys_paymentinformation WHERE
                               DATE(FromDate) <= ? AND DATE(ToDate) >= ? AND
                               FromAge <= ? AND ToAge >= ? AND LARPId = ?";
        return static::getOneObjectQuery($sql, array($date, $date, $age, $age, $larp->Id));
        
    }
    
    public static function getPrice($date, $age, $larp, $foodChoice) {
        $payment_information = static::get($date, $age, $larp);
        if (empty($payment_information)) return null;
        $base_cost = $payment_information->Cost;
        $food_cost = 0;
        if (isset($foodChoice)) {
            $key = array_search($foodChoice, $payment_information->FoodDescription);
            $food_cost = $payment_information->FoodCost[$key];
        }
        return $base_cost + $food_cost;
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