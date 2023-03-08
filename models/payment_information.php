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
        return new self();
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
    
    
}