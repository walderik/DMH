<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "insert";
    
    if (isset($_GET['action'])) {
        $operation = $_GET['action'];
    }
    
    if ($operation == 'insert') {
        $role = Role::newWithDefault();
        $role->PersonId = $current_person->Id;
        $role->CreatorPersonId = $current_person->Id;
        if (isset($_GET['type'])) {
            if ($_GET['type'] == "npc") $role->PersonId = NULL;
            if ($role->isNPC($current_larp) && isset($_GET['groupId'])) $role->GroupId = $_GET['groupId'];
        }
    } elseif ($operation == 'update') {
        $role = Role::loadById($_GET['id']);
    } else {
    }
}


if (empty($role)) {
    header('Location: index.php'); // Karaktären finns inte
    exit;
}

if ($role->isPC($current_larp) && !$role->isRegistered($current_larp)) {
    header('Location: index.php'); // Karaktären är inte anmäld
    exit;
}

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);


if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation.php';
?>
	<script>
	
	function setFieldState(isYes) {
		var intrigueDivs = document.getElementsByClassName("intrigue");
		var requiredFields = document.getElementsByClassName("requiredIntrigueField");
		var larpertype = document.getElementsByName("LarperTypeId");
		var wealths = document.getElementsByName("WealthId");
		var religions = document.getElementsByName("ReligionId");
		var placeofresidences = document.getElementsByName("PlaceOfResidenceId");
		var believes = document.getElementsByName("BeliefId");
        if (isYes) {
    		for (var i = 0; i < intrigueDivs.length; i++) {
        		intrigueDivs[i].style.display = "none";
    		}
    		for (var i = 0; i < requiredFields.length; i++) {
        		requiredFields[i].required = false;        		
    		}
    		for (var i = 0; i < larpertype.length; i++) {
        		larpertype[i].required = false;  
    		}
    		for (var i = 0; i < wealths.length; i++) {
        		wealths[i].required = false;  
    		}
    		for (var i = 0; i < religions.length; i++) {
    			religions[i].required = false;  
    		}
    		for (var i = 0; i < placeofresidences.length; i++) {
        		placeofresidences[i].required = false;        		
    		}
    		for (var i = 0; i < believes.length; i++) {
    			believes[i].required = false;        		
    		}
        } else {
    		for (var i = 0; i < intrigueDivs.length; i++) {
        		intrigueDivs[i].style.display = "table-row";
    		}
     		for (var i = 0; i < requiredFields.length; i++) {
        		requiredFields[i].required = true;
    		}
    		for (var i = 0; i < larpertype.length; i++) {
        		larpertype[i].required = true;  
    		}
     		for (var i = 0; i < wealths.length; i++) {
        		wealths[i].required = true;        		
    		}
     		for (var i = 0; i < religions.length; i++) {
     			religions[i].required = true;        		
    		}
    		for (var i = 0; i < placeofresidences.length; i++) {
        		placeofresidences[i].required = true;  
    		}
       		for (var i = 0; i < believes.length; i++) {
    			believes[i].required = true;  
    		}
        }
    }
    
    function handleRadioClick() {
        if (document.getElementById("myslajvare_yes").checked) {
            setFieldState(true);
        } else if (document.getElementById("myslajvare_no").checked) {
            setFieldState(false);
        }
    }
	

	
	</script>


	<div class="content">

		<h1>
		<?php 
		if ($operation == 'update') {
		    echo "Ändra $role->Name";
		} else {
		    echo "Skapa karaktär";
		}    
		 ?>	
		</h1>
		<form action="logic/edit_role_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		
		<table>
 			<tr><td valign="top" class="header">Namn&nbsp;<font style="color:red">*</font></td>
 			<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($role->Name); ?>" size="100" maxlength="250" required></td></tr>
			<tr><td valign="top" class="header">Spelas av</td><td>
			<?php 
			$person = $role->getPerson();
			if (!is_null($person)) {
			    echo $person->getViewLink();
			}
			else {
			    echo "NPC";
			    $assignment = NPC_assignment::getAssignment($role, $current_larp);
			    if ($operation == 'update' && empty($assignment)) {
			        echo "<br><a href='turn_into_pc.php?RoleId=$role->Id'>Gör om till spelarkaraktär<a><br>";
			    }
			}
			?></td></tr>

			<tr><td valign="top" class="header">Grupp</td>
			<td><?php selectionDropDownByArray('GroupId', Group::getAllRegistered($current_larp), false, $role->GroupId); ?></td></tr>

			<?php if ($role->isPC($current_larp)) {?>
			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>
			<?php } ?>
			
			<tr><td valign="top" class="header">Yrke&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="Profession" name="Profession" value="<?php echo htmlspecialchars($role->Profession); ?>"  size="100" maxlength="250" required></td></tr>

			<tr><td valign="top" class="header">Beskrivning&nbsp;<font style="color:red">*</font></td>
			<td><textarea id="Description" name="Description" rows="4" cols="100" maxlength="15000" required><?php echo htmlspecialchars($role->Description); ?></textarea></td></tr>
			<tr><td valign="top" class="header">Beskrivning för gruppen</td>
			<td><textarea id="DescriptionForGroup" name="DescriptionForGroup" rows="4" cols="100" maxlength="15000"><?php echo htmlspecialchars($role->DescriptionForGroup); ?></textarea></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td>
			<td><textarea id="DescriptionForOthers" name="DescriptionForOthers" rows="4" cols="100" maxlength="400"><?php echo htmlspecialchars($role->DescriptionForOthers); ?></textarea></td></tr>

			<?php if (Race::isInUse($current_larp)) {?>
    			<tr><td valign="top" class="header">Ras</td><td>
    			<?php Race::selectionDropdown($current_larp, false, false, $role->RaceId); ?>
    			</td></tr>
    			<tr><td valign="top" class="header">Ras kommentar</td><td><input type="text" id="RaceComment" name="RaceComment" value="<?php echo htmlspecialchars($role->RaceComment); ?>"  size="100" maxlength="250"></td></tr>
			<?php }?>



			<tr><td valign="top" class="header">Bakgrundslajvare&nbsp;<font style="color:red">*</font></td>
			<td>
            	<input type="radio" id="myslajvare_yes" name="NoIntrigue" value="1" onclick="handleRadioClick()" <?php if ($role->isMysLajvare()) echo 'checked="checked"'?>>
            	<label for="myslajvare_yes">Ja</label><br>
            	<input type="radio" id="myslajvare_no" name="NoIntrigue" value="0" onclick="handleRadioClick()"<?php if (!$role->isMysLajvare()) echo 'checked="checked"'?>>
            	<label for="myslajvare_no">Nej</label><br>
			</td></tr>

			<?php if (LarperType::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Typ av lajvare&nbsp;<font style="color:red">*</font></td>
    			<td><?php LarperType::selectionDropdown($current_larp, false, false, $role->LarperTypeId); ?></td></tr>
    			<tr class="intrigue"><td valign="top" class="header">Kommentar till typ av lajvare</td>
    			<td><input type="text" id="TypeOfLarperComment" value="<?php echo htmlspecialchars($role->TypeOfLarperComment); ?>" name="TypeOfLarperComment"  size="100" maxlength="250"></td></tr>
			<?php }?>


			<tr class="intrigue"><td valign="top" class="header">Tidigare lajv</td>
			<td><textarea id="PreviousLarps" name="PreviousLarps" rows="8" cols="100" maxlength="15000"><?php echo htmlspecialchars($role->PreviousLarps); ?></textarea></td></tr>

			<tr class="intrigue"><td valign="top" class="header">Varför befinner sig<br>karaktären på platsen?
			<?php if ($role->isPC($current_larp)) {?>
				&nbsp;<font style="color:red">*</font>
			<?php }?>
			</td>
			<td><textarea 
			<?php if ($role->isPC($current_larp)) {?>
			class="requiredIntrigueField" 
			<?php }?>
			id="ReasonForBeingInSlowRiver" name="ReasonForBeingInSlowRiver" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->ReasonForBeingInSlowRiver); ?></textarea></td></tr>



			<?php if (Religion::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Religion</td><td>
    			<?php Religion::selectionDropdown($current_larp, false, $role->isPC($current_larp), $role->ReligionId); ?>
    			</td></tr>
    			<tr class="intrigue"><td valign="top" class="header">Religion förklaring</td><td><input type="text" id="Religion" name="Religion" value="<?php echo htmlspecialchars($role->Religion); ?>"  size="100" maxlength="250"></td></tr>
			<?php }?>

			<?php if (Belief::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Hur troende</td><td>
    			<?php Belief::selectionDropdown($current_larp, false, $role->isPC($current_larp), $role->BeliefId); ?>
    			</td></tr>
			<?php }?>


			<?php if (Ability::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Kunskaper</td>
    			<td><?php selectionByArray('Ability' , Ability::allActive($current_larp), true, false, $role->getSelectedAbilityIds());?></td></tr>
    			<tr class="intrigue"><td valign="top" class="header">Kunskap förklaring</td><td><input type="text" id="AbilityComment" name="AbilityComment" value="<?php echo htmlspecialchars($role->AbilityComment); ?>"  size="100" maxlength="250"></td></tr>
			<?php }?>
			
			
			<?php if (RoleFunction::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Funktioner</td>
    			<td><?php selectionByArray('RoleFunction' , RoleFunction::allActive($current_larp), true, false, $role->getSelectedRoleFunctionIds());?></td></tr>
    			<tr class="intrigue"><td valign="top" class="header">Funktioner förklaring</td><td><input type="text" id="RoleFunctionComment" name="RoleFunctionComment" value="<?php echo htmlspecialchars($role->RoleFunctionComment); ?>"  size="100" maxlength="400"></td></tr>
			<?php }?>


			<?php if (SuperPowerActive::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Superkraft, aktiv</td>
    			<td><?php selectionByArray('ActiveSuperPowers' , SuperPowerActive::allActive($current_larp), false, true, $role->getSelectedActiveSuperPowerIds());?></td></tr>
 			<?php }?>

			<?php if (SuperPowerPassive::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Superkraft, passiv</td>
    			<td><?php selectionByArray('ActiveSuperPowers' , SuperPowerPassive::allActive($current_larp), true, false, $role->getSelectedPassiveSuperPowerIds());?></td></tr>
 			<?php }?>



			<tr class="intrigue"><td valign="top" class="header">Mörk hemlighet
    			<?php if ($role->isPC($current_larp)) {?>
    				&nbsp;<font style="color:red">*</font>
    			<?php }?>
				</td>
			<td><textarea 
			<?php if ($role->isPC($current_larp)) {?>
				class="requiredIntrigueField" 
			<?php }?>

			id="DarkSecret" name="DarkSecret" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->DarkSecret); ?> </textarea></td></tr>

			<tr class="intrigue"><td valign="top" class="header">Mörk hemlighet - intrig idéer
    			<?php if ($role->isPC($current_larp)) {?>
    				&nbsp;<font style="color:red">*</font>
    			<?php }?>
				</td>
			<td><input 
			<?php if ($role->isPC($current_larp)) {?>
				class="requiredIntrigueField" 
			     <?php }?>
				type="text" id="DarkSecretIntrigueIdeas" name="DarkSecretIntrigueIdeas" value="<?php echo htmlspecialchars($role->DarkSecretIntrigueIdeas); ?>"  size="100" maxlength="250"></td></tr>

			<?php if (isset($larp_role)) { ?>
			<?php if (IntrigueType::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Intrigtyper</td>
    			<td><?php IntrigueType::selectionDropdownRole($current_larp, true, false, $larp_role->getSelectedIntrigueTypeIds());?></td></tr>
			<?php }?>
			
			<tr class="intrigue"><td valign="top" class="header">Intrigidéer</td>
			<td><textarea id="IntrigueIdeas" name="IntrigueIdeas" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($larp_role->IntrigueIdeas); ?></textarea></td></tr>

			<?php } ?>
			<tr class="intrigue"><td valign="top" class="header">Saker karaktären inte vill spela på</td>
			<td><input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo htmlspecialchars($role->NotAcceptableIntrigues); ?>"  size="100" maxlength="250"></td></tr>

			<tr class="intrigue"><td valign="top" class="header">Relationer med andra</td>
			<td><textarea id="CharactersWithRelations" name="CharactersWithRelations" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->CharactersWithRelations); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->OtherInformation); ?></textarea></td></tr>

			<?php if (Wealth::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Rikedom
    			<?php if ($role->isPC($current_larp)) {?>
    				&nbsp;<font style="color:red">*</font>
    			<?php }?>
    			</td>
    			<td><?php Wealth::selectionDropdown($current_larp, false, $role->isPC($current_larp), $role->WealthId); ?></td></tr>
			<?php } ?>
			
			<tr class="intrigue"><td valign="top" class="header">Var är karaktären född?
    			<?php if ($role->isPC($current_larp)) {?>
    				&nbsp;<font style="color:red">*</font>
    			<?php }?>
				</td>
			<td><input 
			<?php if ($role->isPC($current_larp)) {?>
    			class="requiredIntrigueField" 
    			<?php }?>
    			type="text" id="Birthplace" name="Birthplace" value="<?php echo htmlspecialchars($role->Birthplace); ?>"  size="100" maxlength="250"></td></tr>

			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
    			<tr class="intrigue"><td valign="top" class="header">Var bor karaktären?
    			<?php if ($role->isPC($current_larp)) {?>
    				&nbsp;<font style="color:red">*</font>
    			<?php }?>
    			</td>
    			<td><?php
    			PlaceOfResidence::selectionDropdown($current_larp, false, $role->isPC($current_larp), $role->PlaceOfResidenceId);
                ?></td></tr>
            <?php } ?>
           
			<tr><td valign="top" class="header">Död&nbsp;<font style="color:red">*</font></td>
			<td>
				<input type="radio" id="IsDead_yes" name="IsDead" value="1" <?php if ($role->IsDead == 1) echo 'checked="checked"'?>> 
    			<label for="IsDead_yes">Ja</label><br> 
    			<input type="radio" id="IsDead_no" name="IsDead" value="0" <?php if ($role->IsDead == 0) echo 'checked="checked"'?>> 
    			<label for="IsDead_no">Nej</label>
			</td></tr>


		</table>		
			<input type="submit" value="Spara">

			</form>

	</div>

<script>

	<?php 
	if($role->isMysLajvare()) {
	    echo 'setFieldState(true);';
	} else {
	    echo 'setFieldState(false);';
	}
	?>



</script>
</body>
</html>
