<?php

class Bookkeeping extends BaseModel{
    
    
    
    public  $Id;
    public  $Number;
    public  $LarpId;
    public  $Headline;
    public  $BookkeepingAccountId;
    public  $Text;
    public  $Who;
    public  $UserId;
    public  $Amount;
    public  $Date;
    public  $ImageId;
    
    public static $orderListBy = 'Number';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Number'])) $this->Number = $arr['Number'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['Headline'])) $this->Headline = $arr['Headline'];
        if (isset($arr['BookkeepingAccountId'])) $this->BookkeepingAccountId = $arr['BookkeepingAccountId'];
        if (isset($arr['Text'])) $this->Text = $arr['Text'];
        if (isset($arr['Who'])) $this->Who = $arr['Who'];
        if (isset($arr['UserId'])) $this->UserId = $arr['UserId'];
        if (isset($arr['Amount'])) $this->Amount = $arr['Amount'];
        if (isset($arr['Date'])) $this->Date = $arr['Date'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        
        
    }
  
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_user;
        $obj = new self();
        $obj->Date = date("Y-m-d");
        $obj->LarpId = $current_larp->Id;
        $obj->UserId=$current_user->Id;
        return $obj;
    }
    
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_bookkeeping SET Headline=?, BookkeepingAccountId=?, 
        Text=?, Who=?, Amount=?, Date=?, ImageId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Headline, $this->BookkeepingAccountId,
            $this->Text, $this->Who, $this->Amount, $this->Date, $this->ImageId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new in db
    public function create() {
        $sql = "SELECT max(Number) as Num FROM regsys_bookkeeping WHERE LarpId=?";
        $max_number = static::countQuery($sql, array($this->LarpId)); 
        if (empty($max_number)) $max_number = 0;
        $this->Number = $max_number + 1;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_bookkeeping (Number, LarpId, Headline, BookkeepingAccountId, 
        Text, Who, UserId, Amount, Date, ImageId) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Number, $this->LarpId, $this->Headline, $this->BookkeepingAccountId,
            $this->Text, $this->Who, $this->UserId, $this->Amount, $this->Date, $this->ImageId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    public function getBookkeepingAccount() {
        return Bookkeeping_Account::loadById($this->BookkeepingAccountId);
    }
    
    public function getLarp() {
        return LARP::loadById($this->LarpId);
    }
    
    
    public static function allByLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_bookkeeping WHERE LarpId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public function hasImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
    
    public function getImage() {
        if (empty($this->ImageId)) return null;
        return Image::loadById($this->ImageId);
    }
    
    public static function sumRegisteredIncomes(LARP $larp) {
        $sql = "SELECT sum(Amount) AS Num FROM regsys_bookkeeping WHERE LarpId = ? AND Amount > 0 ORDER BY ".static::$orderListBy.";";
        return static::countQuery($sql, array($larp->Id));
        
    }

    public static function sumRegisteredExpenses(LARP $larp) {
        $sql = "SELECT sum(Amount) AS Num FROM regsys_bookkeeping WHERE LarpId = ? AND Amount < 0 ORDER BY ".static::$orderListBy.";";
        return static::countQuery($sql, array($larp->Id));
        
    }
    
}