<?php
require 'header.php';
include_once '../includes/error_handling.php';

include "navigation.php";

?>

		<div class="content">
			<h1>Anmälan till <?php echo $current_larp->Name;?></h1>
        	  <?php if (isset($error_message) && strlen($error_message)>0) {
        	      echo '<div class="error">'.$error_message.'</div>';
        	  }
        	  if (isset($message_message) && strlen($message_message)>0) {
        	      echo '<div class="message">'.$message_message.'</div>';
        	  }
        	  
            if ($current_larp->isFull() || Reserve_Registration::isInUse($current_larp)) {

                echo "<div><b style='color: red'>Lajvet är fullt, men du kan göra en anmälan så att du hamnar på reservlistan.</b>";
                echo "</div>";
            }
            elseif ($current_larp->isPastLatestRegistrationDate()) {

                echo "<div><b style='color: red'>Sista anmälningsdag har passerat</b>";
                echo "</div>";
            }
            elseif ($current_larp->RegistrationOpen == 0) {

                echo "<div><b style='color: red'>Anmälan inte öppen</b>";
                echo "<br><br>Du kan registrera deltagare, grupper och karaktärer i väntan på att anmälan ska öppna. <br><br>"; 
                echo "OBS! En karaktär kan bara bli medlem i en grupp om den är anmäld. Så det får du editera efter att anmälan har öppnat. Men övrig information kan du fylla i så länge.";
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
			 	<i class='fa-solid fa-pen'></i> - Ändra<br>
			 	<i class='fa-solid fa-trash'></i> - Ta bort<br>
			 	<i class='fa-solid fa-image-portrait'></i> - Ladda upp bild (går bara efter anmälan)<br>
			 	<i class='fa-solid fa-skull-crossbones'></i> - Död	
			 	
			 </div>
		</div>
		<div class="content">
    		<h2>Registreringar / anmälningar</h2>
    		<div>
    		<?php 
    		
    		//Personer
    		$persons = $current_user->getPersons();
    		if (empty($persons)) {
    		    echo "<a href='person_form.php'>Registrera en deltagare.</a>";
    		} else {
    		    foreach ($persons as $person)  {
    		        $roles = $person->getRoles($current_larp);
    		        $groups = $person->getGroups($current_larp);
    		        
    		        echo "<div class='person'>\n";
    		        
    		        if (($person->isRegistered($current_larp)) || $person->isReserve($current_larp)) {
    		            echo "<a href='view_person.php?id=" . $person->Id . "'><h3>$person->Name</a></h3>\n";    		            
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
    		            echo "<tr><td>Anmäld</td><td>";

    		            if ($person->isReserve($current_larp)) {
    		                echo "Reservlista";
    		            }
    		            else if ($person->isNotComing($current_larp)) {
    		                echo "Avbokad";
    		            }
    		            else {
    		                echo showStatusIcon($person->isRegistered($current_larp), "person_registration_form.php?PersonId=$person->Id");
    		            }
    		            echo "</td></tr>\n";
    		        }
    		        if ($person->isRegistered($current_larp) && !$person->isNotComing($current_larp)) {
                        $registration = $person->getRegistration($current_larp);
                        
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
                        echo "<tr><td>Godkända karaktärer</td><td>" . showStatusIcon($person->isApprovedCharacters($current_larp)). "</td></tr>\n";
                        echo "<tr><td>Betalat</td><td>" . showStatusIcon($person->hasPayed($current_larp));
                        if (!$person->hasPayed($current_larp)) {

                            $campaign = $current_larp->getCampaign();
                            echo "</td><td>Betala <b>$registration->AmountToPay</b> SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>. Betalas senast ".$registration->paymentDueDate();
                        }
                        echo "</td></tr>\n";
 
                        
                        echo "<tr><td>Medlem</td><td>".showStatusIcon($registration->isMember(), "https://ebas.sverok.se/signups/index/5915")."</a>";
                        if (!$registration->isMember()) {
                            echo "</td><td><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>Betala medlemsavgiften</a>";
                            echo "</td></tr>\n";
                        }

                        echo "<tr><td>Säker plats på lajvet</td><td>".showStatusIcon(($registration->hasSpotAtLarp()))."</a></td></tr>";
                       
                    }
                    echo "</table>";
    		        
                    
                    //Grupper
    		        if (isset($groups) && count($groups) > 0) {
    		            echo "<br><b>Gruppansvarig för:</b><br>\n";
    		        }
    		        foreach ($groups as $group)  {
    		            if ($group->isRegistered($current_larp) && !$group->userMayEdit($current_larp)) {
    		                echo  "<a href='view_group.php?id=$group->Id'>$group->Name</a>";
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
    		            if ($group->hasImage()) {
    		                
    		                $image = Image::loadById($group->ImageId);
    		                echo "<td><img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/> <a href='logic/delete_image.php?id=$group->Id&type=group'>Ta bort bild</a></td>\n";
    		            }
    		            else {
    		                echo "<td><a href='upload_image.php?id=$group->Id&type=group'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>\n";
    		            }
    		        }
    		       
    		        
    		        //Karaktärer
    		        if (isset($roles) && count($roles) > 0) {
    		            echo "<br><b>Karaktärer:</b><br>\n";
    		    
    		            echo "<table class='roles'>\n";   		            
        		        foreach ($roles as $role)  {
        		            echo "<tr>";
        		            
        		            //Eventuell bild
        		            echo "<td style='font-weight: normal; padding-right: 0px;'>";
        		            if ($role->hasImage()) {
        		                $image = Image::loadById($role->ImageId);
        		                echo "<a href='view_role.php?id=$role->Id'><img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/></a>";
        		                echo "<a href='logic/delete_image.php?id=$role->Id&type=role'><i class='fa-solid fa-trash' title='Ta bort bild'></i></a>\n";
        		            }
        		            else {
        		                echo "<a href='upload_image.php?id=$role->Id&type=role'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
        		            }
        		            echo "</td>";

        		            //Namn på karaktären
        		            echo "<td>";
        		            if ($role->isRegistered($current_larp) && !$role->userMayEdit($current_larp)) {
        		                echo "<a href='view_role.php?id=$role->Id'>$role->Name</a> ";
        		            }
        		            else {
        		                echo "<a href='role_form.php?operation=update&id=$role->Id'>$role->Name <i class='fa-solid fa-pen'></i></a>";
        		            }
    		                if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i> ";
    		                if($role->isNeverRegistered()) {
    		                    echo "&nbsp;<a href='logic/delete_role.php?id=" . $role->Id . "'><i class='fa-solid fa-trash' title='Ta bort karaktär'></i></a>";
    		                }
    		                echo "</td>";    
        		               
        		            //Karaktärsblad
        		            echo "<td>";
    		                $registration = $person->getRegistration($current_larp);
    		                if (!empty($registration) && $registration->SpotAtLARP==1) {
    		                    echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
    		                }
    		                echo "</td>\n";
        		              
    		                //Grupp
    		                $role_group = $role->getGroup();
    		                $role_group_name = " (Inte med i någon grupp)";
    		                if (isset($role_group) && $role->isRegistered($current_larp)) {
    		                    $role_group_name = "<a href='view_group.php?id=$role_group->Id'>$role_group->Name</a> ".
        		                    "<a href='group_sheet.php?id=" . $role_group->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad för $role_group->Name'></i></a>\n";
    		                }
    		                elseif (isset($role_group)) {
    		                    $role_group_name = "$role_group->Name";
    		                }
    		                echo "<td>";
    		                echo $role_group_name;
        		            echo "</td>";  		            
    		                
    		                if ($current_larp->isEnded()) {
    		                    echo "<td><a href='larp_report_form.php?id=$role->Id'>Vad hände?</a></td>";
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
    		                echo "<a href='view_npc.php?id=$npc->Id'>$npc->Name</a>";
    		                
    		                if ($npc->IsInGroup()) {
    		                  $npc_group = $npc->getNPCGroup();
		                      echo " - <a href='view_npc_group.php?id=$npc->NPCGroupId'>$npc_group->Name</a>";
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
		<?php 
		  if ($current_user->isComing($current_larp)) {
		        echo "</div><div class='content'>\n";
		        echo "<h2>Telegram</h2>\n";
		        echo "<div>\n";
		        $telegram_array = $current_user->getTelegramsAtLarp($current_larp);
		        if(isset($telegram_array) && count($telegram_array) > 0) {
		            echo "<b>Telegram skapade av $current_user->Name:</b><br>\n";
		            echo "<table class='data' id='telegrams' align='left'>";
		            echo "<tr align='left'><th>Leveranstid</th><th>Avsändare</th><th>Mottagare</th>";
		            echo "<th>Meddelande</th><th>Ok</th><th>Ändra</th><th>Visa</th></tr>\n";
		            foreach ($telegram_array as $telegram) {
		                echo "<tr>\n";
		                echo "<td style='font-weight:normal'>" . $telegram->Deliverytime . "</td>\n";
		                echo "<td>" . $telegram->Sender ."<br>". $telegram->SenderCity. "</td>\n";
		                echo "<td>" . $telegram->Reciever ."<br>". $telegram->RecieverCity . "</td>\n";
		                echo "<td>" . str_replace("\n", "<br>", $telegram->Message) . "</td>\n";
		                echo "<td>" . showStatusIcon($telegram->Approved) . "</td>\n";
		                echo "<td align='center'>" . "<a href='telegram_suggestion.php?operation=update&id=" . $telegram->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
		                echo "<td align='center'>" . "<a href='logic/show_telegram.php?id=" . $telegram->Id . "'  target='_blank'><i class='fa-solid fa-file-pdf'></i></td>\n";
		                echo "</tr>\n";
		            }
		            echo "</table></p>\n";

		        }
		        echo "<p><a href='telegram_suggestion.php'><b>Skapa ett telegram</b></i></a>\n";
		        echo "</div></div>";
		        
		        
		        echo "</div><div class='content'>\n";
		        echo "<h2>Brev</h2>\n";
		        echo "<div>\n";
		        $letter_array = $current_user->getLettersAtLarp($current_larp);
		        if(isset($letter_array) && count($letter_array) > 0) {
		            echo "<b>Brev skapade av $current_user->Name:</b><br>\n";
		            echo "<table class='data' id='letters' align='left'>";
		            echo "<tr align='left'><th>Ort och datum</th><th>Hälsningsfras</th>";
		            echo "<th>Meddelande</th><th>Hälsning</th><th>Underskrift</th><th>Ok</th><th>Ändra</th><th>Visa</th></tr>\n";
		            foreach ($letter_array as $letter) {
		                echo "<tr>\n";
		                echo "<td style='font-weight:normal'>$letter->WhenWhere</td>\n";
		                echo "<td>$letter->Greeting</td>\n";
		                echo "<td>" . str_replace("\n", "<br>", $letter->Message) . "</td>\n";
		                echo "<td>$letter->EndingPhrase</td>\n";
		                echo "<td>$letter->Signature</td>\n";
		                echo "<td>" . showStatusIcon($letter->Approved) . "</td>\n";
		                echo "<td align='center'>" . "<a href='letter_suggestion.php?operation=update&id=" . $letter->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
		                echo "<td align='center'>" . "<a href='logic/show_letter.php?id=" . $letter->Id . "'  target='_blank'><i class='fa-solid fa-file-pdf'></i></td>\n";
		                echo "</tr>\n";
		            }
		            echo "</table></p>\n";
		            
		        }
		        echo "<p><a href='letter_suggestion.php'><b>Skapa ett brev</b></i></a>\n";
		        echo "</div></div>";
		        
		        
		        
    		    echo "</div><div class='content'>\n";
    		    echo "<h2>Rykten</h2>\n";
    		    echo "<div>\n";
    		    $rumour_array = $current_user->getRumoursAtLarp($current_larp);
    		    if(isset($rumour_array) && count($rumour_array) > 0) {
    		        echo "<b>Rykten skapade av $current_user->Name:</b><br>\n";
    		        echo "<table class='data' id='letters' align='left'>";
    		        echo "<tr align='left'><th>Text</th><th>Ok</th><th>Ändra</th>";
    		        echo "</tr>\n";
    		        foreach ($rumour_array as $rumour) {
    		            echo "<tr>\n";
    		            echo "<td style='font-weight:normal'>$rumour->Text</td>\n";
    		            echo "<td>" . showStatusIcon($letter->Approved) . "</td>\n";
    		            echo "<td align='center'>";
    		            if (!$rumour->isApproved()) echo "<a href='rumour_suggestion.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a>";
                        echo "</td>\n";
    		            echo "</tr>\n";
    		        }
    		        echo "</table></p>\n";
    		        
    		    }
    		    echo "<p><a href='rumour_suggestion.php'><b>Skapa ett rykte</b></i></a>\n";
    		    echo "</div></div>";
    		    
    }
    ?>
	</body>
</html>