<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require $root . '/includes/init.php';

/*
$bresta = NPCGroup::loadById(11);
convertNPCGroup($bresta);

$freja = NPC::loadById(36);
convertNPC($freja);
*/
$npcGroups = NPCGroup::all();
foreach ($npcGroups as $npcGroup) {
    convertNPCGroup($npcGroup);
}

$npcs = NPC::all();
foreach ($npcs as $npc) {
    //if ($npc->Id == 70 || $npc->Id == 109 || $npc->Id==89) continue;
    convertNPC($npc);
}


echo "Done";
exit;




function convertNPCGroup(NPCGroup $npcGroup) {
    if (empty($npcGroup->GroupId)) {
        echo "Konverterar gruppen $npcGroup->Name<br>";
        $group = new Group();
        
        $larp = Larp::loadById($npcGroup->LarpId);
        
        $group->CampaignId = $larp->CampaignId;
        
        $group->Name = $npcGroup->Name;
        $group->Description = $npcGroup->Description;
        $group->IsApproved = 1;
        $group->Visibility = Group::VISIBILITY_INVISIBLE;
        $group->create();
        echo "Skapar grupp $group->Id<br>";
        $npcGroup->GroupId = $group->Id;
        $npcGroup->update();
        
        
        $intrigueNpcGroups = Intrigue_NPCGroup::getAllForNPCGroup($npcGroup);
        foreach ($intrigueNpcGroups as $intrigueNpcGroup) {
            $intrigueActor = IntrigueActor::newWithDefault();
            $intrigueActor->IntrigueId = $intrigueNpcGroup->IntrigueId;
            $intrigueActor->GroupId = $group->Id;
            $intrigueActor->create();
            echo "Med i intrig id $intrigueActor->IntrigueId<br>";
            
            //Går det här åt rätt håll, eller fel håll, vi ska hitta alla som känner till NPCGruppen
            $knownNPCGroups = IntrigueActor_KnownNPCGroup::getAllKnownNPCGroupsForIntrigueNPCGroup($intrigueNpcGroup);
            foreach ($knownNPCGroups as $knownNPCGroup) {
                $knowIntrigueActor = IntrigueActor_KnownActor::newWithDefault();
                $knowIntrigueActor->IntrigueActorId = $knownNPCGroup->IntrigueActorId;
                $knowIntrigueActor->KnownIntrigueActorId = $intrigueActor->Id;
                $knowIntrigueActor->create();
                echo "Känd av $knowIntrigueActor->IntrigueActorId<br>";
            }
        }
        echo "<br><br>";
    }
    
}

function convertNPC(NPC $npc) {
    global $current_person;
    if (empty($npc->RoleId)) {
        echo "Konverterar NPC $npc->Name (id $npc->Id)<br>";
        $role = new Role();
        echo "Lajv $npc->LarpId<br>";
        $larp = Larp::loadById($npc->LarpId);
        $role->CampaignId = $larp->CampaignId;
        echo "Kampanj $role->CampaignId<br>";
        $role->Name = $npc->Name;
        if (empty($npc->Description)) $npc->Description = "";
        $role->Description = $npc->Description;
        $role->IsApproved = 1;
        $role->UserMayEdit = 0;
        $role->CreatorPersonId = $current_person->Id;
        $role->Profession = substr($npc->Description, 0, 40);
        echo "Yrke: $role->Profession<br>";
        if (isset($npc->NPCGroupId)) {
            $npcGroup = NPCGroup::loadById($npc->NPCGroupId);
            $role->GroupId = $npcGroup->GroupId;
         }
        
        $role->create();
        echo "Skapar karaktär $role->Id<br>";
        
        if ($npc->IsToBePlayed) {
            $assignment = NPC_assignment::newWithDefault();
            $assignment->Instructions = $npc->Description;
            $assignment->IsReleased = $npc->IsReleased;
            $assignment->LarpId = $npc->LarpId;
            $assignment->PersonId = $npc->PersonId;
            $assignment->Time = $npc->Time;
            $assignment->RoleId = $role->Id;
            $assignment->create();
        }
        $npc->RoleId = $role->Id;
        $npc->update();
        
        $intrigueNpcs = Intrigue_NPC::getAllForNPC($npc);
        foreach ($intrigueNpcs as $intrigueNpc) {
            $intrigueActor = IntrigueActor::newWithDefault();
            $intrigueActor->IntrigueId = $intrigueNpc->IntrigueId;
            $intrigueActor->RoleId = $role->Id;
            $intrigueActor->create();
            echo "Med i intrig id $intrigueActor->IntrigueId<br>";
            
            //Går det här åt rätt håll, eller fel håll
            $knownNPCs = IntrigueActor_KnownNPC::getAllKnownNPCsForIntrigueNPC($intrigueNpc);
            foreach ($knownNPCs as $knownNPC) {
                $knowIntrigueActor = IntrigueActor_KnownActor::newWithDefault();
                $knowIntrigueActor->IntrigueActorId = $knownNPC->IntrigueActorId;
                $knowIntrigueActor->KnownIntrigueActorId = $intrigueActor->Id;
                $knowIntrigueActor->create();
                echo "Känd av $knowIntrigueActor->IntrigueActorId<br>";
            }
        }
        echo "<br><br>";
    }
    
}
