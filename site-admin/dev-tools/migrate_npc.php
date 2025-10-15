<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require $root . '/includes/init.php';

$npcGroups = NPCGroup::all();

//TODO ta hänsyn till Intriger

foreach ($npcGroups as $npcGroup) {
    $npcs = $npcGroup->getNPCsInGroup();
    if (!empty($npcs)){
        $group = new Group();
        
        $larp = Larp::loadById($npcGroup->LarpId);
    
        $group->CampaignId = $larp->CampaignId;
        
        $group->Name = $npcGroup->Name;
        $group->Description = $npcGroup->Description;
        $group->IsApproved = 1;
        $group->create();
        
        foreach ($npcs as $npc) {
            $role = new Role();
            $role->CampaignId = $larp->CampaignId;
            $role->Name = $npc->Name;
            $role->Description = $npc->Description;
            $role->IsApproved = 1;
            $role->Profession = substr($npc->Description, 50);
            $role->GroupId = $group->Id;
            $role->create();
            $npc->RoleId = $role->Id;
            $npc->Time = $npcGroup->Time . " " . $npc->Time;
            $npc->update();
        }
    }
}

$npcs = NPC::all();
foreach ($npcs as $npc) {
    if (!isset($npc->NPCGroupId) && !isset($npc->RoleId)) {
        $role = new Role();
        $role->CampaignId = $larp->CampaignId;
        $role->Name = $npc->Name;
        $role->Description = $npc->Description;
        $role->IsApproved = 1;
        $role->Profession = substr($npc->Description, 50);
        $role->create();
        
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
        
        $intrigueNpcs = Intrigue_NPC::getAllForNPC($npc->Id);
        foreach ($intrigueNpcs as $intrigueNpc) {
            $intrigueActor = IntrigueActor::newWithDefault();
            $intrigueActor->IntrigueId = $intrigueNpc->IntrigueId;
            $intrigueActor->RoleId = $role->Id;
            $intrigueActor->create();
            
            $knownNPCs = IntrigueActor_KnownNPC::getAllKnownNPCsForIntrigueNPC($intrigueNpc);
            foreach ($knownNPCs as $knownNPC) {
                $knowIntrigueActor = IntrigueActor_KnownActor::newWithDefault();
                $knowIntrigueActor->IntrigueActorId = $knownNPCs->IntrigueActorId;
                $knowIntrigueActor->KnownIntrigueActorId = $intrigueActor->Id;
                $knowIntrigueActor->create();
            }
        }

    }
}


echo "Done";
exit;


