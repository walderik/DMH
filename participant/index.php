<?php
require 'header.php';
include_once '../includes/error_handling.php';

?>

<?php include "navigation.php" ?>

		<div class="content">
			<h1>Anmälan till <?php echo $current_larp->Name;?></h1>
        	  <?php if (isset($error_message) && strlen($error_message)>0) {
        	      echo '<div class="error">'.$error_message.'</div>';
        	  }?>
        	  <?php if (isset($message_message) && strlen($message_message)>0) {
        	      echo '<div class="message">'.$message_message.'</div>';
        	  }?>
            
            <?php 
            if ($current_larp->isFull()) {

                echo "<div><b style='color: red'>Lajvet är fullt</b>";
                echo "</div>";
            }
            elseif ($current_larp->isPastLatestRegistrationDate()) {

                echo "<div><b style='color: red'>Sista anmälningsdag har passerat</b>";
                echo "</div>";
            }
            elseif ($current_larp->RegistrationOpen == 0) {

                echo "<div><b style='color: red'>Anmälan inte öppen</b>";
                echo "<br><br>Du kan registrera deltagare, grupper och roller i väntan på att anmälan ska öppna. <br><br>"; 
                echo "OBS! En roll kan bara bli medlem i en grupp om den är anmäld. Så det får du editera efter att anmälan har öppnat. Men övrig information kan du fylla i så länge.";
                echo "</div>";
            }
            ?>
			

			<div>
			Så här använder du anmälningssystemet:
				<ol>
			 	<li>Börja med att <a href="person_form.php">registrera en deltagare.</a></li>
			 	<li>Om du är gruppansvarig, <a href="group_form.php">registrera en grupp</a> och <a href="group_registration_form.php">anmäl den till lajvet</a>.</li>
			 	<li><a href="role_form.php">Registrera karaktärer</a>, gärna flera.</li>
			 	<li><a href="select_person.php">Anmäl deltagaren</a> till lajvet.</li>
			 	</ol>
			 	Det går att hantera flera deltagare från ett konto, tex om ni är en familj.<br><br>
			 	Till nästa lajv kommer alla registrerade deltagare, grupper och karaktärer att finnas kvar. Så då kan du bara kontrollera att allt ser rätt och sedan skicka in anmälan.
			 	<br><br>
			 	<strong>Symboler</strong><br>
			 	<?php echo showStatusIcon(false); ?> - Något behöver göras<br>
			 	<?php echo showStatusIcon(true); ?> - Allt klart på den här punkten<br>
			 	<i class='fa-solid fa-eye'></i> - Visa<br>
			 	<i class='fa-solid fa-pen'></i> - Ändra (går bara att göra om man inte är anmäld)<br>
			 	<i class='fa-solid fa-trash'></i> - Ta bort (går bara att göra om man inte är anmäld)<br>
			 	<i class='fa-solid fa-image-portrait'></i> - Ladda upp bild (går bara efter anmälan)<br>
			 	<i class='fa-solid fa-skull-crossbones'></i> - Död
			 	
			 	
			 	
			 </div>
		</div>
		<div class="content">
    		<h2>Registreringar /anmälningar</h2>
    		<div>
    		<?php 
    		
    		//Personer
    		$persons = $current_user->getPersons();
    		if (empty($persons)) {
    		    echo "<a href='person_form.php'>Registrera en deltagare.</a>";
    		} else {
    		    foreach ($persons as $person)  {
    		        $roles = $person->getRoles();
    		        $groups = $person->getGroups();
    		        
    		        echo "<div class='person'>\n";
    		        
    		        if ($person->isRegistered($current_larp)) {
    		            echo "<a href='view_person.php?id=" . $person->Id . "'><h3>$person->Name&nbsp;<i class='fa-solid fa-eye'></i></a></h3>\n";    		            
    		        }
    		        else {
    		            echo "<a href='person_form.php?operation=update&id=" . $person->Id . "'><h3>$person->Name&nbsp;<i class='fa-solid fa-pen'></i></a>";
    		            if($person->isNeverRegistered() && (!isset($roles) or count($roles) == 0) && (!isset($groups) or count($groups) == 0)) {
    		                echo "&nbsp;<a href='logic/delete_person.php?id=" . $person->Id . "'><i class='fa-solid fa-trash' title='Ta bort deltagare'></i></a>";
    		            }
    		            echo "</h3>\n";
    		        }
    		        echo "Epost: " . $person->Email. "<br>\n";
    		        echo "Mobilnummer: " . $person->PhoneNumber. "<br>\n";
    		        echo "<table  class='checks'>";
    		        if (isset($roles) && count($roles) > 0) {
                        echo "<tr><td>Anmäld</td><td>" . showStatusIcon($person->isRegistered($current_larp), "person_registration_form.php?PersonId=$person->Id"). "</td></tr>\n";
    		        }
                    if ($person->isRegistered($current_larp)) {
                        $registration = Registration::loadByIds($person->Id, $current_larp->Id);
                        if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian)  {
                            echo "<tr><td>Ansvarig vuxen</td><td>";
                            if (empty($registration->GuardianId)) {
                                echo showStatusIcon(false);
                                echo "</td><td><a href='input_guardian.php?PersonId=$person->Id'>Ange ansvarig vuxen</a>";
                            }
                            else {
                                echo showStatusIcon(true);
                                
                                
                                echo "</td><td>".$registration->getGuardian()->Name;
                            }
                            echo "</td></tr>\n";                            
                        }
                        echo "<tr><td>Godkänd</td><td>" . showStatusIcon($person->isApproved($current_larp)). "</td></tr>\n";
                        echo "<tr><td>Betalat</td><td>" . showStatusIcon($person->hasPayed($current_larp));
                        if (!$person->hasPayed($current_larp)) {

                            $campaign = $current_larp->getCampaign();
                            echo "</td><td>Betala <b>$registration->AmountToPay</b> SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>";
                        }
                        echo "</td></tr>\n";
 
                        
                        echo "<tr><td>Medlem</td><td>".showStatusIcon($person->isMember($current_larp), "https://ebas.sverok.se/signups/index/5915")."</a>";
                        if (!$person->isMember($current_larp)) {
                            echo "</td><td><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>Betala medlemsavgiften</a>";
                            echo "</td></tr>\n";
                        }
                    }
                    echo "</table>";
    		        
                    
                    //Grupper
    		        if (isset($groups) && count($groups) > 0) {
    		            echo "<br><b>Gruppansvarig för:</b><br>\n";
    		        }
    		        foreach ($groups as $group)  {
    		            if ($group->isRegistered($current_larp)) {
    		                echo  "<a href='view_group.php?id=$group->Id'>$group->Name <i class='fa-solid fa-eye'></i></a>";
    		                if ($group->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
        		                
    		            }
    		            else {
    		                echo "<a href='group_form.php?operation=update&id=$group->Id'>$group->Name <i class='fa-solid fa-pen'></i></a>"; 
    		                if ($group->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
    		                
    		                 if($group->isNeverRegistered()) {
    		                     echo "&nbsp;<a href='logic/delete_group.php?id=" . $group->Id . "'><i class='fa-solid fa-trash' title='Ta bort grupp'></i></a>";
    		                 }
    		                 
    		            }
    		            echo " Anmäld&nbsp;&nbsp;" . showStatusIcon($group->isRegistered($current_larp), "group_registration_form.php?new_group=$group->Id") . "<br>\n";
    		        }
    		       
    		        
    		        //Karaktärer
    		        if (isset($roles) && count($roles) > 0) {
    		            echo "<br><b>Karaktärer:</b><br>\n";
    		            echo "<table class='roles'>\n";   		            
        		        foreach ($roles as $role)  {
        		            $role_group = $role->getGroup();
        		            $role_group_name = " (Inte med i någon grupp)";
        		            if (isset($role_group) && $role->isRegistered($current_larp)) {
        		                $role_group_name = " - <a href='view_group.php?id=$role_group->Id'>$role_group->Name <i class='fa-solid fa-eye'></i></a>";
        		            }
        		            elseif (isset($role_group)) {
        		                $role_group_name = " - $role_group->Name";
        		            }
        		            echo "<tr><td style='font-weight: normal; padding-right: 0px;'>";

        		            if ($role->isRegistered($current_larp) && !$role->userMayEdit($current_larp)) {
        		                echo "<a href='view_role.php?id=$role->Id'>$role->Name <i class='fa-solid fa-eye'></i></a>";
        		                if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i> ";
        		                
        		                echo "- $role->Profession $role_group_name</td>";
        		                if ($role->hasImage()) {
        		                    
        		                    $image = Image::loadById($role->ImageId);
        		                    echo "<td><a href='show_role_image.php?id=$role->Id'><img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/></a> <a href='logic/delete_role_image.php?id=$role->Id'>Ta bort bild</a></td>";
        		                }
        		                else {
        		                    echo "<td><a href='upload_role_image.php?id=$role->Id'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>";
        		                }
        		                if ($current_larp->isEnded()) {
        		                    echo "<td><a href='larp_report_form.php?id=$role->Id'>Vad hände?</a></td>";
        		                }
        		            }
        		            else {
        		                echo "<a href='role_form.php?operation=update&id=$role->Id'>$role->Name <i class='fa-solid fa-pen'></i></a>";
        		                if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i> ";
        		                
        		                echo "- $role->Profession $role_group_name";
            		            if($role->isNeverRegistered()) {
            		                echo "&nbsp;<a href='logic/delete_role.php?id=" . $role->Id . "'><i class='fa-solid fa-trash' title='Ta bort karaktär'></i></a>";
            		            }
            		            
            		            echo "</td>\n";
        		            }
        		            echo "</tr>\n";
        		            
        		            
        		        }
        		        echo "</table>";
    		        }
    		        else {
    		            echo "<br><b>Har ännu ingen karaktär</b>&nbsp;&nbsp;<a href='role_form.php'>".showStatusIcon(false)."</a><br>\n";
    		        }
        		        


    		        
    		        
    		        
    		        //NPC'er
    		        $npcs = NPC::getReleasedNPCsForPerson($person, $current_larp);
    		        if (isset($npcs) && count($npcs) > 0) {
    		            echo "<br><b>NPC:</b><br>\n";
    		            echo "<table class='roles'>\n";
    		            foreach ($npcs as $npc)  {
    		                echo "<tr><td style='font-weight: normal; padding-right: 0px;'>";
    		                echo "<a href='view_npc.php?id=$npc->Id'>$npc->Name <i class='fa-solid fa-eye'></i></a>";
    		                
    		                if ($npc->IsInGroup()) {
    		                  $npc_group = $npc->getNPCGroup();
		                      echo " - <a href='view_npc_group.php?id=$npc->NPCGroupId'>$npc_group->Name <i class='fa-solid fa-eye'></i></a>";
    		                }

    		                    
    		            }
    		            echo "</table>";    
    		            
    		        }
    		        echo "</div>\n";
    		        
    		    }
    		}
    		?>
    		</div>
		</div>
	</body>
</html>