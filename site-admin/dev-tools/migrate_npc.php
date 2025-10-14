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
    if (!isset($npc->RoleId)) {
        $role = new Role();
        $role->CampaignId = $larp->CampaignId;
        $role->Name = $npc->Name;
        $role->Description = $npc->Description;
        $role->IsApproved = 1;
        $role->Profession = substr($npc->Description, 50);
        $role->create();
        $npc->RoleId = $role->Id;
        $npc->update();
        
        //TODO rätta upp intriger
    }
}


echo "Done";
exit;


