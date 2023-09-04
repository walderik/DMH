<?php

class Intrigue_Telegram extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $TelegramId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['TelegramId'])) $this->TelegramId = $arr['TelegramId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue_telegram SET IntrigueId=?, TelegramId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->TelegramId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue_telegram (IntrigueId, TelegramId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->TelegramId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getIntrigue() {
        return Intrigue::loadById($this->IntrigueId);
    }

    
    public function getTelegram() {
        return Telegram::loadById($this->TelegramId);
    }

    public function getAllIntrigues() {
        return Intrigue::getAllIntriguesForIntrigueTelegram($this);
    }
    
    
    public static function delete($id)
    {
        $intrigueTelegram = static::loadById($id);
        $checkin_telegrams = $intrigueTelegram->getAllCheckinTelegrams();
        foreach ($checkin_telegrams as $checkin_telegram) IntrigueActor_CheckinTelegram::delete($checkin_telegram->Id);
        
        parent::delete($id);
    }
    
    public static function getAllTelegramsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT regsys_intrigue_telegram.* FROM regsys_intrigue_telegram, regsys_telegram WHERE ".
        "regsys_intrigue_telegram.IntrigueId = ? AND ".
        "regsys_intrigue_telegram.TelegramId = regsys_telegram.Id ".
        "ORDER BY regsys_telegram.Deliverytime";
        
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public function getAllCheckinTelegrams() {
        return IntrigueActor_CheckinTelegram::getAllCheckinTelegramsForIntrigueTelegram($this);
    }
    
    
}
