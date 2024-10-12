<?php
require 'header.php';
// include_once '../includes/error_handling.php';

$_SESSION['navigation'] = Navigation::PARTICIPANT;

include "navigation.php";
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));
?>

<style>
.role {
line-height: 1.8;
}
</style>



		<div class="content">
			<h1>Anmälan till <?php echo $current_larp->Name;?></h1>
        	  <?php if (isset($error_message) && strlen($error_message)>0) {
        	      echo '<div class="error">'.$error_message.'</div>';
        	  }
        	  if (isset($message_message) && strlen($message_message)>0) {
        	      echo '<div class="message">'.$message_message.'</div>';
        	  }
        	  
        	  if ($current_larp->isEnded()) {
        	      echo "<div class='nomargin'><b style='color: green'>Lajvet är över. Hoppas att du hade roligt.<br>Gå gärna och och skriv vad som hände.</b>";
        	      echo "</div>";
        	  }elseif ($current_larp->isPastLatestRegistrationDate() && !$current_larp->mayRegister()) {        	      
        	      echo "<div class='nomargin'><b style='color: red'>Sista anmälningsdag har passerat</b>";
        	      echo "</div>";
        	  } elseif ($current_larp->isPastLatestRegistrationDate()) {       	      
        	      echo "<div class='nomargin'><b style='color: red'>Sista anmälningsdag har passerat</b>, men du kan göra en anmälan så att du hamnar på reservlistan.<br>Arrangörerna väljer vilka som plockas in. Vilken plats man har på reservlistan spelar ingen roll.";
        	      echo "</div>";
        	  } elseif ($current_larp->isFull() && $current_larp->RegistrationOpen == 0) {
        	      
        	      echo "<div class='nomargin'><b style='color: red'>Anmälan är stängd</b>";
         	      echo "</div>";
        	  } elseif ($current_larp->isFull() || Reserve_Registration::isInUse($current_larp)) {

                echo "<div class='nomargin'><b style='color: red'>Lajvet är fullt, men du kan göra en anmälan så att du hamnar på reservlistan.</b> Om någon annan avbokar kan du kanske få en plats.<br>Arrangörerna väljer vilka som plockas in. Vilken plats man har på reservlistan spelar ingen roll.";
                echo "</div>";
            } elseif ($current_larp->RegistrationOpen == 0) {

                echo "<div class='nomargin'><b style='color: red'>Anmälan inte öppen</b>";
                echo "<br><br>Du kan registrera deltagare, grupper och karaktärer i väntan på att anmälan ska öppna. <br><br>"; 
                echo "OBS! En karaktär kan bara bli medlem i en grupp om den är anmäld. Så det får du editera efter att anmälan har öppnat. Men övrig information kan du fylla i så länge.";
                echo "</div>";
            }
            ?>
			

			<div class='nomargin'>
			Läs gärna <a href='help.php'>hjälpen</a> om du vill veta hur du använder systemet.
			
			 	
			 </div>
		</div>
		<div class="content">
    		<h2>Registreringar / anmälningar<?php help_icon("Du kan registrera och hantera flera deltagare från ditt konto.</br>Exempelvis kan en förälder hantera familjens barn via ett konto."); ?></h2><br />
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
    		        

		            echo "<h3><a href='person_form.php?operation=update&id=" . $person->Id . "'>$person->Name&nbsp;<i class='fa-solid fa-pen'></i></a>";
		            if($person->isNeverRegistered() && (!isset($roles) or count($roles) == 0) && (!isset($groups) or count($groups) == 0)) {
		                echo "&nbsp;<a href='logic/delete_person.php?id=" . $person->Id . "'><i class='fa-solid fa-trash' title='Ta bort deltagare'></i></a>";
		            }
		            echo "</h3>\n";
    		        
    		        echo "Epost: " . $person->Email. "<br>\n";
    		        echo "Mobilnummer: " . $person->PhoneNumber. "<br>\n";
    		        echo "<table  class='checks'>";
    		        if (isset($roles) && count($roles) > 0) {
    		            echo "<tr><td valign='baseline'>Anmäld</td><td>";

    		            if ($person->isReserve($current_larp)) {
    		                echo "Reservlista";
    		            }
    		            else if ($person->isNotComing($current_larp)) {
    		                echo "Avbokad";
    		            }
    		            else {
    		                if ($current_larp->isEnded()) {
    		                    if ($person->isRegistered($current_larp)) echo showStatusIcon(true);
    		                    else echo "Var inte amnäld till lajvet";
    		                }
    		                else echo showParticipantStatusIcon($person->isRegistered($current_larp), "Du är inte anmäld");
    		            }
    		            if ($person->isRegistered($current_larp)) echo "</td><td><a href='view_registration.php?id=$person->Id'>Visa anmälan</a>";
    		            echo "</td></tr>\n";
    		        }
    		        if ($person->isRegistered($current_larp) && !$person->isNotComing($current_larp)) {
                        $registration = $person->getRegistration($current_larp);
                        
                        //Utvärdering
                        if ($current_larp->isEnded() && !(AccessControl::hasAccessLarp($current_user, $current_larp) && (sizeof($persons) == 1 || $current_user->Name==$person->Name))) {
                            echo "<tr><td valign='baseline'>Utvärdering</td><td>";
                            if ($current_larp->isEvaluationOpen()) {
                                if ($registration->hasDoneEvaluation()) {
                                    echo showStatusIcon(true);
                                    echo "</td><td>Utvärderingen är inlämnad";
                                } elseif ($current_larp->useInternalEvaluation()) {
                                    echo showParticipantStatusIcon(false, "Utvärderingen är inte gjord");
                                    echo "</td><td><a href='evaluation.php?PersonId=$person->Id'>Gör utvärdering";
                                } else {
                                    echo "<a target='_blank' href='$current_larp->EvaluationLink'>Gör utvärdering (extern länk)";
                                }
                            } else echo "Utvärderingen öppnar $current_larp->EvaluationOpenDate";
                            echo "</td></tr>\n";
                            
                        }
                        
                        //Ansvarig vuxen
                        if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian)  {
                            echo "<tr><td valign='middle'>Ansvarig vuxen</td><td>";
                            if (empty($registration->GuardianId)) {
                                echo showParticipantStatusIcon(false, "Du saknar ansvarig vuxen");
                                echo "</td><td><a href='input_guardian.php?PersonId=$person->Id'>Ange ansvarig vuxen</a>";
                            }
                            else {
                                echo showStatusIcon(true);
                                echo "</td><td>".$registration->getGuardian()->Name;
                            }
                            echo "</td></tr>\n";                            
                        }
                        echo "<tr><td valign='baseline'>Godkända karaktärer</td><td>" . showParticipantStatusIcon($person->isApprovedCharacters($current_larp), "Karaktärerna är inte godkända"). "</td></tr>\n";
                        echo "<tr><td valign='baseline'>Betalat</td><td>" . showParticipantStatusIcon($person->hasPayed($current_larp), "Du har inte betalat");
                        
                       
                        if (!$person->hasPayed($current_larp)) {
                            $invoice = $registration->getInvoice();
                            if (!empty($invoice)) {
                                echo "</td><td>Avgiften är del av en faktura som betalas av $invoice->Recipient";
                            } else {
                                $campaign = $current_larp->getCampaign();
                                echo "</td><td>Betala <b>$registration->AmountToPay</b> SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>. Betalas senast ".$registration->paymentDueDate();
                                if (!empty($campaign->SwishNumber)) 
                                    echo "<br><img width='200' src='../includes/display_image.php?Swish=1&RegistrationId=$registration->Id&CampaignId=$campaign->Id'/>\n";
                                    
                                
                            }
                        }
                        echo "</td></tr>\n";
 
                        
                        echo "<tr><td>Medlem</td><td>".showParticipantStatusIcon($registration->isMember(), "Du är inte medlem i Berghems Vänner")."</a>";
                        echo "</td>";
                        if (!$registration->isMember()) {
                            $currentYear = date("Y");
                            $larpYear = substr($current_larp->StartDate, 0, 4);
                            if ($currentYear == $larpYear) {
                                echo "<td><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>Betala medlemsavgiften</a></td>";
                            } else {
                                echo "<td>Medlemsavgiften kan inte betalas än.</td>";
                            }
                            
                        }

                        echo "</tr>\n";
                        echo "<tr><td>Säker plats på lajvet</td><td>".showParticipantStatusIcon($registration->hasSpotAtLarp(), "Du har inte fått en plats på lajvet")."</a></td></tr>";
                        echo "<tr><td>Boende</td>";
                        if ($current_larp->isHousingReleased()) {
                            $house = House::getHouseAtLarp($person, $current_larp);
                            if (empty($house)) {
                                echo "<td>Inget tilldelat</td>";
                            } else {
                                echo "<td><a href='view_house.php?id=$house->Id'>$house->Name</a></td>";
                            }
                        } else {
                            echo "<td>Inte klart än</td>";
                        }
                        echo "</tr>";

                    }
                    echo "</table>";
    		        
                    //Epost
                    $emails = Email::allForPersonAtLarp($person, $current_larp);
                    if (!empty($emails)) {
                        echo "<br><b>Meddelanden från arrangörer till $person->Name</b><br>\n";
                        
                        $tableId = "mail";
                        echo "<table id='$tableId' class='data'>";
                        echo "<tr>".
                            "<th onclick='sortTable(0, \"$tableId\")'>Ämne</th>".
                            "<th onclick='sortTable(1, \"$tableId\")'>Bilagor</th>".
                            "<th onclick='sortTable(2, \"$tableId\")'>Skickat av</th>".
                            "<th onclick='sortTable(3, \"$tableId\")'>Skickat</th>".
                            "<th onclick='sortTable(3, \"$tableId\")'>Ligger kvar tills</th>".
                            "</tr>\n";
                        foreach (array_reverse($emails) as $email) {
                            $sendUserName = "";
                            if (isset($email->SenderUserId)) {
                                $user = User::loadById($email->SenderUserId);
                                $sendUserName = $user->Name;
                            }
                            
                            echo "<tr>";
                            echo "<td><a href='view_email.php?id=$email->Id'>$email->Subject</a></td>";
                            
                            $attachements = $email->attachments();
                            echo "<td>";
                            if (!empty($attachements)) echo "<i class='fa-solid fa-paperclip'></i>";
                            echo "</td>";
                            echo "<td>$sendUserName</td>";
                            echo "<td>$email->SentAt</td>";
                            echo "<td>$email->DeletesAt</td>";
                        }
                        echo "</table>";
                        echo "<br>";
                        
                        
                    }
                    
                    
                    
                    
                    //Grupper
    		        if (!empty($groups)) {
    		            echo "<br><b>Gruppansvarig för:</b><br>\n";
    		            echo "<table class='roles'>\n";
    		            
    		        }
    		        foreach ($groups as $group)  {
    		            echo "<tr>";
    		            echo "<td>";
    		            if ($group->isRegistered($current_larp) && !$group->userMayEdit($current_larp)) {
    		                echo  $group->getViewLink();        		                
    		            }
    		            else {
    		                echo $group->getViewLink() . " " . $group->getEditLinkPen(false);
    		                
    		                 if($group->isNeverRegistered()) {
    		                     echo "&nbsp;<a href='logic/delete_group.php?id=" . $group->Id . "'><i class='fa-solid fa-trash' title='Ta bort grupp'></i></a>";
    		                 }
    		            }
    		            echo "</td>";
    		            echo "<td>Anmäld&nbsp;&nbsp;" . showParticipantStatusIcon($group->isRegistered($current_larp), "Gruppen är inte anmäld") . "</td>\n";
    		            if ($group->hasImage()) {
    		                echo "<td>";
    		                echo "<img width='30' src='../includes/display_image.php?id=$group->ImageId'/>\n";
    		                echo " <a href='../common/logic/rotate_image.php?id=$group->ImageId'><i class='fa-solid fa-rotate-right'></i></a> <a href='logic/delete_image.php?id=$group->Id&type=group'>Ta bort bild</a></td>\n";
    		            }
    		            else {
    		                echo "<td><a href='upload_image.php?id=$group->Id&type=group'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>\n";
    		            }
    		            if ($group->isRegistered($current_larp) && $current_larp->isEnded()) {
    		                echo "<td><a href='larp_report_form.php?groupId=$group->Id'>Vad hände?</a> ".
        		                showParticipantStatusIcon($group->hasRegisteredWhatHappened($current_larp), "Inte noterat vad som hände") .
     		                "</td>";
    		            }
    		            
    		            echo "</tr>";
    		        }
    		        echo "</table>";
    		        
    		        
    		        
    		        
    		        
    		        
    		        //Karaktärer
    		        if (isset($roles) && count($roles) > 0) {
         		        foreach ($roles as $role)  {
        		            echo "<div id='role_$role->Id' class='role' style='border-bottom: solid 1px silver; overflow: hidden;'>";
        		            echo "<table><tr><td style='font-weight: normal; padding-right: 0px;' width='140px'>";
        		            //Eventuell bild
 

        		            if ($role->hasImage()) {
        		                echo $role->getViewLink();
        		                echo "<img width='100' src='../includes/display_image.php?id=$role->ImageId'/ >\n";
        		                echo "</a>";
        		                echo "<br>";
        		                echo "<a href='../common/logic/rotate_image.php?id=$role->ImageId'><i class='fa-solid fa-rotate-right'></i></a> <a href='logic/delete_image.php?id=$role->Id&type=role'><i class='fa-solid fa-trash' title='Ta bort bild'></i>  Ta bort bild</a>\n";
        		            }
        		            else {
        		                echo "<img width='100' src='../images/man-shape.png' / >\n";
        		                echo "<br>";
        		                echo "<a href='upload_image.php?id=$role->Id&type=role'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i> Ladda upp bild</a> \n";
        		            }
        		            echo "</td><td>";
        		            
        		            //Namn på karaktären
        		            //echo "<div style='clear:both;'>";
        		            echo "<strong>";
        		            echo $role->getViewLink();
        		            
        		            if (!$role->isRegistered($current_larp) || $role->userMayEdit($current_larp)) {
        		                echo " " . $role->getEditLinkPen(false);
        		            }
							
        		            if($role->isNeverRegistered()) {
        		                echo "&nbsp;<a href='logic/delete_role.php?id=" . $role->Id . "'><i class='fa-solid fa-trash' title='Ta bort karaktär'></i></a>";
        		            }
        		            echo "</strong>";
        		            //Karaktärsblad
        		            $registration = $person->getRegistration($current_larp);
        		            if (!empty($registration) && $registration->SpotAtLARP==1) {
        		                echo " <a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
        		            }
        		            echo "<br>";
        		            
        		            
         		               
    		                
    		                //Grupp
    		                $role_group = $role->getGroup();
    		                $role_group_name = "Inte med i någon grupp";
    		                if (isset($role_group) && $role->isRegistered($current_larp)) {
    		                    $role_group_name = $role_group->getViewLink() .
        		                    "<a href='group_sheet.php?id=" . $role_group->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad för $role_group->Name'></i></a>\n";
    		                }
    		                elseif (isset($role_group)) {
    		                    $role_group_name = "$role_group->Name";
    		                }
    		                echo $role_group_name;
    		                echo "<br>";
    		                
    		                //Specialkunskaper
         		            if (Magic_Magician::isMagician($role)) {
        		                $magician = Magic_Magician::getForRole($role);
        		                echo "<a href='view_magician.php?id=$role->Id'>Magiker</a> ";
        		                echo "<a href='magic_magician_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för $role->Name'></i></a> ";
        		                if ($magician->StaffApproved && $magician->hasDoneWorkshop())  echo showStatusIcon(true);
        		                else {
        		                    if (!$magician->StaffApproved) {
        		                        if ($isMob) echo "<br>";
        		                        echo showParticipantStatusIcon(false, "Staven är inte godkänd");
        		                    }
        		                    if (!$magician->hasDoneWorkshop()) {
        		                        if ($isMob) echo "<br>";
        		                        echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om magi");
        		                    }
        		                }
        		                echo "<br>";
         		            }
        		            if (Alchemy_Supplier::isSupplier($role)) {
        		                $supplier = Alchemy_Supplier::getForRole($role);
        		                echo "<a href='view_alchemy_supplier.php?id=$role->Id'>Löjverist</a> ";
        		                echo "<a href='alchemy_supplier_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Lövjeristblad för $role->Name'></i></a> ";
        		                echo " ";
        		                if ($supplier->allAmountOfIngredientsApproved($current_larp) &&
        		                    $supplier->hasDoneWorkshop() &&
        		                    $supplier->hasIngredientList($current_larp)) echo showStatusIcon(true);
    		                    else {
    		                        if (!$supplier->hasIngredientList($current_larp)) {
    		                            if ($isMob) echo "<br>";
    		                            echo showParticipantStatusIcon(false,"Du har ingen ingredienslista");
    		                        }
    		                        if (!$supplier->allAmountOfIngredientsApproved($current_larp)) {
    		                            if ($isMob) echo "<br>";
    		                            echo showParticipantStatusIcon(false,"Antalet ingredienser är ännu inte godkänt");
    		                        }
    		                        if (!$supplier->hasDoneWorkshop()) {
    		                            if ($isMob) echo "<br>";
    		                            echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om lövjeri");
    		                        }
    		                    }
    		                    echo "<br>";
        		            }
        		            if (Alchemy_Alchemist::isAlchemist($role)) {
        		                $alchemist = Alchemy_Alchemist::getForRole($role);
        		                echo "<a href='view_alchemist.php?id=$role->Id'>Alkemist</a> ";
        		                echo "<a href='alchemy_alchemist_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för $role->Name'></i></a> ";
        		                echo " ";
        		                $recipes = $alchemist->getRecipes(false);
        		                if ($alchemist->recipeListApproved() && $alchemist->hasDoneWorkshop() && 
        		                    !empty($recipes)) echo showStatusIcon(true);
        		                else {
        		                    if (empty($recipes)) {
        		                        if ($isMob) echo "<br>";
        		                        echo showParticipantStatusIcon(false, "Din receptlist är tom");
        		                    }
        		                    if (!$alchemist->recipeListApproved()) {
        		                        if ($isMob) echo "<br>";
        		                        echo showParticipantStatusIcon(false,"Din receptlista är inte godkänd, än");
        		                    }
        		                    if (!$alchemist->hasDoneWorkshop()) {
        		                        if ($isMob) echo "<br>";
        		                        echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om alkemi");
        		                    }
        		                }
        		                echo "<br>";
        		            }
        		            if (Vision::hasVisions($current_larp, $role)) {
        		                echo "<a href='view_visions.php?id=$role->Id'>Syner</a> ";
        		                echo "<br>";
        		            }
        		            
        		            
        		            //Grupperingar
        		            $subdivisions = Subdivision::allVisibleForRole($role);
        		            if (!empty($subdivisions)) {
        		                $subdivisionLinks = array();
        		                foreach ($subdivisions as $subdivision) {
        		                    if ($subdivision->isVisibleToParticipants()) $subdivisionLinks[] = "<a href='view_subdivision.php?id=$subdivision->Id'>$subdivision->Name</a>";
        		                }
        		                if (!empty($subdivisionLinks)) echo implode(", ", $subdivisionLinks)."<br>";
        		            }
        		            
        		            
        		            
    		                if ($role->isRegistered($current_larp) && $current_larp->isEnded()) {
    		                    echo "<a href='larp_report_form.php?roleId=$role->Id'>Vad hände?</a> ".
        		                    showParticipantStatusIcon($role->hasRegisteredWhatHappened($current_larp), "Inte noterat vad som hände") .
        		                    "<br>";
    		                } else {
     
        		                if ($person->isRegistered($current_larp)) {
        		                    echo "<br>";
        		                    //echo "<td style='font-weight: normal; padding-right: 0px;'>";
        		                    if ($role->isRegistered($current_larp)) echo "Anmäld till lajvet";
        		                    else echo "Inte med på lajvet";
        		                    echo "<br>";
        		                }
    		                }
    		                
    		                echo "</td></tr></table>";
    		                echo "</div>\n";
        		            
        		            
        		        }
     		        }
    		        else {
    		            echo "<br><b>Har ännu ingen karaktär</b>&nbsp;&nbsp;<a href='role_form.php'>";
    		            if (!$current_larp->isEnded()) echo showParticipantStatusIcon(false, "Du har inte registrerat någon karaktär");
    		            
    		            echo "</a><br>\n";
    		        }
     
    		        
    		        //NPC'er
    		        $npcs = NPC::getReleasedNPCsForPerson($person, $current_larp);
    		        if (isset($npcs) && count($npcs) > 0) {
    		            echo "<br><b>NPC:</b><br>\n";
    		            echo "<table class='roles'>\n";
    		            foreach ($npcs as $npc)  {
    		                echo "<tr><td style='font-weight: normal; padding-right: 0px;'>";
    		                if ($npc->hasImage()) {
    		                    echo "<img width='30' src='../includes/display_image.php?id=$npc->ImageId'/>\n";
    		                    echo "<a href='logic/delete_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-trash' title='Ta bort bild'></i></a>\n";
    		                }
    		                else {
    		                    echo "<a href='upload_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
    		                }
    		                
    		                echo "</td><td>";
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
		      echo "<div class='content'>\n";
		      echo "<h2>Bildgallerier på deltagare och funktionärer</h2>\n";
		      echo "<div>";
		      echo "<a href='participants.php' target='_blank'>Deltagare på lajvet</a><br>\n";
		      echo "<a href='officials.php' target='_blank'>Funktionärer på lajvet</a>\n";
		      echo "</div></div>";
		      
		      
		      
		      if ($current_larp->hasTelegrams()) {
		      
		        echo "<div class='content'>\n";
		        echo "<h2>Telegram";
		        help_icon("På ett visst klockslag under lajvet kommer telegrammets mottagare att få ditt meddelande.");
		        echo "</h2>\n";
		        echo "<div>\n";
		        $telegram_array = $current_user->getTelegramsAtLarp($current_larp);
		        $antal = (isset($telegram_array)) ? count($telegram_array) : 0;
		        if($antal > 0) {
		            echo "<details><summary>Du har skapat $antal telegram</summary> ";
// 		            echo "<b>Telegram skapade av $current_user->Name:</b><br>\n";
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
		            echo "</table></p><br>";
		            echo "</details>\n";

		        }
		        echo "<p><a href='telegram_suggestion.php'><b>Skapa ett telegram</b></i></a>\n";
		        echo "</div></div>";
		      }
		      
		      
		      if ($current_larp->hasLetters()) {
		          
		        echo "<div class='content'>\n";
		        echo "<h2>Brev"; 
		        help_icon("Någon gång under lajvet kommer förhoppningsvis det här handskrivna meddelandet nå sin mottagare.");
		        echo "</h2>\n";
		        echo "<div>\n";
		        $letter_array = $current_user->getLettersAtLarp($current_larp);
		        $antal = (isset($letter_array)) ? count($letter_array) : 0;
		        if($antal > 0) {
		            echo "<details><summary>Du har skrivit $antal brev</summary> ";
// 		            echo "<b>Brev skapade av $current_user->Name:</b><br>\n";
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
		            echo "</details>\n";
		        }
		        echo "<p><a href='letter_suggestion.php'><b>Skriv ett brev</b></i></a>\n";
		        echo "</div></div>";
		      }
		      
		      
		      if ($current_larp->hasRumours()) {
		          
    		    echo "<div class='content'>\n";
    		    echo "<h2>Rykten";
    		    help_icon("Rykten är kul. Sprid om dig eller andra.");
    		    echo "</h2>\n";
    		    echo "<div>\n";
    		    $rumour_array = $current_user->getRumoursAtLarp($current_larp);
    		    $antal = (isset($rumour_array)) ? count($rumour_array) : 0;
    		    if($antal > 0) {
    		        echo "<details><summary>Du har spridit $antal rykten</summary> ";
//     		        echo "<b>Rykten skapade av $current_user->Name:</b><br>\n";
    		        echo "<table class='data' id='letters' align='left'>";
    		        echo "<tr align='left'><th>Text</th><th>Ok</th><th>Ändra</th>";
    		        echo "</tr>\n";
    		        foreach ($rumour_array as $rumour) {
    		            echo "<tr>\n";
    		            echo "<td style='font-weight:normal'>$rumour->Text</td>\n";
    		            echo "<td>" . showStatusIcon($rumour->Approved) . "</td>\n";
    		            echo "<td align='center'>";
    		            if (!$rumour->isApproved()) echo "<a href='rumour_suggestion.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a>";
                        echo "</td>\n";
    		            echo "</tr>\n";
    		        }
    		        echo "</table></p>\n";
    		        echo "</details>\n";
    		        
    		    }
    		    echo "<p><a href='rumour_suggestion.php'><b>Sprid ett rykte</b></i></a>\n";
    		    echo "</div></div>";
	       }
	       
	      $adtypes = AdvertismentType::allActive($current_larp);
	       if (!empty($adtypes)) {
	           
	           
	           
	           echo "<div class='content'>\n";
	           echo "<h2>Annonser</h2>\n";
	           echo "<p><a href='advertisments.php'><b>Se alla annonser</b></i></a></p>\n";
	           $advertisment_array = $current_user->getAdvertismentsAtLarp($current_larp);
	           if(isset($advertisment_array) && count($advertisment_array) > 0) {
	               echo "<div>\n";
	               echo "<b>Annonser skapade av $current_user->Name:</b><br>\n";
	               echo "<table class='data' id='ads' align='left'>";
	               echo "<tr align='left'><th>Kontakt information</th><th>Text</th><th>Ändra/<br>Ta bort</th>";
	               echo "</tr>\n";
	               foreach ($advertisment_array as $advertisment) {
	                   echo "<tr>\n";
	                   echo "<td style='font-weight:normal'>$advertisment->ContactInformation</td>\n";
	                   echo "<td>$advertisment->Text</td>\n";
	                   echo "<td align='center'>";
	                   echo "<a href='advertisment_form.php?operation=update&id=" . $advertisment->Id . "'><i class='fa-solid fa-pen' title='Ändra annons'></i></a>";
	                   echo " <a href='logic/delete_advertisment.php?id=" . $advertisment->Id . "'><i class='fa-solid fa-trash' title='Ta bort annons'></i></a>";
	                   echo "</td>\n";
	                   echo "</tr>\n";
	               }
	               echo "</table></div>\n";
	               
	           }
	           echo "<p><a href='advertisment_form.php'><b>Skapa en annons</b></i></a></p>\n";
	           echo "</div>";
	       }
	       
        }
    ?>

	</body>
</html>