<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$person = Person::loadById($PersonId);

if (!$person->isRegistered($current_larp) && !$person->isReserve($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);
$reserveregistration = Reserve_Registration::loadByIds($person->Id, $current_larp->Id);
$activeRegistration = null;

if (isset($registration)) $activeRegistration = $registration;
elseif (isset($reserveregistration)) $activeRegistration = $reserveregistration;

include 'navigation.php';

?>

	<div class="content">
		<h1><?php echo $person->Name;?>&nbsp;<a href='edit_person.php?id=<?php echo $person->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Personnummer</td><td><?php echo $person->SocialSecurityNumber;?>, <?php echo $person->getAgeAtLarp($current_larp) ?> år</td></tr>
			<tr><td valign="top" class="header">Email</td><td><?php echo $person->Email." ".contactEmailIcon($person);?></td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td><td><?php echo $person->PhoneNumber;?></td></tr>
			<tr><td valign="top" class="header">Närmaste anhörig</td><td><?php echo $person->EmergencyContact;?></td></tr>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<tr><td valign="top" class="header">Ansvarig vuxen</td><td>
			
			<?php if (!empty($activeRegistration->GuardianId)) echo $activeRegistration->getGuardian()->getViewLink(); else echo showStatusIcon(false); ?>
			
			</td></tr>
		    
		    <?php 
		    }
		    ?>

		    <?php 
		    $minors = $person->getGuardianFor($current_larp);
		    if (!empty($minors)) {
    			echo "<tr><td valign='top' class='header'>Ansvarig för</td><td>";
    			$minor_str_arr = array();
    			foreach ($minors as $minor) {
    			     $minor_str_arr[] = $minor->getViewLink();
    			}
    			echo implode(", ", $minor_str_arr);
    			echo "</td></tr>";
		    }
		    ?>


			<tr><td valign="top" class="header">Erfarenhet</td><td><?php echo Experience::loadById($person->ExperienceId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Intriger du inte vill spela på</td><td><?php echo $person->NotAcceptableIntrigues;?></td></tr>

			<?php if (TypeOfFood::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av mat</td><td>
			<?php 
			echo TypeOfFood::loadById($activeRegistration->TypeOfFoodId)->Name;
			
			     
			     ?></td></tr>
			<?php } ?>
			<?php 
			if (isset($activeRegistration->FoodChoice))  { ?>
			    <tr><td valign="top" class="header">Matalternativ</td><td><?php echo $activeRegistration->FoodChoice; ?></td></tr>
			<?php }?>
			<tr><td valign="top" class="header">Vanliga allergier</td><td><?php echo commaStringFromArrayObject($person->getNormalAllergyTypes());?></td></tr>

			<tr><td valign="top" class="header">Andra allergier</td><td><?php echo $person->FoodAllergiesOther;?></td></tr>

			<tr><td valign="top" class="header">NPC önskemål</td><td><?php echo $activeRegistration->NPCDesire;?></td></tr>
			<tr>
				<td valign="top" class="header">Husförvaltare</td>
				<td><?php 
				
				  $houses = $person->housesOf();
				  $houseslinks = array();
				  foreach ($houses as $house) {
				      $houseslinks[] = "<a href='view_house.php?id=$house->Id'>".$house->Name."</a>";
				  }
				  echo implode(",", $houseslinks);
				  ?>
				</td>
			</tr>
			
			<?php if (HousingRequest::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Önskat boende</td>
				<td>
					<?php 
					$housingrequest = $activeRegistration->getHousingRequest();
					if (!empty($housingrequest)) echo $housingrequest->Name;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Typ av tält</td><td><?php echo nl2br(htmlspecialchars($activeRegistration->TentType)); ?></td></tr>
			<tr><td valign="top" class="header">Storlek på tält</td><td><?php echo nl2br(htmlspecialchars($activeRegistration->TentSize)); ?></td></tr>
			<tr><td valign="top" class="header">Vilka ska bo i tältet</td><td><?php echo nl2br(htmlspecialchars($activeRegistration->TentHousing)); ?></td></tr>
			<tr><td valign="top" class="header">Önskad placering</td><td><?php echo nl2br(htmlspecialchars($activeRegistration->TentPlace)); ?></td></tr>
			<?php 
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
            ?>
			
			
			<tr><td valign="top" class="header">Boendehänsyn</td><td><?php echo $person->getFullHousingComment($current_larp);?></td></tr>
			<tr><td valign="top" class="header">Hälsa</td><td><?php echo $person->HealthComment;?></td></tr>


			<tr><td valign="top" class="header">Annan information</td><td><?php echo nl2br($person->OtherInformation);?></td></tr>
			<tr><td valign="top" class="header">Får visa namn</td><td><?php echo ja_nej($person->HasPermissionShowName)?></td></tr>
			
			<?php if (OfficialType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av funktionär</td><td><?php echo commaStringFromArrayObject($activeRegistration->getOfficialTypes());?></td></tr>
			<?php } ?>
			<?php if (isset($registration)) {?>
			<tr><td valign="top" class="header">Funktionär</td><td><?php echo ja_nej($registration->IsOfficial)?></td></tr>
			<tr><td valign="top" class="header">Medlem</td><td><?php echo ja_nej($registration->isMember())?></td></tr>
			<tr><td valign="top" class="header">Anmäld</td><td><?php echo $registration->RegisteredAt;?></td></tr>
			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $registration->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><?php echo $registration->AmountToPay;?></td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><?php echo $registration->AmountPayed;?></td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><?php echo $registration->Payed;?></td></tr>
			<?php 
			if ($registration->isNotComing()) {
			?>
			<tr><td valign="top" class="header">Avbokad</td><td><?php echo ja_nej($registration->NotComing);?></td></tr>
			<tr><td valign="top" class="header">Återbetalning</td><td><?php echo $registration->RefundAmount;?></td></tr>
			<tr><td valign="top" class="header">Återbetalningsdatum</td><td><?php echo $registration->RefundDate;?></td></tr>
			<?php  }?>
			<?php }?>
		</table>	
		</div>	
		<?php 
    		        echo "<div>\n";
    		        echo "<strong>Karaktärer:</strong><br>\n";
    		        $roles = $person->getRolesAtLarp($current_larp);
    		        echo "<table>";
    		        
    		        foreach($roles as $role) {
                        echo "<tr>";
                        echo "<td>";
                        if ($role->hasImage()) {
                            echo "<img width='30' src='../includes/display_image.php?id=$role->ImageId'/>\n";
                        }
                        echo "</td>";
                        
                        echo "<td>";
                        echo $role->getViewLink();
		                echo $role->getEditLinkPen(true);
                        echo "</td>";
		                echo "<td>$role->Profession</td>";
		                echo "<td>";
		                $role_group = $role->getGroup();
		                if (isset($role_group)) {
		                    echo $role_group->getViewLink();
		                }
		                echo "</td>";
		                echo "<td>";
		                $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
		                if ($larp_role->IsMainRole != 1) {
    		              echo " Sidokaraktär";
    		            }
    		            echo "</td>";
     		            echo "</tr>";
    		        }
    		        echo "</table>";
    		        if (sizeof($roles) > 1) {
    		            echo "<form style='display: inline-block;' action='change_main_role.php' method='post'>";
        		        echo "<input type='hidden' id='PersonId' name='PersonId' value='$person->Id'>";
        		        echo "<input type='submit' value='Ändra vilken som är huvudkaraktär'>";
        		        echo "</form> ";

        		        echo "<form style='display: inline-block;' action='remove_side_role.php' method='post'>";
        		        echo "<input type='hidden' id='PersonId' name='PersonId' value='$person->Id'>";
        		        echo "<input type='submit' value='Ta bort sidokaraktär(er) från lajvet'>";
        		        echo "</form>";
    		        }

    		        echo "<br><br>";
    		        $unregistered_roles = $person->getUnregisteredRolesAtLarp($current_larp);
					if (!empty($unregistered_roles)) {
						echo "<strong>Karaktärer som inte är med på lajvet:</strong><br>\n";
	
						echo "<table>";
						foreach($unregistered_roles as $role) {
							if ($role->IsDead !==1) {
								echo "<tr>";
								echo "<td>";
								echo $role->getViewLink();
								echo "</td>";
								echo "<td>$role->Profession</td>";
								echo "<td>";
								$role_group = $role->getGroup();
								if (isset($role_group)) {
									echo $role_group->getViewLink();
								}
								echo "</td>";
								echo "<td>";
								echo "<a href='logic/add_role.php?id=$role->Id'>Lägg till som sidokaraktär</a>";
								echo "</td>";
								echo "</tr>";
							}
						}
						echo "</table>";
					} else {
						echo "<strong>$person->Name har inga karaktärer som inte är med på lajvet.</strong>";
					}      
					echo "</div>\n";     
    		        
    		        //Epost
    		        $emails = Email::allForPersonAtLarp($person, $current_larp);
    		        if (!empty($emails)) {
    		            echo "<div><b>Meddelanden från arrangörer till $person->Name</b><br>\n";
    		            
    		            $tableId = "mail";
    		            echo "<table id='$tableId' class='data'>";
    		            echo "<tr>".
        		            "<th onclick='sortTable(0, \"$tableId\")'>Ämne</th>".
        		            "<th onclick='sortTable(1, \"$tableId\")'>Bilagor</th>".
        		            "<th onclick='sortTable(2, \"$tableId\")'>Skickat av</th>".
        		            "<th onclick='sortTable(3, \"$tableId\")'>Skickat</th>".
        		            "</tr>\n";
    		            foreach (array_reverse($emails) as $email) {
    		                $senderName = "";
    		                if (isset($email->SenderPersonId)) {
    		                    $sender = Person::loadById($email->SenderPersonId);
    		                    $senderName = $sender->Name;
    		                }
    		                
    		                echo "<tr>";
    		                echo "<td><a href='view_email.php?id=$email->Id'>$email->Subject</a></td>";
    		                
    		                $attachements = $email->attachments();
    		                echo "<td>";
    		                if (!empty($attachements)) echo "<i class='fa-solid fa-paperclip'></i>";
    		                echo "</td>";
    		                echo "<td>$senderName</td>";
    		                echo "<td>$email->SentAt</td>";
    		            }
    		            echo "</table>";
    		            echo "</div>";
    		            
    		            
    		        }
    		        
    		        
    		        
    		        ?>
	</div>


</body>
</html>
