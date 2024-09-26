<?php

class Bookkeeping extends BaseModel{
    
    
    
    public  $Id;
    public  $Number;
    public  $LarpId;
    public  $CampaignId;
    public  $Headline;
    public  $BookkeepingAccountId;
    public  $Text;
    public  $Who;
    public  $PersonId;
    public  $Amount;
    public  $CreationDate;
    public  $AccountingDate;
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
        if (isset($arr['LarpId'])) $this->CampaignId = $arr['LarpId'];
        if (isset($arr['Headline'])) $this->Headline = $arr['Headline'];
        if (isset($arr['BookkeepingAccountId'])) $this->BookkeepingAccountId = $arr['BookkeepingAccountId'];
        if (isset($arr['Text'])) $this->Text = $arr['Text'];
        if (isset($arr['Who'])) $this->Who = $arr['Who'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['Amount'])) $this->Amount = $arr['Amount'];
        if (isset($arr['CreationDate'])) $this->CreationDate = $arr['CreationDate'];
        if (isset($arr['AccountingDate'])) {
            if ($arr['AccountingDate']== '0000-00-00' || empty($arr['AccountingDate'])) $this->AccountingDate = NULL;
            else $this->AccountingDate = $arr['AccountingDate'];
        }
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        
    }
  
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_user;
        $obj = new self();
        $obj->CreationDate = date("Y-m-d");
        $obj->LarpId = $current_larp->Id;
        $obj->UserId=$current_user->Id;
        return $obj;
    }
    
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_bookkeeping SET Headline=?, BookkeepingAccountId=?, 
        Text=?, Who=?, Amount=?, CreationDate=?, AccountingDate=?, ImageId=?, PersonId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Headline, $this->BookkeepingAccountId,
            $this->Text, $this->Who, $this->Amount, $this->CreationDate, $this->AccountingDate, $this->ImageId, $this->PersonId, $this->Id))) {
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
        $stmt = $connection->prepare("INSERT INTO regsys_bookkeeping (Number, LarpId, CampaignId, Headline, BookkeepingAccountId, 
        Text, Who, PersonId, Amount, CreationDate, AccountingDate, ImageId) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Number, $this->LarpId, $this->CampaignId, $this->Headline, $this->BookkeepingAccountId,
            $this->Text, $this->Who, $this->PersonId, $this->Amount, $this->CreationDate, $this->AccountingDate, $this->ImageId))) {
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
    
    public function getPerson() {
        return Person::loadById($this->PersonId);
    }
    

    public function getLarp() {
        if (empty($this->LarpId)) return NULL;
        return LARP::loadById($this->LarpId);
    }

    public function getCampaign() {
        if (empty($this->CampaignId)) return NULL;
        return Campaign::loadById($this->CampaignId);
    }
    
    public static function allFinished(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_bookkeeping WHERE LarpId = ? AND AccountingDate IS NOT NULL ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allUnFinished(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_bookkeeping WHERE LarpId = ? AND AccountingDate IS NULL ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allFinishedCampaign(Campaign $campaign, $year) {
        if (is_null($campaign)) return Array();
        $sql = "SELECT * FROM regsys_bookkeeping WHERE CampaignId = ? AND AccountingDate IS NOT NULL AND CreationDate LIKE ?  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($campaign->Id,$year."%"));
    }
    
    public static function allUnFinishedCampaign(Campaign $campaign, $year) {
        if (is_null($campaign)) return Array();
        $sql = "SELECT * FROM regsys_bookkeeping WHERE CampaignId = ? AND AccountingDate IS NULL AND CreationDate LIKE ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($campaign->Id,$year."%"));
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
        $sql = "SELECT sum(Amount) AS Num FROM regsys_bookkeeping WHERE LarpId = ? AND Amount > 0 AND AccountingDate IS NOT NULL ORDER BY ".static::$orderListBy.";";
        return static::countQuery($sql, array($larp->Id));
        
    }

    public static function sumRegisteredExpenses(LARP $larp) {
        $sql = "SELECT sum(Amount) AS Num FROM regsys_bookkeeping WHERE LarpId = ? AND Amount < 0 AND AccountingDate IS NOT NULL ORDER BY ".static::$orderListBy.";";
        return static::countQuery($sql, array($larp->Id));
        
    }

    public static function sumRegisteredIncomesCampaign(Campaign $campaign, int $year) {
        $sql = "SELECT sum(Amount) AS Num FROM regsys_bookkeeping WHERE CampaignId = ? AND Amount > 0 AND AccountingDate LIKE ? ORDER BY ".static::$orderListBy.";";
        return static::countQuery($sql, array($campaign->Id,$year."%"));
        
    }
    
    public static function sumRegisteredExpensesCampaign(Campaign $campaign, int $year) {
        $sql = "SELECT sum(Amount) AS Num FROM regsys_bookkeeping WHERE CampaignId = ? AND Amount < 0 AND AccountingDate LIKE ? ORDER BY ".static::$orderListBy.";";
        return static::countQuery($sql, array($campaign->Id,$year."%"));
        
    }
    
    
     
}