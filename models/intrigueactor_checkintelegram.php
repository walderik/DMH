<?php

class IntrigueActor_CheckinTelegram extends BaseModel{
    
    public $Id;
    public $IntrigueActorId;
    public $IntrigueTelegramId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueActorId'])) $this->IntrigueActorId = $arr['IntrigueActorId'];
        if (isset($arr['IntrigueTelegramId'])) $this->IntrigueTelegramId = $arr['IntrigueTelegramId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_checkintelegram SET IntrigueActorId=?, IntrigueTelegramId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueTelegramId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_checkintelegram (IntrigueActorId, IntrigueTelegramId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueTelegramId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getIntrigueActor() {
        return IntrigueActor::loadById($this->IntrigueActorId);
    }
    
    public function getIntrigueTelegram() {
        return Intrigue_Letter::loadById($this->IntrigueTelegramId);
    }
    
    public static function getAllCheckinTelegramsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_checkintelegram WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function loadByIds($telegramId, $intrigueActorId) {
        $sql = "SELECT regsys_intrigueactor_checkintelegram.* FROM regsys_intrigueactor_checkintelegram, regsys_intrigue_telegram, regsys_intrigueactor WHERE ".
            "regsys_intrigueactor.Id = ?  AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_checkintelegram.IntrigueActorId AND ".
            "regsys_intrigue_telegram.Id = regsys_intrigueactor_checkintelegram.IntrigueTelegramId AND ".
            "regsys_intrigue_telegram.TelegramId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue_telegram.IntrigueId";
        return static::getOneObjectQuery($sql, array($intrigueActorId, $telegramId));
    }
    
    
}
