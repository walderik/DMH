<?php
include_once 'header.php';

$persons=Person::getAllInterestedNPC($current_larp);


function print_assigned_npc(NPC $npc, $npc_group) {
    global $current_larp;
    echo "<tr><td width='80'>";
    if ($npc->hasImage()) {
        echo "<img width='30' src='image.php?id=$npc->ImageId'/>\n";
        echo " <a href='logic/delete_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-trash' title='Ta bort bild'></i></a>\n";
    } else {
        echo "<a href='upload_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
    }
    echo "</td><td>";
    
    echo "<div class='npc'>";
    
    $person=$npc->getPerson();
    
    echo "<a href='npc_form.php?operation=update&id=$npc->Id'>$npc->Name</a> \n";
    if (!empty($npc->Time)) echo "$npc->Time";
    echo "<br>";
    if (!empty($npc->Description)) echo nl2br(htmlspecialchars($npc->Description))."<br>\n";
    
    $intrigues = Intrigue::getAllIntriguesForNPC($npc->Id, $current_larp->Id);
    if (!empty($intrigues)) {
        echo "Intrig: ";
        foreach ($intrigues as $intrigue) {
            echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
            if ($intrigue->isActive()) echo $intrigue->Number;
            else echo "<s>$intrigue->Number</s>";
            echo "</a>";
            echo " ";
        }
    echo "<br>";
    }
    
    
    echo "Spelas av $person->Name\n";
    echo "<form action='logic/assign_npc.php' method='post' style='display:inline-block'><input type='hidden' name='id' value='$npc->Id'>\n";
    echo "<input type='hidden' name='PersonId' value='null'>\n";
    echo "<button class='invisible' type='submit'><i class='fa-solid fa-xmark' title='Ta bort från deltagaren'></i></button>";
    echo "</form>\n";
    if (empty($npc_group) || (!$npc->IsReleased())) {
        echo "<form action='logic/release_npc.php' method='post' style='display:inline-block'><input type='hidden' name='id' value='$npc->Id'>\n";
        echo " <button class='invisible' type ='submit'><i class='fa-solid fa-envelope' title=''Skicka NPC:n till deltagaren'></i></button>\n";
        echo "</form>\n";
    }
    echo "</div>";
    echo "</td></tr>";
    
}

function print_unassigned_npc(NPC $npc) {
    global $persons, $current_larp;
    $intrigues = Intrigue::getAllIntriguesForNPC($npc->Id, $current_larp->Id);
    echo "<tr><td width='80'>";
    if ($npc->hasImage()) {
        $image = Image::loadById($npc->ImageId);
        echo "<img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>";
        echo " <a href='logic/delete_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-trash' title='Ta bort bild'></i></a>\n";
    } else {
        echo "<a href='upload_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
    }
    echo "</td><td>";
    echo "<div class='npc'>";
    echo "<a href='npc_form.php?operation=update&id=$npc->Id'>$npc->Name</a> ";
    if (empty($intrigues)) echo "<a href='logic/delete_npc.php?id=$npc->Id'><i class='fa-solid fa-trash'></i></a> ";
    if (!empty($npc->Time)) echo "$npc->Time";
    echo "<br>";
    if (!empty($npc->Description)) echo nl2br(htmlspecialchars($npc->Description))."<br>\n";
    
    if (!empty($intrigues)) echo "Intrig: ";
    foreach ($intrigues as $intrigue) {
        echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
        if ($intrigue->isActive()) echo $intrigue->Number;
        else echo "<s>$intrigue->Number</s>";
        echo "</a>";
        echo " ";
    }
    echo "</td><td>";
    if ($npc->IsToBePlayed()) {
        echo "<form action='logic/assign_npc.php' method='post'><input type='hidden' name='id' value=$npc->Id>";
        echo selectionDropDownByArray("PersonId", $persons);
        echo "<input type ='submit' value='Tilldela'>";
        echo "</form>";
    }
    echo "</td></tr>";
    
    
}

include 'navigation.php';
?>


<style>
div.groupname {
    margin: 0;
    padding-top: 10px;
    padding-bottom: 0px;
    font-size: 1.4em;
    color: #4a536e;
    font-weight: bold;
}

div.npc {
    margin-left: 10px;
    margin-bottom: 10px;
}

