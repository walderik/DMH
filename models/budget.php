<?php

class Budget extends BaseModel{
    
    
    
    public  $Id;
    public  $AccountId;
    public  $LarpId;
    public  $FixedAmount = 0;
    public  $AmountPerPerson = 0;
    
    public static $orderListBy = 'AccountId';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['AccountId'])) $this->AccountId = $arr['AccountId'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['FixedAmount'])) $this->FixedAmount = $arr['FixedAmount'];
        if (isset($arr['AmountPerPerson'])) $this->AmountPerPerson = $arr['AmountPerPerson'];
    }
  
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $obj = new self();
        $obj->LarpId = $current_larp->Id;
        return $obj;
    }
    
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_budget SET FixedAmount=?, AmountPerPerson=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->FixedAmount, $this->AmountPerPerson, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    
    # Create a new in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_budget (AccountId, LarpId, FixedAmount, AmountPerPerson) VALUES (?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->AccountId, $this->LarpId, $this->FixedAmount, $this->AmountPerPerson))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function getAll(LARP $larp) {
        $sql = "SELECT * FROM regsys_budget WHERE LarpId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }

    public static function getByAccount(LARP $larp, Bookkeeping_Account $account) {
        $sql = "SELECT * FROM regsys_budget WHERE LarpId=? AND AccountId = ? ORDER BY ".static::$orderListBy.";";
        return static::getOneObjectQuery($sql, array($larp->Id, $account->Id));
    }
    
}