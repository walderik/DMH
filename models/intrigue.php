<?php

class Intrigue extends BaseModel{
    
    public $Id;
    public $Number;
    public $Name;
    public $Active = 1;
    public $MainIntrigue = 0;
    public $Notes;
    public $LarpId;
    public $ResponsibleUserId;
    
    public static $orderListBy = 'Number';
    
    public static function newFromArray($post){
        $intrigue = static::newWithDefault();
        $intrigue->setValuesByArray($post);
        return $intrigue;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Number'])) $this->Number = $arr['Number'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Active'])) $this->Active = $arr['Active'];
        if (isset($arr['MainIntrigue'])) $this->MainIntrigue = $arr['MainIntrigue'];
        if (isset($arr['Notes'])) $this->Notes = $arr['Notes'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['ResponsibleUserId'])) $this->ResponsibleUserId = $arr['ResponsibleUserId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $intrigue = new self();
        $intrigue->LarpId = $current_larp->Id;
        return $intrigue;
    }
    
    # Update an existing intrigue in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue SET Number=?, Name=?, Active=?, MainIntrigue=?, Notes=?, LarpId=?, ResponsibleUserId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Number, $this->Name, $this->Active, $this->MainIntrigue, $this->Notes, $this->LarpId, $this->ResponsibleUserId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new intrigue in db
    public function create() {
        $this->Number = static::getMaxNumberForLarp($this->LarpId) + 1;
        
        $connection = $this->connect();
        
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue (Number, Name, Active, MainIntrigue, Notes, LarpId, ResponsibleUserId) VALUES (?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Number, $this->Name, $this->Active, $this->MainIntrigue, $this->Notes, $this->LarpId, $this->ResponsibleUserId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    
    public static function getMaxNumberForLarp($LarpId) {
        $sql = "SELECT MAX(Number) AS Num FROM regsys_intrigue WHERE LarpId=?;";
        return static::countQuery($sql, array($LarpId));
    }
    
    public static function allByLARP(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_intrigue WHERE LarpId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public function getResponsibleUser() {
        return User::loadById($this->ResponsibleUserId);
    }
    
    public function getSelectedIntrigueTypeIds() {
        $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM regsys_intriguetype_intrigue WHERE IntrigueId = ? ORDER BY IntrigueTypeId;");
        
        if (!$stmt->execute(array($this->Id))) {
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
            $resultArray[] = $row['IntrigueTypeId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    public function getIntrigueTypes(){
        return IntrigueType::getIntrigueTypesForIntrigue($this->Id);
    }
    
    
    public function isActive() {
        if ($this->Active == 0) return false;
        return true;
    }
    
    public function saveAllIntrigueTypes($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_intrigue (IntrigueTypeId, IntrigueId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllIntrigueTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_intrigue WHERE IntrigueId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function addRoleActors($rolesIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingRoleIds = array();
        $intrigue_actors = $this->getAllRoleActors();
        foreach ($intrigue_actors as $intrigue_actor) {
            $exisitingRoleIds[] = $intrigue_actor->RoleId;
        }
        
        $newRoleIds = array_diff($rolesIds,$exisitingRoleIds);
        //Koppla karaktären till intrigen
        foreach ($newRoleIds as $roleId) {
            $intrigue_actor = IntrigueActor::newWithDefault();
            $intrigue_actor->IntrigueId = $this->Id;
            $intrigue_actor->RoleId = $roleId;
            $intrigue_actor->create();
        }
    }
   
    
    public function addGroupActors($groupIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingGroupIds = array();
        $intrigue_actors = $this->getAllGroupActors();
        foreach ($intrigue_actors as $intrigue_actor) {
            $exisitingGroupIds[] = $intrigue_actor->GroupId;
        }
        
        $newGroupIds = array_diff($groupIds,$exisitingGroupIds);
        //Koppla karaktären till intrigen
        foreach ($newGroupIds as $groupId) {
            $intrigue_actor = IntrigueActor::newWithDefault();
            $intrigue_actor->IntrigueId = $this->Id;
            $intrigue_actor->GroupId = $groupId;
            $intrigue_actor->create();
        }
    }
    
    
    public function addProps($propIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $intrigue_props = $this->getAllProps();
        foreach ($intrigue_props as $intrigue_prop) {
            $exisitingIds[] = $intrigue_prop->PropId;
        }
        
        $newPropIds = array_diff($propIds,$exisitingIds);
        //Koppla rekvisitan till intrigen
        foreach ($newPropIds as $propId) {
            $intrigue_prop = Intrigue_Prop::newWithDefault();
            $intrigue_prop->IntrigueId = $this->Id;
            $intrigue_prop->PropId = $propId;
            $intrigue_prop->create();
        }
    }
    
    public function addNPCs($NPCIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $intrigue_npcs = $this->getAllNPCs();
        foreach ($intrigue_npcs as $intrigue_npc) {
            $exisitingIds[] = $intrigue_npc->NPCId;
        }
        
        $newNPCIds = array_diff($NPCIds,$exisitingIds);
        //Koppla rekvisitan till intrigen
        foreach ($newNPCIds as $NPCId) {
            $intrigue_npc = Intrigue_NPC::newWithDefault();
            $intrigue_npc->IntrigueId = $this->Id;
            $intrigue_npc->NPCId = $NPCId;
            $intrigue_npc->create();
        }
    }
 
    public function addNPCGroups($NPCGroupIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $intrigue_npcgroups = $this->getAllNPCGroups();
        foreach ($intrigue_npcgroups as $intrigue_npcgroup) {
            $exisitingIds[] = $intrigue_npcgroup->NPCGroupId;
        }
        
        $newNPCGroupIds = array_diff($NPCGroupIds,$exisitingIds);
        //Koppla rekvisitan till intrigen
        foreach ($newNPCGroupIds as $NPCGroupId) {
            $intrigue_npcgroup = Intrigue_NPCGroup::newWithDefault();
            $intrigue_npcgroup->IntrigueId = $this->Id;
            $intrigue_npcgroup->NPCGroupId = $NPCGroupId;
            $intrigue_npcgroup->create();
        }
    }
    
    
    public function addLetters($letterIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $intrigue_letters = $this->getAllLetters();
        foreach ($intrigue_letters as $intrigue_letter) {
            $exisitingIds[] = $intrigue_letter->LetterId;
        }
        
        $newLetterIds = array_diff($letterIds,$exisitingIds);
        //Koppla rekvisitan till intrigen
        foreach ($newLetterIds as $LetterId) {
            $intrigue_letter = Intrigue_Letter::newWithDefault();
            $intrigue_letter->IntrigueId = $this->Id;
            $intrigue_letter->LetterId = $LetterId;
            $intrigue_letter->create();
        }
    }

    public function addTelegrams($telegramIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $intrigue_telegrams = $this->getAllTelegrams();
        foreach ($intrigue_telegrams as $intrigue_telegram) {
            $exisitingIds[] = $intrigue_telegram->TelegramId;
        }
        
        $newTelegramIds = array_diff($telegramIds,$exisitingIds);
        //Koppla rekvisitan till intrigen
        foreach ($newTelegramIds as $telegramId) {
            $intrigue_telegram = Intrigue_Telegram::newWithDefault();
            $intrigue_telegram->IntrigueId = $this->Id;
            $intrigue_telegram->TelegramId = $telegramId;
            $intrigue_telegram->create();
        }
    }

    public function addIntrigueRelations($intrigueIds) {
        //TODO inte klart här (finns inget i databasen än
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $intrigues = $this->getAllIntrigueRelations();
        foreach ($intrigues as $intrigue) {
            $exisitingIds[] = $intrigues->TelegramId;
        }
        
        $newIntrigueIds = array_diff($intrigueIds,$exisitingIds);
        //Koppla rekvisitan till intrigen
        foreach ($newIntrigueIds as $telegramId) {
            $intrigue_telegram = Intrigue_Telegram::newWithDefault();
            $intrigue_telegram->IntrigueId = $this->Id;
            $intrigue_telegram->TelegramId = $telegramId;
            $intrigue_telegram->create();
        }
    }
    
    public function getAllGroupActors() {
        return IntrigueActor::getAllGroupActorsForIntrigue($this);
    }
    
    public function getAllRoleActors() {
        return IntrigueActor::getAllRoleActorsForIntrigue($this);
    }
    
    public function getAllProps() {
        return Intrigue_Prop::getAllPropsForIntrigue($this);
    }

    public function getAllNPCs() {
        return Intrigue_NPC::getAllNPCsForIntrigue($this);
    }

    public function getAllNPCGroups() {
        return Intrigue_NPCGroup::getAllNPCGroupsForIntrigue($this);
    }
    
    public function getAllLetters() {
        return Intrigue_Letter::getAllLettersForIntrigue($this);
    }
    
    public function getAllTelegrams() {
        return Intrigue_Telegram::getAllTelegramsForIntrigue($this);
    }

    public function getAllIntrigueRelations() {
        //TODO inte klart här
        $sql = "SELECT * FROM regsys_intrigue_relation WHERE IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public static function getAllIntriguesForIntrigueActor(IntrigueActor $intrigueActor) {
        if (!empty($intrigueActor->GroupId)) {
            return static::getAllIntriguesForGroup($intrigueActor->GroupId);
        }
        else {
            return static::getAllIntriguesForRole($intrigueActor->RoleId);
        }
    }
    
    public static function getAllIntriguesForGroup($groupId, $larpId) {
            $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
                "SELECT IntrigueId FROM regsys_intrigueactor WHERE GroupId = ? AND LarpId = ?) ORDER BY Id";
            return static::getSeveralObjectsqQuery($sql, array($groupId, $larpId));
    }
    
    public static function getAllIntriguesForRole($roleId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigueactor WHERE RoleId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($roleId, $larpId));
    }
    
}