</style>
    <div class="content">   
        <h1>NPC</h1>
            <a href="npc_form.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC</a>  
            <a href="npc_group_form.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC grupp</a>  

            <div>
            <h2>Alla tilldelade NPC'er</h2>
            <p>För att deltagaren ska kunna se en NPC som de har fått tilldelad måste man "Skicka NPC" <i class='fa-solid fa-envelope' title=''Skicka NPC'></i> till dem. Då får deltagaren ett mail om det och NPC'n går att se på deltagarens översiktssida. Det går att skicka en hel grupp på en gång.</p>
          <a href="reports/npc_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>PDF med NPC'er som ska spelas</a> 
            <?php 
            
            
            
            
            $npc_groups = NPCGroup::getAllForLARP($current_larp);
            $groups_with_assigned_npcs = array();
            echo "<table>";
            
            foreach ($npc_groups as $npc_group) {
                $npcs=NPC::getAllAssignedByGroup($npc_group, $current_larp);
                if (!empty($npcs)) {
                    $groups_with_assigned_npcs[] = $npc_group;
                    echo "<tr><td colspan='2'>";
                    echo "<form action='logic/release_npc_group.php' method='post'><input type='hidden' name='id' value='$npc_group->Id'>\n";
                    echo "<h3><a href='npc_group_form.php?operation=update&id=$npc_group->Id'>$npc_group->Name</a>";
                    
                    if ($npc_group->IsReleased()) {
                        echo "</h3>";
                        echo "<br>Deltagarna har fått sina npc'er.\n";
                    }
                    else {
                        echo " <button class='invisible' type ='submit'><i class='fa-solid fa-envelope' title=''Skicka NPC:n till deltagarna'></i></button>\n";
                        echo "</h3>";
                    }
                    echo "</form>\n";
                    
                    $intrigues = Intrigue::getAllIntriguesForNPCGroup($npc_group->Id, $current_larp->Id);
                    if (!empty($intrigues)) {
                        echo "Intrig: ";
                        foreach ($intrigues as $intrigue) {
                            echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
                            if ($intrigue->isActive()) echo $intrigue->Number;
                            else echo "<s>$intrigue->Number</s>";
                            echo "</a>";
                            echo " ";
                        }
                    }
                    
                    
                    
                    echo "</td></tr>";
                }
                foreach($npcs as $npc) {
                    print_assigned_npc($npc, $npc_group);
                }
            }
            $npcs=NPC::getAllAssignedWithoutGroup($current_larp);
            if (!empty($npcs)) {
                echo "<tr><td colspan='2'>";
                echo "<h3>Utan grupp</h3>";
                echo "</td></tr>";
            }
            foreach($npcs as $npc) {
                print_assigned_npc($npc, null);
            }
            echo "</table>";
            ?>
            </div>
            
            <div>
            <h2>Alla NPC'er som inte är tilldelade</h2>
            <?php 
            
            $npc_groups = NPCGroup::getAllForLARP($current_larp);
            $groups_without_unassigned_npcs = array();
            echo "<table>";
            foreach ($npc_groups as $npc_group) {
                $npcs=NPC::getAllUnassignedByGroup($npc_group, $current_larp);
                
                if(!empty($npcs)) {
                    echo "<tr><td colspan='2'><h3><a href='npc_group_form.php?operation=update&id=$npc_group->Id'>$npc_group->Name</a>";
                    echo "</h3>";
                    
                    $intrigues = Intrigue::getAllIntriguesForNPCGroup($npc_group->Id, $current_larp->Id);
                    if (!empty($intrigues)) {
                        echo "Intrig: ";
                        foreach ($intrigues as $intrigue) {
                            echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
                            if ($intrigue->isActive()) echo $intrigue->Number;
                            else echo "<s>$intrigue->Number</s>";
                            echo "</a>";
                            echo " ";
                        }
                    }
                    
                    
                    echo "</td></tr>";
                    
    
                    foreach($npcs as $npc) {
                        print_unassigned_npc($npc);
                    }
                }
                else {
                    $groups_without_unassigned_npcs[] = $npc_group;
                }
            }
 
            $npcs=NPC::getAllUnassignedWithoutGroup($current_larp);
            if (!empty($npcs)) {
                echo "<tr><td colspan='2'><h3>Utan grupp</h3></td></tr>";
            }
            foreach($npcs as $npc) {
                print_unassigned_npc($npc);
            }

            echo "</table>";
            $unused_groups = array_udiff($groups_without_unassigned_npcs, $groups_with_assigned_npcs,
            function ($objOne, $objTwo) {
                return $objOne->Id - $objTwo->Id;
            });
            $unused_group_links = array();
            foreach($unused_groups as $npc_group) {
                $intrigues = Intrigue::getAllIntriguesForNPCGroup($npc_group->Id, $current_larp->Id);
                if (empty($intrigues)) {
                    $unused_group_links[] = "<a href='npc_group_form.php?operation=update&id=$npc_group->Id'>$npc_group->Name</a>".
                        " <a href='logic/delete_npc_group.php?id=$npc_group->Id'><i class='fa-solid fa-trash'></i></a>";
                } else {
                    $intrigstr = ", intrig: ";
                    foreach ($intrigues as $intrigue) {
                        $intrigstr = $intrigstr . "<a href='view_intrigue.php?Id=$intrigue->Id'>";
                        if ($intrigue->isActive()) $intrigstr = $intrigstr . $intrigue->Number;
                        else $intrigstr = $intrigstr . "<s>$intrigue->Number</s>";
                        $intrigstr = $intrigstr . "</a>";
                        $intrigstr = $intrigstr . " ";
                    }
                    $unused_group_links[] = "<a href='npc_group_form.php?operation=update&id=$npc_group->Id'>$npc_group->Name</a>". $intrigstr;
                }
                
                
                
                
            }
            
            
            echo "<h3>Grupper utan npc'er<h3>";
            echo implode(", ", $unused_group_links);
            

            
            ?>
            </div>
            
            <div>
            <h2>Personer som vill vara NPC</h2>
            <?php 

            foreach($persons as $person) {
                $registration = $person->getRegistration($current_larp);
            
                echo "<a href='view_person.php?id=$person->Id'><strong>$person->Name</strong></a>, ";

                echo $person->getMainRole($current_larp)->getLarperType()->Name."<br>";
                echo "karaktär(er): ";
                $roles = Role::getRegistredRolesForPerson($person, $current_larp);
                foreach ($roles as $role) {
                    echo "<a href='view_role.php?id=$role->Id'>$role->Name</a> ";
                }
                echo "<br>";
                echo "Önskemål: $registration->NPCDesire<br><br>";
            }
            ?>
            </div>
  	</div>
</body>

</html>
  