<?php

class IntrigueActor extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $RoleId;
    public $GroupId;
    public $IntrigueText;
    public $OffInfo;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['IntrigueText'])) $this->IntrigueText = $arr['IntrigueText'];
        if (isset($arr['OffInfo'])) $this->OffInfo = $arr['OffInfo'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor SET IntrigueId=?, RoleId=?, GroupId=?, IntrigueText=?, OffInfo=? WHERE Id = ?");
        if (!$stmt->execute(array($this->IntrigueId, $this->RoleId, $this->GroupId, $this->IntrigueText, $this->OffInfo, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor (IntrigueId, RoleId, GroupId, IntrigueText, OffInfo) VALUES (?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->RoleId, $this->GroupId, $this->IntrigueText, $this->OffInfo))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getRole() {
        if (empty($this->RoleId)) return null;
        return Role::loadById($this->RoleId);
    }
    
    public function getGroup() {
        if (empty($this->GroupId)) return null;
        return Group::loadById($this->GroupId);
    }

    
    public function getIntrigue() {
        return Intrigue::loadById($this->IntrigueId);
    }
    
    public static function getAllGroupActorsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE GroupId IS NOT NULL AND IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }

    public static function getGroupActorForIntrigue(Intrigue $intrigue, Group $group) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE GroupId = ? AND IntrigueId = ? ORDER BY Id";
        return static::getOneObjectQuery($sql, array($group->Id, $intrigue->Id));
    }
    
    public static function getAllRoleActorsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE RoleId IS NOT NULL AND IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public static function getRoleActorForIntrigue(Intrigue $intrigue, Role $role) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE RoleId = ? AND IntrigueId = ? ORDER BY Id";
        return static::getOneObjectQuery($sql, array($role->Id, $intrigue->Id));
    }
    
    public function getAllIntrigues() {
        return Intrigue::getAllIntriguesForIntrigueActor($this);
    }
    
    
    public static function delete($id)
    {
        //TODO ta bort alla länkar
        parent::delete($id);
     }
 
     
     public function addCheckinLetters($intrigue_letterIds) {
         //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_letters = $this->getAllCheckinLetters();
         foreach ($intrigue_letters as $intrigue_letter) {
             $exisitingIds[] = $intrigue_letter->IntrigueLetterId;
         }
         
         $newLetterIds = array_diff($intrigue_letterIds,$exisitingIds);
         //Koppla brevet till aktören
         foreach ($newLetterIds as $intrigue_letterId) {
             $intrigueactor_checkinletter = IntrigueActor_CheckinLetter::newWithDefault();
             $intrigueactor_checkinletter->IntrigueActorId = $this->Id;
             $intrigueactor_checkinletter->IntrigueLetterId = $intrigue_letterId;
             $intrigueactor_checkinletter->create();
         }
     }
     
     public function addCheckinTelegrams($intrigue_telegramIds) {
          //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_telegrams = $this->getAllCheckinTelegrams();
         foreach ($intrigue_telegrams as $intrigue_telegram) {
             $exisitingIds[] = $intrigue_telegram->IntrigueTelegramId;
         }
         $newTelegramIds = array_diff($intrigue_telegramIds,$exisitingIds);

         //Koppla telegrammet till aktören
         foreach ($newTelegramIds as $intrigue_telegramId) {
             $intrigueactor_checkintelegram = IntrigueActor_CheckinTelegram::newWithDefault();
             $intrigueactor_checkintelegram->IntrigueActorId = $this->Id;
             $intrigueactor_checkintelegram->IntrigueTelegramId = $intrigue_telegramId;
             $intrigueactor_checkintelegram->create();
         }
     }
     
     public function addCheckinProps($intrigue_propIds) {
         //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_props = $this->getAllCheckinProps();
         foreach ($intrigue_props as $intrigue_prop) {
             $exisitingIds[] = $intrigue_prop->IntriguePropId;
         }
         
         $newPropIds = array_diff($intrigue_propIds,$exisitingIds);
         //Koppla rekvisitan till aktören
         foreach ($newPropIds as $intrigue_propId) {
             $intrigueactor_checkinprop = IntrigueActor_CheckinProp::newWithDefault();
             $intrigueactor_checkinprop->IntrigueActorId = $this->Id;
             $intrigueactor_checkinprop->IntriguePropId = $intrigue_propId;
             $intrigueactor_checkinprop->create();
         }
     }
     
     public function getAllCheckinLetters() {
         return IntrigueActor_CheckinLetter::getAllCheckinLettersForIntrigueActor($this);
     }
     
     public function getAllCheckinTelegrams() {
         return IntrigueActor_CheckinTelegram::getAllCheckinTelegramsForIntrigueActor($this);
     }
     
     public function getAllCheckinProps() {
         return IntrigueActor_CheckinProp::getAllCheckinPropsForIntrigueActor($this);
     }
     
     public function getAllPropsForCheckin() {
         return Prop::getAllCheckinPropsForIntrigueActor($this);
     }

     public function getAllLettersForCheckin() {
         return Letter::getAllCheckinLettersForIntrigueActor($this);
     }
     
     public function getAllTelegramsForCheckin() {
         return Telegram::getAllCheckinTelegramsForIntrigueActor($this);
     }
     
     
     public function getAllKnownProps() {
         //TODO gör anrop
         return array();
     }

     public function getAllKnownActors() {
         //TODO gör anrop
         return array();
     }
     
     public function getAllKnownNPCs() {
         //TODO gör anrop
         return array();
     }
     
     public function removeProp($propId) {
         $checkin_prop=IntrigueActor_CheckinProp::loadByIds($propId, $this->Id);
         IntrigueActor_CheckinProp::delete($checkin_prop->Id);
     }

     public function removeLetter($letterId) {
         //TODO Gör innehåll
     }

     public function removeTelegram($telegramId) {
         //TODO Gör innehåll
     }
}
