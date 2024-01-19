<?php

class Intrigue extends BaseModel{
    
    public $Id;
    public $Number;
    public $Name;
    public $Active = 1;
    public $MainIntrigue = 0;
    public $CommonText;
    public $Notes;
    public $LarpId;
    public $ResponsibleUserId;
    public $PreviousInstanceId;
    
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
        if (isset($arr['CommonText'])) $this->CommonText = $arr['CommonText'];
        if (isset($arr['Notes'])) $this->Notes = $arr['Notes'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['ResponsibleUserId'])) $this->ResponsibleUserId = $arr['ResponsibleUserId'];
        if (isset($arr['PreviousInstanceId'])) $this->PreviousInstanceId = $arr['PreviousInstanceId'];
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
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue SET Number=?, Name=?, Active=?, MainIntrigue=?, CommonText=?, Notes=?, LarpId=?, ResponsibleUserId=?, PreviousInstanceId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Number, $this->Name, $this->Active, $this->MainIntrigue, $this->CommonText, $this->Notes, $this->LarpId, $this->ResponsibleUserId, $this->PreviousInstanceId, $this->Id))) {
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
        
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue (Number, Name, Active, MainIntrigue, CommonText, Notes, LarpId, ResponsibleUserId, PreviousInstanceId) VALUES (?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Number, $this->Name, $this->Active, $this->MainIntrigue, $this->CommonText, $this->Notes, $this->LarpId, $this->ResponsibleUserId, $this->PreviousInstanceId))) {
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
    
    public static function allNotContinuedInCampaign(LARP $larp) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id NOT IN (".
        "SELECT PreviousInstanceId FROM regsys_intrigue WHERE PreviousInstanceId IS NOT NULL) AND ".
        "LarpId IN (".
        "SELECT Id from regsys_larp WHERE CampaignId=? AND Id != ?) ".
        "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $larp->Id));
        
    }
    
    
    public function getResponsibleUser() {
        return User::loadById($this->ResponsibleUserId);
    }
    
 
    public function getLarp() {
        return LARP::loadById($this->LarpId);
    }
    
    public function getPreviousInstace() {
        if (isset($this->PreviousInstanceId)) return Intrigue::loadById($this->PreviousInstanceId);
        return null;
    }
    
    public function getPreviousInstaces() {
        $intrigues = array();
        
        $previuos = $this->getPreviousInstace();
        while(isset($previuos)) {
            $intrigues[] = $previuos;
            $previuos = $previuos->getPreviousInstace();
        }
        return $intrigues;

    }
    
    public static function continueIntrigues($intrigueIds, LARP $larp, User $user) {
        foreach($intrigueIds as $intrigueId) {
            $previousIntrigue = Intrigue::loadById($intrigueId);
            $newIntrigue = Intrigue::newWithDefault();
            
            //Kopiera intrighuvudet
            $newIntrigue->Name = $previousIntrigue->Name;
            $newIntrigue->MainIntrigue = $previousIntrigue->MainIntrigue;
            $newIntrigue->CommonText = $previousIntrigue->CommonText;
            $newIntrigue->Notes = $previousIntrigue->Notes;
            $newIntrigue->LarpId = $larp->Id;
            $newIntrigue->ResponsibleUserId = $user->Id;
            $newIntrigue->PreviousInstanceId = $previousIntrigue->Id;
            $newIntrigue->create();
            
            //Kopiera intrigtyper
            $intrigueTypeIds = $previousIntrigue->getSelectedIntrigueTypeIds();
            $newIntrigue->saveAllIntrigueTypes($intrigueTypeIds);
            
            //Koppla alla roller och grupper
            $groupactors = $previousIntrigue->getAllGroupActors();
            foreach($groupactors as $groupactor) {
                $newactor = IntrigueActor::newWithDefault();
                $newactor->IntrigueId = $newIntrigue->Id;
                $newactor->GroupId = $groupactor->GroupId;
                $newactor->create();
            }
            
            $roleactors = $previousIntrigue->getAllRoleActors();
            foreach($roleactors as $roleactor) {
                $newactor = IntrigueActor::newWithDefault();
                $newactor->IntrigueId = $newIntrigue->Id;
                $newactor->RoleId = $roleactor->RoleId;
                $newactor->create();
            }
            
            //Koppla rekvisita
            $props = $previousIntrigue->getAllProps();
            foreach($props as $prop) {
                $newProp = Intrigue_Prop::newWithDefault();
                $newProp->IntrigueId = $newIntrigue->Id;
                $newProp->PropId = $prop->PropId;
                $newProp->create();
            }
            
            
            //Koppla pdf'er
            $pdfs = $previousIntrigue->getAllPdf();
            foreach($pdfs as $pdf) {
                $newPdf = Intrigue_Pdf::newWithDefault();
                $newPdf->IntrigueId = $newIntrigue->Id;
                $newPdf->Filename = $pdf->Filename;
                $newPdf->FileData = $pdf->FileData;
                $newPdf->create();
            }
            
        }
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

    public function addPdf() {
        if ($_FILES["bilaga"]["size"] > 5242880) return array();
        
        $file_tmp  = $_FILES['bilaga']['tmp_name'];
        if(empty($file_tmp)) return array();
        $fileSize = filesize($file_tmp);
        if ($fileSize > 5242880) return array();
        
        $allowed = array("pdf" => "application/pdf");
        $filetype = $_FILES["bilaga"]["type"];
        $file_name = $_FILES['bilaga']['name'];
        
        // Validate file extension
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) return array();
        // Validate type of the file
        if(!in_array($filetype, $allowed)) return array();
        
        $the_file = file_get_contents($file_tmp);
        
        $intrigue_pdf = Intrigue_Pdf::newWithDefault();
        $intrigue_pdf->IntrigueId = $this->Id;
        $intrigue_pdf->Filename = $file_name;
        $intrigue_pdf->FileData = $the_file;
        $intrigue_pdf->create();
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
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $intrigues = $this->getAllIntrigueRelations();
        foreach ($intrigues as $intrigue) {
            $exisitingIds[] = $intrigue->Id;
        }
        
        $newIntrigueIds = array_diff($intrigueIds,$exisitingIds);
        //Skapa relation mellan intrigerna
        foreach ($newIntrigueIds as $intrigueId) {
            $this->createNewIntrigueRelation($this->Id, $intrigueId);
        }
    }
    
    private function createNewIntrigueRelation($intrigueId1, $intrgueId2) {
        if ($intrigueId1 == $intrgueId2) return;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_relationship () VALUES();");
        if (!$stmt->execute()) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $relationId = $connection->lastInsertId();
        
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_intrigue_relationship (RelationshipId, IntrigueId) VALUES (?,?);");
        if (!$stmt->execute(array($relationId, $intrigueId1))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_intrigue_relationship (RelationshipId, IntrigueId) VALUES (?,?);");
        if (!$stmt->execute(array($relationId, $intrgueId2))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    public static function removeRelation($intrigueId1, $intrigueId2) {
        $connection = static::connectStatic();
        
        //Hitta id för relationen
        $stmt = $connection->prepare("SELECT regsys_relationship.Id FROM regsys_relationship, regsys_intrigue_relationship r1, regsys_intrigue_relationship r2 WHERE ".
            "regsys_relationship.Id = r1.RelationshipId AND ".
            "regsys_relationship.Id = r2.RelationshipId AND ".
            "r1.IntrigueId = ? AND ".
            "r2.IntrigueId = ?;");
        
        if (!$stmt->execute(array($intrigueId1, $intrigueId2))) {
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
        $relationshipId = $row['Id'];
        
        $stmt = $connection->prepare("DELETE FROM regsys_intrigue_relationship WHERE RelationshipId = ?");
        if (!$stmt->execute(array($relationshipId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = $connection->prepare("DELETE FROM regsys_relationship WHERE Id = ?");
        if (!$stmt->execute(array($relationshipId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
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

    public function getAllPdf() {
        return Intrigue_Pdf::getAllPDFsForIntrigue($this);
    }
    
    public function getAllIntrigueRelations() {
        $sql = "SELECT regsys_intrigue.* FROM regsys_intrigue, regsys_intrigue_relationship r1, regsys_intrigue_relationship r2 WHERE ".
            "r1.IntrigueId = ? AND ".
            "r1.RelationshipId = r2.RelationshipId AND ".
            "r1.IntrigueId <> r2.IntrigueId AND ". 
            "r2.IntrigueId = regsys_intrigue.Id ".
            "ORDER BY regsys_intrigue.Number";
        
        return static::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    public static function getAllIntriguesForIntrigueActor(IntrigueActor $intrigueActor) {
        if (!empty($intrigueActor->GroupId)) {
            return static::getAllIntriguesForGroup($intrigueActor->GroupId, $intrigueActor->getIntrigue()->LarpId);
        }
        else {
            return static::getAllIntriguesForRole($intrigueActor->RoleId, $intrigueActor->getIntrigue()->LarpId);
        }
    }

    
    public static function getAllIntriguesForIntrigueProp(Intrigue_Prop $intrigueProp) {
        return static::getAllIntriguesForProp($intrigueProp->PropId, $intrigueProp->getIntrigue()->LarpId);
    }
    
    public static function getAllIntriguesForIntrigueNPCGroup(Intrigue_NPCGroup $intrigueNPCGroup) {
        return static::getAllIntriguesForNPCGroup($intrigueNPCGroup->NPCGroupId, $intrigueNPCGroup->getIntrigue()->LarpId);
    }
    
    public static function getAllIntriguesForIntrigueNPC(Intrigue_NPC $intrigueNPC) {
        return static::getAllIntriguesForNPC($intrigueNPC->NPCId, $intrigueNPC->getIntrigue()->LarpId);
    }
    
    public static function getAllIntriguesForIntrigueLetter(Intrigue_Letter $intrigueLetter) {
        return static::getAllIntriguesForLetter($intrigueLetter->LetterId, $intrigueLetter->getIntrigue()->LarpId);
    }
    
    public static function getAllIntriguesForIntrigueTelegram(Intrigue_Telegram $intrigueTelegram) {
        return static::getAllIntriguesForTelegram($intrigueTelegram->TelegramId, $intrigueTelegram->getIntrigue()->LarpId);
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

    public static function getAllIntriguesForProp($propId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigue_prop WHERE PropId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($propId, $larpId));
    }

    public static function getAllIntriguesForNPCGroup($npcGroupId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigue_npcgroup WHERE NPCGroupId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($npcGroupId, $larpId));
    }
    
    public static function getAllIntriguesForNPC($npcId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigue_npc WHERE NPCId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($npcId, $larpId));
    }

    public static function getAllIntriguesForLetter($letterId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigue_letter WHERE LetterId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($letterId, $larpId));
    }

    public static function getAllIntriguesForTelegram($telegramId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigue_telegram WHERE TelegramId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($telegramId, $larpId));
    }
    
    public function getTimeline() {
        return Timeline::getAllForIntrigue($this);
    }
    
    public function getRumours() {
        return Rumour::getAllForIntrigue($this);
    }
    
}
