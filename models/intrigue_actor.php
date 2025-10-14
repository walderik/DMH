<?php

class IntrigueActor extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $RoleId;
    public $GroupId;
    public $SubdivisionId;
    public $IntrigueText;
    public $OffInfo;
    public $WhatHappened = "";
    
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
        if (isset($arr['SubdivisionId'])) $this->SubdivisionId = $arr['SubdivisionId'];
        if (isset($arr['IntrigueText'])) $this->IntrigueText = $arr['IntrigueText'];
        if (isset($arr['OffInfo'])) $this->OffInfo = $arr['OffInfo'];
        if (isset($arr['WhatHappened'])) $this->WhatHappened = $arr['WhatHappened'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor SET IntrigueId=?, RoleId=?, GroupId=?, SubdivisionId=?, IntrigueText=?, OffInfo=?, WhatHappened=? WHERE Id = ?");
        if (!$stmt->execute(array($this->IntrigueId, $this->RoleId, $this->GroupId, $this->SubdivisionId, $this->IntrigueText, $this->OffInfo, $this->WhatHappened, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor (IntrigueId, RoleId, GroupId, SubdivisionId, IntrigueText, OffInfo, WhatHappened) VALUES (?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->RoleId, $this->GroupId, $this->SubdivisionId, $this->IntrigueText, $this->OffInfo, $this->WhatHappened))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function isRoleActor() {
        if (isset($this->RoleId)) return true;
        return false;
    }
    
    public function isGroupActor() {
        if (isset($this->GroupId)) return true;
        return false;
    }
    
    public function isSubdivisionActor() {
        if (isset($this->SubdivisionId)) return true;
        return false;
    }
    
    public function getRole() {
        if (empty($this->RoleId)) return null;
        return Role::loadById($this->RoleId);
    }
    
    public function getGroup() {
        if (empty($this->GroupId)) return null;
        return Group::loadById($this->GroupId);
    }
    
    public function getSubdivision() {
        if (empty($this->SubdivisionId)) return null;
        return Subdivision::loadById($this->SubdivisionId);
    }
    
    public function isAtLARP() {
        if ($this->isSubdivisionActor()) return true;
        
        $larp = $this->getIntrigue()->getLarp();
        
        $role = $this->getRole();
        if (isset($role)) {
            if ($role->isPC()) return LARP_Role::isRegistered($role->Id, $larp->Id);
            $assignment = NPC_assignment::getAssignment($role, $larp);
            return !empty($assignment);
        }
        
        $group = $this->getGroup();
        if (isset($group)) {
            return $group->isRegistered($larp);
        }
        return false;
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
    
    public static function getAllSubdivisionActorsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE SubdivisionId IS NOT NULL AND IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public static function getSubdivisionActorForIntrigue(Intrigue $intrigue, Subdivision $subdivision) {
        $sql = "SELECT * FROM regsys_intrigueactor WHERE SubdivisionId = ? AND IntrigueId = ? ORDER BY Id";
        return static::getOneObjectQuery($sql, array($subdivision->Id, $intrigue->Id));
    }
    
    public function getAllIntrigues() {
        return Intrigue::getAllIntriguesForIntrigueActor($this);
    }
    
    
    public static function delete($id)
    {
        $intrigueActor = static::loadById($id);
        $checkin_letters = $intrigueActor->getAllCheckinLetters();
        foreach ($checkin_letters as $checkin_letter) IntrigueActor_CheckinLetter::delete($checkin_letter->Id);

        $checkin_telegrams = $intrigueActor->getAllCheckinTelegrams();
        foreach ($checkin_telegrams as $checkin_telegram) IntrigueActor_CheckinTelegram::delete($checkin_telegram->Id);

        $checkin_props = $intrigueActor->getAllCheckinProps();
        foreach ($checkin_props as $checkin_prop) IntrigueActor_CheckinProp::delete($checkin_prop->Id);
        
        $known_actors = $intrigueActor->getAllKnownActors();
        foreach ($known_actors as $known_actor) IntrigueActor_KnownActor::delete($known_actor->Id);
        
        $known_npcs = $intrigueActor->getAllKnownNPCs();
        foreach ($known_npcs as $known_npc) IntrigueActor_KnownNPC::delete($known_npc->Id);
        
        $known_npcgroups = $intrigueActor->getAllKnownNPCGroups();
        foreach ($known_npcgroups as $known_npcgroup) IntrigueActor_KnownNPCGroup::delete($known_npcgroup->Id);
        
        $known_props = $intrigueActor->getAllKnownProps();
        foreach ($known_props as $known_prop) IntrigueActor_KnownProp::delete($known_prop->Id);
        
        $known_pdfs = $intrigueActor->getAllKnownPdfs();
        foreach ($known_pdfs as $known_pdf) IntrigueActor_KnownPdf::delete($known_pdf->Id);
        
        //Ta bort så att ingen känner till den här aktören
        $known_actors = $intrigueActor->getAllWhoKnowsActor();
        foreach ($known_actors as $known_actor) IntrigueActor_KnownActor::delete($known_actor->Id);
        
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
     
     public function addKnownProps($intrigue_propIds) {
         //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_props = $this->getAllKnownProps();
         foreach ($intrigue_props as $intrigue_prop) {
             $exisitingIds[] = $intrigue_prop->IntriguePropId;
         }
         
         $newPropIds = array_diff($intrigue_propIds,$exisitingIds);
         //Koppla rekvisitan till aktören
         foreach ($newPropIds as $intrigue_propId) {
             $intrigueactor_knownprop = IntrigueActor_KnownProp::newWithDefault();
             $intrigueactor_knownprop->IntrigueActorId = $this->Id;
             $intrigueactor_knownprop->IntriguePropId = $intrigue_propId;
             $intrigueactor_knownprop->create();
         }
     }
     
     public function addKnownActors($intrigue_actorIds) {
         //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_actors = $this->getAllKnownActors();
         foreach ($intrigue_actors as $intrigue_actor) {
             $exisitingIds[] = $intrigue_actor->KnownIntrigueActorId;
         }
         
         $newKnownActorIds = array_diff($intrigue_actorIds,$exisitingIds);
         //Koppla till aktören
         foreach ($newKnownActorIds as $intrigue_actorId) {
             if ($intrigue_actorId != $this->Id) {
                 $intrigueactor_knownactor = IntrigueActor_KnownActor::newWithDefault();
                 $intrigueactor_knownactor->IntrigueActorId = $this->Id;
                 $intrigueactor_knownactor->KnownIntrigueActorId = $intrigue_actorId;
                 $intrigueactor_knownactor->create();
             }
         }
     }
     
     public function addKnownNPCGroups($intrigue_NPCGroupIds) {
         //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_npcgroups = $this->getAllKnownNPCGroups();
         foreach ($intrigue_npcgroups as $intrigue_npcgroup) {
             $exisitingIds[] = $intrigue_npcgroup->IntrigueNPCGroupId;
         }
         
         $newKnownNPCGroupIds = array_diff($intrigue_NPCGroupIds,$exisitingIds);
         //Koppla till aktören
         foreach ($newKnownNPCGroupIds as $newKnownNPCGroupId) {
             $intrigueactor_knownnpcgroup = IntrigueActor_KnownNPCGroup::newWithDefault();
             $intrigueactor_knownnpcgroup->IntrigueActorId = $this->Id;
             $intrigueactor_knownnpcgroup->IntrigueNPCGroupId = $newKnownNPCGroupId;
             $intrigueactor_knownnpcgroup->create();            
         }
     }
     
     public function addKnownNPCs($intrigue_NPCIds) {
         //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_npcs = $this->getAllKnownNPCs();
         foreach ($intrigue_npcs as $intrigue_npc) {
             $exisitingIds[] = $intrigue_npc->IntrigueNPCId;
         }
         
         $newKnownNPCIds = array_diff($intrigue_NPCIds,$exisitingIds);
         //Koppla till aktören
         foreach ($newKnownNPCIds as $intrigue_NPCId) {
             $intrigueactor_knownnpc = IntrigueActor_KnownNPC::newWithDefault();
             $intrigueactor_knownnpc->IntrigueActorId = $this->Id;
             $intrigueactor_knownnpc->IntrigueNPCId = $intrigue_NPCId;
             $intrigueactor_knownnpc->create();
         }
     }
     
     public function addKnownPdf($intrigue_pdfIds) {
         //Ta reda på vilka som inte redan är kopplade till aktören
         $exisitingIds = array();
         $intrigue_pdfs = $this->getAllKnownPdfs();
         foreach ($intrigue_pdfs as $intrigue_pdf) {
             $exisitingIds[] = $intrigue_pdf->IntriguePdfId;
         }
         
         $newPdfIds = array_diff($intrigue_pdfIds,$exisitingIds);
         //Koppla rekvisitan till aktören
         foreach ($newPdfIds as $intrigue_pdfId) {
             $intrigueactor_knownpdf = IntrigueActor_KnownPdf::newWithDefault();
             $intrigueactor_knownpdf->IntrigueActorId = $this->Id;
             $intrigueactor_knownpdf->IntriguePdfId = $intrigue_pdfId;
             $intrigueactor_knownpdf->create();
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

     public function getAllPropsThatAreKnown() {
         return Prop::getAllKnownPropsForIntrigueActor($this);
     }
     
     public function getAllLettersForCheckin() {
         return Letter::getAllCheckinLettersForIntrigueActor($this);
     }
     
     public function getAllTelegramsForCheckin() {
         return Telegram::getAllCheckinTelegramsForIntrigueActor($this);
     }
     
     public function getAllPdfsThatAreKnown() {
         return Intrigue_Pdf::getAllPDFsForIntrigueActor($this);
     }
     
     
     public function getAllKnownProps() {
         return IntrigueActor_KnownProp::getAllKnowninPropsForIntrigueActor($this);
     }

     public function getAllKnownActors() {
         return IntrigueActor_KnownActor::getAllKnownIntrigueActorsForIntrigueActor($this);
     }
     
     public function getAllWhoKnowsActor() {
         return IntrigueActor_KnownActor::getAllWhoKnowsIntrigueActor($this);
     }
     
     public function getAllKnownNPCGroups() {
         return IntrigueActor_KnownNPCGroup::getAllKnownNPCGroupsForIntrigueActor($this);
     }
     
     public function getAllKnownNPCs() {
         return IntrigueActor_KnownNPC::getAllKnownNPCsForIntrigueActor($this);
     }
     
     public function getAllKnownPdfs() {
         return IntrigueActor_KnownPdf::getAllKnowninPdfsForIntrigueActor($this);
     }
     
     public function removePropCheckin($propId) {
         $checkin_prop=IntrigueActor_CheckinProp::loadByIds($propId, $this->Id);
         IntrigueActor_CheckinProp::delete($checkin_prop->Id);
     }

     public function removeKnownProp($propId) {
         $known_prop=IntrigueActor_KnownProp::loadByIds($propId, $this->Id);
         IntrigueActor_KnownProp::delete($known_prop->Id);
     }
     
     public function removeKnownRole($roleId) {
         $known_actor=IntrigueActor_KnownActor::loadByIds($roleId, $this->Id, true);
         IntrigueActor_KnownActor::delete($known_actor->Id);
     }
     
     public function removeKnownNPCGroup($npcgroupId) {
         $known_npcgroup=IntrigueActor_KnownNPCGroup::loadByIds($npcgroupId, $this->Id);
         IntrigueActor_KnownNPCGroup::delete($known_npcgroup->Id);
     }
     
     public function removeKnownNPC($npcId) {
         $known_npc=IntrigueActor_KnownNPC::loadByIds($npcId, $this->Id);
         IntrigueActor_KnownNPC::delete($known_npc->Id);
     }
     
     public function removeKnownGroup($groupId) {
         $known_actor=IntrigueActor_KnownActor::loadByIds($groupId, $this->Id, false);
         IntrigueActor_KnownActor::delete($known_actor->Id);
     }
     
     public function removeLetter($letterId) {
         $checkin_letter=IntrigueActor_CheckinLetter::loadByIds($letterId, $this->Id);
         IntrigueActor_CheckinLetter::delete($checkin_letter->Id);
     }

     public function removeTelegram($telegramId) {
         $checkin_telegram=IntrigueActor_CheckinTelegram::loadByIds($telegramId, $this->Id);
         IntrigueActor_CheckinTelegram::delete($checkin_telegram->Id);
     }

     public function removeKnownPdf($intriguePdfId) {
         $known_pdf=IntrigueActor_KnownPdf::loadByIds($intriguePdfId, $this->Id);
         IntrigueActor_KnownPdf::delete($known_pdf->Id);
     }
     
     # Tidigare lajvs intrigaktörer för rollen eller gruppen.
     public function getPrevious() {
         if ($this->isRoleActor()) {
             $sql = "SELECT * FROM regsys_intrigueactor WHERE Id IN (".
                 "SELECT old_actor.Id FROM regsys_intrigueactor as old_actor, regsys_intrigue as new_intrigue WHERE ".
                 "new_intrigue.Id = ? AND ".
                 "new_intrigue.PreviousInstanceId = old_actor.IntrigueId AND ".
                 "old_actor.RoleId = ?)";
             return static::getOneObjectQuery($sql, array($this->IntrigueId, $this->RoleId));
         } elseif ($this->isGroupActor()) {
             $sql = "SELECT * FROM regsys_intrigueactor WHERE Id IN (".
                 "SELECT old_actor.Id FROM regsys_intrigueactor as old_actor, regsys_intrigue as new_intrigue WHERE ".
                 "new_intrigue.Id = ? AND ".
                 "new_intrigue.PreviousInstanceId = old_actor.IntrigueId AND ".
                 "old_actor.GroupId = ?)";
             
             return static::getOneObjectQuery($sql, array($this->IntrigueId, $this->GroupId));         
         } elseif ($this->isSubdivisionActor()) {
             $sql = "SELECT * FROM regsys_intrigueactor WHERE Id IN (".
                 "SELECT old_actor.Id FROM regsys_intrigueactor as old_actor, regsys_intrigue as new_intrigue WHERE ".
                 "new_intrigue.Id = ? AND ".
                 "new_intrigue.PreviousInstanceId = old_actor.IntrigueId AND ".
                 "old_actor.SubdivisionId = ?)";
             
             return static::getOneObjectQuery($sql, array($this->IntrigueId, $this->SubdivisionId));
         }
         return null;

     }
     
     
}
