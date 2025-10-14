<?php

class Intrigue extends BaseModel{
    
    public $Id;
    public $Number;
    public $Name;
    public $Status = 0;
    public $Active = 1;
    public $MainIntrigue = 0;
    public $CommonText;
    public $Notes;
    public $LarpId;
    public $ResponsiblePersonId;
    public $PreviousInstanceId;
    
    public static $orderListBy = 'Number';
    
    
    
    const STATUS_TYPES = [
        0 => "Ny",
        10 => "Påbörjad",
        20 => "I behov av fler aktörer",
        30 => "Utveckla texterna",
        40 => "Hjälp mig",
        50 => "Kolla med aktör",
        60 => "Klar för granskning",
        100 => "Färdig"
    ];
    
    public static function newFromArray($post){
        $intrigue = static::newWithDefault();
        $intrigue->setValuesByArray($post);
        return $intrigue;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Number'])) $this->Number = $arr['Number'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Status'])) $this->Status = $arr['Status'];
        if (isset($arr['Active'])) $this->Active = $arr['Active'];
        if (isset($arr['MainIntrigue'])) $this->MainIntrigue = $arr['MainIntrigue'];
        if (isset($arr['CommonText'])) $this->CommonText = $arr['CommonText'];
        if (isset($arr['Notes'])) $this->Notes = $arr['Notes'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['ResponsiblePersonId'])) $this->ResponsiblePersonId = $arr['ResponsiblePersonId'];
        if (isset($arr['PreviousInstanceId'])) $this->PreviousInstanceId = $arr['PreviousInstanceId'];
        
        if (isset($this->ResponsiblePersonId) && $this->ResponsiblePersonId=='null') $this->ResponsiblePersonId = null;
        
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
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue SET Number=?, Name=?, Status=?, Active=?, MainIntrigue=?, CommonText=?, Notes=?, LarpId=?, ResponsiblePersonId=?, PreviousInstanceId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Number, $this->Name, $this->Status, $this->Active, $this->MainIntrigue, $this->CommonText, $this->Notes, $this->LarpId, $this->ResponsiblePersonId, $this->PreviousInstanceId, $this->Id))) {
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
        
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue (Number, Name, Status, Active, MainIntrigue, CommonText, Notes, LarpId, ResponsiblePersonId, PreviousInstanceId) VALUES (?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Number, $this->Name, $this->Status, $this->Active, $this->MainIntrigue, $this->CommonText, $this->Notes, $this->LarpId, $this->ResponsiblePersonId, $this->PreviousInstanceId))) {
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
        $sql = "SELECT * FROM regsys_intrigue WHERE LarpId = ? ORDER BY MainIntrigue DESC,".static::$orderListBy.";";
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
    
    
    public function getResponsiblePerson() {
        return Person::loadById($this->ResponsiblePersonId);
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
    
    public static function continueIntrigues($intrigueIds, LARP $larp, Person $person) {
        foreach($intrigueIds as $intrigueId) {
            $previousIntrigue = Intrigue::loadById($intrigueId);
            $newIntrigue = Intrigue::newWithDefault();
            
            //Kopiera intrighuvudet
            $newIntrigue->Name = $previousIntrigue->Name;
            $newIntrigue->MainIntrigue = $previousIntrigue->MainIntrigue;
            $newIntrigue->CommonText = $previousIntrigue->CommonText;
            $newIntrigue->Notes = $previousIntrigue->Notes;
            $newIntrigue->LarpId = $larp->Id;
            $newIntrigue->ResponsiblePersonId = $person->Id;
            $newIntrigue->PreviousInstanceId = $previousIntrigue->Id;
            $newIntrigue->create();
            
            //Kopiera intrigtyper
            $intrigueTypeIds = $previousIntrigue->getSelectedIntrigueTypeIds();
            $newIntrigue->saveAllIntrigueTypes($intrigueTypeIds);
            
            //Koppla alla roller, grupper och grupperingar
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
            
            $subdivisionactors = $previousIntrigue->getAllSubdivisionActors();
            foreach($subdivisionactors as $subdivisionactor) {
                $newactor = IntrigueActor::newWithDefault();
                $newactor->IntrigueId = $newIntrigue->Id;
                $newactor->SubdivisionId = $subdivisionactor->SubdivisionId;
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
            
            
            //Kopiera brev
            if ($larp->hasLetters()) {
                $intrigueLetters = $previousIntrigue->getAllLetters();
                foreach($intrigueLetters as $intrigueLetter) {
                    $oldLetter = $intrigueLetter->getLetter();
                    $newLetter = Letter::newWithDefault();
                    
                    $newLetter->LARPid = $newIntrigue->LarpId;
                    $newLetter->WhenWhere = $oldLetter->WhenWhere;
                    $newLetter->Signature = $oldLetter->Signature;
                    $newLetter->Recipient = $oldLetter->Recipient;
                    $newLetter->Message = $oldLetter->Message;
                    $newLetter->Greeting = $oldLetter->Greeting;
                    $newLetter->Font = $oldLetter->Font;
                    $newLetter->EndingPhrase = $oldLetter->EndingPhrase;
                    $newLetter->OrganizerNotes = $oldLetter->OrganizerNotes;
                    $newLetter->PersonId = $oldLetter->PersonId;
                    $newLetter->Approved = $oldLetter->Approved;
                    $newLetter->create();
                    
                    $newIntrigueLetter = Intrigue_Letter::newWithDefault();
                    $newIntrigueLetter->IntrigueId = $newIntrigue->Id;
                    $newIntrigueLetter->LetterId = $newLetter->Id;
                    $newIntrigueLetter->create();
                }
            }
            
            //Kopiera telegram
            if ($larp->hasTelegrams()) {
                $intrigueTelegrams = $previousIntrigue->getAllTelegrams();
                foreach($intrigueTelegrams as $intrigueTelegram) {
                    $oldTelegram = $intrigueTelegram->getTelegram();
                    $newTelegram = Telegram::newWithDefault();
                    
                    $newTelegram->LARPid = $newIntrigue->LarpId;
                    $newTelegram->Deliverytime = $oldTelegram->Deliverytime;
                    $newTelegram->Message = $oldTelegram->Message;
                    $newTelegram->Reciever = $oldTelegram->Reciever;
                    $newTelegram->RecieverCity = $oldTelegram->RecieverCity;
                    $newTelegram->Sender = $oldTelegram->Sender;
                    $newTelegram->SenderCity = $oldTelegram->SenderCity;
                    $newTelegram->OrganizerNotes = $oldTelegram->OrganizerNotes;
                    $newTelegram->PersonId = $oldTelegram->PersonId;
                    $newTelegram->Approved = $oldTelegram->Approved;
                    $newTelegram->create();
                    
                    $newIntrigueTelegram = Intrigue_Telegram::newWithDefault();
                    $newIntrigueTelegram->IntrigueId = $newIntrigue->Id;
                    $newIntrigueTelegram->TelegramId = $newTelegram->Id;
                    $newIntrigueTelegram->create();
                }
            }
            
            
            //Kopiera rykten
            if ($larp->hasRumours()) {
                $rumours = $previousIntrigue->getRumours();
                foreach($rumours as $rumour) {
                    $newRumour = Rumour::newWithDefault();
                    $newRumour->IntrigueId = $newIntrigue->Id;
                    $newRumour->LARPid = $newIntrigue->LarpId;
                    $newRumour->Text = $rumour->Text;
                    $newRumour->Notes = $rumour->Notes;
                    $newRumour->PersonId = $rumour->PersonId;
                    $newRumour->Approved = $rumour->Approved;
                    $newRumour->create();
                }
            }
            
            //Kopiera syner
            if ($larp->HasVisions()) {
                $visions = $previousIntrigue->getVisions();
                foreach($visions as $vision) {
                    $newVision = Vision::newWithDefault();
                    
                    $newVision->LARPid = $newIntrigue->LarpId;
                    $newVision->OrganizerNotes = $vision->OrganizerNotes;
                    $newVision->SideEffect = $vision->SideEffect;
                    $newVision->Source = $vision->Source;
                    $newVision->VisionText = $vision->VisionText;
                    $newVision->WhenDate = NULL;
                    $newVision->WhenSpec = NULL;
                    $newVision->create();
                    
                    $newIntrigueVision = Intrigue_Vision::newWithDefault();
                    $newIntrigueVision->IntrigueId = $newIntrigue->Id;
                    $newIntrigueVision->VisionId = $newVision->Id;
                    $newIntrigueVision->create();
                }
            }
            
            
            //Kopiera npc'er
//             $npcs = $previousIntrigue->getAllNPCs();
//             foreach($npcs as $npc) {
//                 $newInrigue_NPC = Intrigue_NPC::newWithDefault();
//                 $newInrigue_NPC->IntrigueId = $newIntrigue->Id;
                
//                 $oldNpc = $npc->getNPC();
                
//                 $newNpc = NPC::newWithDefault();
                
//                 if ($oldNpc->hasImage()) {
//                     $oldImage = $oldNpc->getImage();
//                     $newImage = Image::newWithDefault();
//                     $newImage->file_data = $oldImage->file_data;
//                     $newImage->file_mime = $oldImage->file_mime;
//                     $newImage->file_name = $oldImage->file_name;
//                     $newImage->Photographer = $oldImage->Photographer;
//                     $newImage->create();
                    
//                     $newNpc->ImageId = $newImage->Id;
//                 }
//                 $newNpc->LarpId = $newIntrigue->LarpId;
//                 $newNpc->Name = $oldNpc->Name;
//                 $newNpc->Description = $oldNpc->Description;
//                 $newNpc->IsToBePlayed = $oldNpc->IsToBePlayed;
//                 $newNpc->IsReleased = false;
//                 $newNpc->create();
                
//                 $newInrigue_NPC->NPCId = $newNpc->Id;
//                 $newInrigue_NPC->create();
//             }
            
            
            
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
    
    public function isMainIntrigue() {
        if ($this->MainIntrigue == 0) return false;
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
    
    public function addSubdivisionActors($subdivisionIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingSubdivisionIds = array();
        $intrigue_actors = $this->getAllSubdivisionActors();
        foreach ($intrigue_actors as $intrigue_actor) {
            $exisitingSubdivisionIds[] = $intrigue_actor->SubdivisionId;
        }
        
        $newSubdivisionIds = array_diff($subdivisionIds,$exisitingSubdivisionIds);
        //Koppla karaktären till intrigen
        foreach ($newSubdivisionIds as $subdivisionId) {
            $intrigue_actor = IntrigueActor::newWithDefault();
            $intrigue_actor->IntrigueId = $this->Id;
            $intrigue_actor->SubdivisionId = $subdivisionId;
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
    
//     public function addNPCs($NPCIds) {
//         //Ta reda på vilka som inte redan är kopplade till intrigen
//         $exisitingIds = array();
//         $intrigue_npcs = $this->getAllNPCs();
//         foreach ($intrigue_npcs as $intrigue_npc) {
//             $exisitingIds[] = $intrigue_npc->NPCId;
//         }
        
//         $newNPCIds = array_diff($NPCIds,$exisitingIds);
//         //Koppla rekvisitan till intrigen
//         foreach ($newNPCIds as $NPCId) {
//             $intrigue_npc = Intrigue_NPC::newWithDefault();
//             $intrigue_npc->IntrigueId = $this->Id;
//             $intrigue_npc->NPCId = $NPCId;
//             $intrigue_npc->create();
//         }
//     }
    
//     public function addNPCGroups($NPCGroupIds) {
//         //Ta reda på vilka som inte redan är kopplade till intrigen
//         $exisitingIds = array();
//         $intrigue_npcgroups = $this->getAllNPCGroups();
//         foreach ($intrigue_npcgroups as $intrigue_npcgroup) {
//             $exisitingIds[] = $intrigue_npcgroup->NPCGroupId;
//         }
        
//         $newNPCGroupIds = array_diff($NPCGroupIds,$exisitingIds);
//         //Koppla rekvisitan till intrigen
//         foreach ($newNPCGroupIds as $NPCGroupId) {
//             $intrigue_npcgroup = Intrigue_NPCGroup::newWithDefault();
//             $intrigue_npcgroup->IntrigueId = $this->Id;
//             $intrigue_npcgroup->NPCGroupId = $NPCGroupId;
//             $intrigue_npcgroup->create();
//         }
//     }
    
    
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
    
    public function addVisions($visionIds) {
        //Ta reda på vilka som inte redan är kopplade till intrigen
        $exisitingIds = array();
        $visions = $this->getVisions();
        foreach ($visions as $vision) {
            $exisitingIds[] = $vision->Id;
        }
        
        $newVisionIds = array_diff($visionIds,$exisitingIds);
        //Koppla rekvisitan till intrigen
        foreach ($newVisionIds as $visionId) {
            
            $intrigue_vision = Intrigue_Vision::newWithDefault();
            $intrigue_vision->IntrigueId = $this->Id;
            $intrigue_vision->VisionId = $visionId;
            $intrigue_vision->create();
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
    
    public function getAllSubdivisionActors() {
        return IntrigueActor::getAllSubdivisionActorsForIntrigue($this);
    }
    
    public function getAllProps() {
        return Intrigue_Prop::getAllPropsForIntrigue($this);
    }
    
//     public function getAllNPCs() {
//         return Intrigue_NPC::getAllNPCsForIntrigue($this);
//     }
    
//     public function getAllNPCGroups() {
//         return Intrigue_NPCGroup::getAllNPCGroupsForIntrigue($this);
//     }
    
    public function getAllLetters() {
        return Intrigue_Letter::getAllLettersForIntrigue($this);
    }
    
    public function getAllTelegrams() {
        return Intrigue_Telegram::getAllTelegramsForIntrigue($this);
    }
    
    public function getAllVisions() {
        return Intrigue_Vision::getAllIntrigueVisionsForIntrigue($this);
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
    
//     public static function getAllIntriguesForIntrigueNPCGroup(Intrigue_NPCGroup $intrigueNPCGroup) {
//         return static::getAllIntriguesForNPCGroup($intrigueNPCGroup->NPCGroupId, $intrigueNPCGroup->getIntrigue()->LarpId);
//     }
    
//     public static function getAllIntriguesForIntrigueNPC(Intrigue_NPC $intrigueNPC) {
//         return static::getAllIntriguesForNPC($intrigueNPC->NPCId, $intrigueNPC->getIntrigue()->LarpId);
//     }
    
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
    
    public static function getAllIntriguesForSubdivision($subdivisionId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigueactor WHERE SubdivisionId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($subdivisionId, $larpId));
    }
    
    public static function getAllIntriguesForProp($propId, $larpId) {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
            "SELECT IntrigueId FROM regsys_intrigue_prop WHERE PropId = ? AND LarpId = ?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($propId, $larpId));
    }
    
//     public static function getAllIntriguesForNPCGroup($npcGroupId, $larpId) {
//         $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
//             "SELECT IntrigueId FROM regsys_intrigue_npcgroup WHERE NPCGroupId = ? AND LarpId = ?) ORDER BY Id";
//         return static::getSeveralObjectsqQuery($sql, array($npcGroupId, $larpId));
//     }
    
//     public static function getAllIntriguesForNPC($npcId, $larpId) {
//         $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (".
//             "SELECT IntrigueId FROM regsys_intrigue_npc WHERE NPCId = ? AND LarpId = ?) ORDER BY Id";
//         return static::getSeveralObjectsqQuery($sql, array($npcId, $larpId));
//     }
    
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
    
    public function getVisions() {
        return Vision::getAllForIntrigue($this);
    }
    
    public function mayDelete() {
        //Kolla om det finns något kopplat till intrigen
        $sql = "SELECT COUNT(*) AS Num FROM regsys_intrigue_telegram WHERE IntrigueId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_intrigue_letter WHERE IntrigueId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_intrigue_pdf WHERE IntrigueId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
//         $sql = "SELECT COUNT(*) AS Num FROM regsys_intrigue_npc WHERE IntrigueId=?";
//         if (static::existsQuery($sql, array($this->Id))) return false;
        
//         $sql = "SELECT COUNT(*) AS Num FROM regsys_intrigue_npcgroup WHERE IntrigueId=?";
//         if (static::existsQuery($sql, array($this->Id))) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_intrigue_prop WHERE IntrigueId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_rumour WHERE IntrigueId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_intrigueactor WHERE IntrigueId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_timeline WHERE IntrigueId=?";
        if (static::existsQuery($sql, array($this->Id))) return false;
        
        if (!empty($this->getAllIntrigueRelations())) return false;
        
        return true;
    }
    
    public static function delete($id)
    {
        $intrigue = static::loadById($id);
        
        if (!$intrigue->mayDelete()) return;
        
        $intrigue->deleteAllIntrigueTypes();
        
        parent::delete($id);
    }
    
    //Sätter resultatet i vissa inparametrar
    public function findAllInfoForRoleInIntrigue($role, $subdivisions, &$commonTextHeader, &$intrigueTextArr, &$offTextArr, &$whatHappenedTextArr, ?bool $adminInfo=false) {
        $intrigueActors = array();
        $roleActor = IntrigueActor::getRoleActorForIntrigue($this, $role);
        if (!empty($roleActor)) $intrigueActors[] = $roleActor;
        $invisibleSubdivisionActors = array();
        $visibleSubdivisionActors = array();
        foreach ($subdivisions as $subdivision) {
            $subdivisionActor = IntrigueActor::getSubdivisionActorForIntrigue($this, $subdivision);
            if (!empty($subdivisionActor)) {
                if ($subdivision->isVisibleToParticipants()) $visibleSubdivisionActors[] = $subdivisionActor;
                else $invisibleSubdivisionActors[] = $subdivisionActor;
            }
        }
        if (!empty($invisibleSubdivisionActors)) $intrigueActors = array_merge($intrigueActors, $invisibleSubdivisionActors);
        if (!empty($visibleSubdivisionActors)) $intrigueActors = array_merge($intrigueActors, $visibleSubdivisionActors);
        
        
        
        //Om det bara är en intrigaktör och det är en synlig gruppering ska även För <namn> skrivas ut
        $singleVisibleSubdivisionActor = false;
        
        $commonTextHeader = "";
        
        if (!empty($this->CommonText)) {
            if (sizeOf($intrigueActors) == 1 && $intrigueActors[0]->isSubdivisionActor()) {
                $subdivision = $intrigueActors[0]->getSubdivision();
                if ($subdivision->isVisibleToParticipants()) {
                    $commonTextHeader = $subdivision->Name;
                    $singleVisibleSubdivisionActor = true;
                }
            }
        }
        
        $intrigueTextArr = array();
        foreach ($intrigueActors as $intrigueActor) {
            if (!empty($intrigueActor->IntrigueText) && !in_array($intrigueActor->IntrigueText, $intrigueTextArr)) {
                
                if ($intrigueActor->isRoleActor()) $intrigueTextArr[] = $intrigueActor->IntrigueText;
                else {
                    $subdivision = $intrigueActor->getSubdivision();
                    if ($subdivision->isVisibleToParticipants() && !$singleVisibleSubdivisionActor) {
                        $intrigueTextArr[] =  array($subdivision->Name, $intrigueActor->IntrigueText, true);
                    } elseif ($adminInfo){
                        //Tredje parameterna anger om den ska vara synlig eller inte
                        $intrigueTextArr[] =  array($subdivision->Name, $intrigueActor->IntrigueText, false);
                    } else {
                        $intrigueTextArr[] = $intrigueActor->IntrigueText;
                    }
                    
                }
            }
        }
        
        $offTextArr = array();
        foreach ($intrigueActors as $intrigueActor) {
            if (!empty($intrigueActor->OffInfo)  && !in_array($intrigueActor->OffInfo, $offTextArr)) {
                $offTextArr[] =  $intrigueActor->OffInfo;
            }
        }
        
        $whatHappenedTextArr = array();
        foreach ($intrigueActors as $intrigueActor) {
            if (!empty($intrigueActor->WhatHappened)) {
                
                if ($intrigueActor->isRoleActor()) $whatHappenedTextArr[] = $intrigueActor->WhatHappened;
                else {
                    $subdivision = $intrigueActor->getSubdivision();
                    if ($subdivision->isVisibleToParticipants()) $whatHappenedTextArr[] =  array($subdivision->Name, $intrigueActor->WhatHappened);
                    elseif ($adminInfo) {
                        //Tredje parameterna anger om den ska vara synlig eller inte
                        $whatHappenedTextArr[] =  array($subdivision->Name, $intrigueActor->IntrigueText, false);
                    } else $whatHappenedTextArr[] = $intrigueActor->WhatHappened;
                    
                }
            }
        }
    }
    
}
