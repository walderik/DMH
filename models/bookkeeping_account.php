<?php

class Bookkeeping_Account extends BaseModel{
    
    
    
    public  $Id;
    public  $Name;
    public  $Number;
    
    public static $orderListBy = 'Number';
    
    public static function newFromArray($post){
        $campaign = static::newWithDefault();
        $campaign->setValuesByArray($post);
        return $campaign;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Number'])) $this->Number = $arr['Number'];
        
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        
        
    }
  
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_bookkeeping_account SET Name=?, Number=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Number, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    
    # Create a new in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_bookkeeping_account (Name, Number) VALUES (?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->Number))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public function inUse() {
        $sql = "SELECT Count(*) AS Num FROM regsys_bookkeeping WHERE BookeepingAccountId=?";
        $count = static::countQuery($sql, array($this->Id));
        if ($count > 0) return true;
        return false;
    }
    
}