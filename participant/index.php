<?php
require 'header.php';

$_SESSION['navigation'] = Navigation::PARTICIPANT;
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

include 'navigation.php';
?>

	<script>
function morelessFunction() {
  var moreText = document.getElementById("more");
  var morelesstxt = document.getElementById("morelesstxt");

  if (moreText.style.display === "inline") {
    morelesstxt.innerHTML = "Visa fler &nbsp;<i class='fa-solid fa-chevron-down'></i>"; 
    moreText.style.display = "none";
  } else {
    morelesstxt.innerHTML = "Visa färre &nbsp;<i class='fa-solid fa-chevron-up'></i>"; 
    moreText.style.display = "inline";
  }
}

function openTab(evt, tabName) {
	  // Declare all variables
	  var i, tabcontent, tablinks;

	  // Get all elements with class="tabcontent" and hide them
	  tabcontent = document.getElementsByClassName("tabcontent");
	  for (i = 0; i < tabcontent.length; i++) {
	    tabcontent[i].style.display = "none";
	  }

	  // Get all elements with class="tablinks" and remove the class "active"
	  tablinks = document.getElementsByClassName("tablinks");
	  for (i = 0; i < tablinks.length; i++) {
	    tablinks[i].className = tablinks[i].className.replace(" active", "");
	  }

	  // Show the current tab, and add an "active" class to the button that opened the tab
	  document.getElementById(tabName).style.display = "block";
	  evt.currentTarget.className += " active";
	}


</script>


	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-user"></i>

			Aktiv person<?php help_icon("En person är en verklig person som är deltagare på ett lajv. På ett konto kan du handera flera personer, tex en familj."); ?>
		</div>
	
	<?php 
	$items = array();
	
	$persons = Person::getPersonsForUser($current_user->Id);
	if (!empty($persons)) {
	    if (empty($current_person)) {
	        // ensure the pointer is at the first element
	        reset($persons);
	        
	        // get the value of the element being pointed to
	        $current_person = current($persons);
	        $_SESSION['PersonId'] = $current_person->Id;
	    }
	    
	    if (isset($current_larp)) {
    	    $groups = Group::getGroupsForPerson($current_person->Id, $current_larp->CampaignId);
    	    $roles = $current_person->getRoles($current_larp);
	    } else {
	        $groups = array();
	        $roles = array();
	    }
	    
	    //Först den aktiva personen
	    $item = "<div class='itemcontainer'>";
	    $item .= "<input type='radio' id='PersonId_$current_person->Id' name='PersonId' value='$current_person->Id'";
	    $item .=  'checked="checked"';
	    $item .=  "> ";
	    $item .=  "<label class='itemname' for='PersonId_$current_person->Id'><a href='view_person.php?id=" . $current_person->Id . "'>$current_person->Name</a>";
	    if($current_person->isNeverRegistered() && (!isset($roles) or count($roles) == 0) && (!isset($groups) or count($groups) == 0)) {
	        $item .=  "&nbsp;<a href='logic/delete_person.php?id=" . $current_person->Id . "'><i class='fa-solid fa-trash' title='Ta bort deltagare'></i></a>";
	    }
	    $item .= "</label>";
	    $item .=  "</div>";
	    $items[] = $item;
	    
    	//Sen alla andra
    	foreach ($persons as $person) {
    	    if ($person->Id == $current_person->Id) continue;
    	    $item = "<div class='itemcontainer'>";
     	    $item .= "<form method='post' action='logic/select_person.php'>";
     	    $item .= "<input type='radio' onclick='submit()' id='PersonId_$person->Id' name='PersonId' value='$person->Id> ";
     	    $item .= "<label for='PersonId_$person->Id'>$person->Name</label>";
     	    $item .= "</form>";
     	    $item .=  "</div>";
     	    $items[] = $item;
    	}
	}
	
	
    $item = "<div class='itemcontainer'>";
	$item .= "<a href='person_form.php'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-user'></i> Lägg till person</a>";
	$item .= "</div>";
    $items[] = $item;
    
    if (sizeof($items) == 1) {
        echo array_values($items)[0];
    } else {
        $first = true;
        foreach ($items as $item) {
            echo $item;
            if ($first) {
                $first = false;
                echo "<span id='more'>";
            }
        }
        echo "</span>";
        echo "<div class='showmorecontainer'><button id='morelesstxt' class='showmore unstyledbutton' type='button' onclick='morelessFunction()'>Visa fler &nbsp;<i class='fa-solid fa-chevron-down'></i></div></div></button></div>	</div>";
    }
	?>

	<?php 
	if (empty($current_person)) exit;
	?>	
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-shield-halved"></i>
			
			Aktivt lajv<?php help_icon("Det lajv som du just nu tittar på eller anmäler dig till. Är du intresserad av ett annat lajv får du välja ett annat lajv."); ?>
		</div>
		<?php if (isset($current_larp)) {
		  echo "<div class='itemcontainer itemname'>$current_larp->Name</div>";


    		if ($current_larp->isEnded()) {
    		    echo "<div class='larpinfo'><span>Lajvet är över.</span>";
    		    help_icon("Hoppas att du hade roligt. Gå gärna och och skriv vad som hände.");
    		    echo "</div>";
    		}elseif ($current_larp->isPastLatestRegistrationDate() && !$current_larp->mayRegister()) {
    		    echo "<div class='larpinfo'><span>Sista anmälningsdag har passerat</span>";
    		    echo "</div>";
    		} elseif ($current_larp->isPastLatestRegistrationDate()) {
    		    echo "<div class='larpinfo'><span>Sista anmälningsdag har passerat</span>";
    		    help_icon("Du kan göra en anmälan så att du hamnar på reservlistan. Arrangörerna väljer vilka som plockas in. Vilken plats man har på reservlistan spelar ingen roll.");
    		    echo "</div>";
    		} elseif ($current_larp->isFull() && $current_larp->RegistrationOpen == 0) {
    		    
    		    echo "<div class='larpinfo'><span>Anmälan är stängd</span>";
    		    echo "</div>";
    		} elseif ($current_larp->isFull() || Reserve_Registration::isInUse($current_larp)) {
    		    
    		    echo "<div class='larpinfo'><span>Lajvet är fullt.</span>";
    		    help_icon("Du kan göra en anmälan så att du hamnar på reservlistan. Om någon annan avbokar kan du kanske få en plats. Arrangörerna väljer vilka som plockas in. Vilken plats man har på reservlistan spelar ingen roll.");
    		    echo "</div>";
    		} elseif ($current_larp->RegistrationOpen == 0) {
    		    
    		    echo "<div class='larpinfo'><span>Anmälan inte öppen</span>";
    		    help_icon("Du kan registrera deltagare, grupper och karaktärer i väntan på att anmälan ska öppna. OBS! En karaktär kan bara bli medlem i en grupp om den är anmäld. Så det får du editera efter att anmälan har öppnat. Men övrig information kan du fylla i så länge.");
    		    echo "</div>";
    		}
		}
		
		
		?>
		<div class='itemcontainer'><a href="choose_larp.php">Välj lajv</a></div>
	</div>
	
	<?php 
	if (empty($current_larp)) exit;
	?>
	
	<?php 
	$registration = $current_person->getRegistration($current_larp);
	if (empty($registration)) {
	    if ($current_larp->mayRegister() && !empty($roles)) echo "<div class='center'><a href='person_registration_form.php'><button class='button-18'>Anmäl</button></a></div>";
	} else {
	    echo "<div class='tab'>";
	    echo "<button class='tablinks' onclick='openTab(event, \"Characters\")'>Karaktärer</button>";
	    echo "<button class='tablinks' ";
	    if (!$registration->hasSpotAtLarp()) echo "id='defaultOpen' ";
	    echo "onclick='openTab(event, \"Registration\")'>Anmälan</button>";
	    if ($registration->hasSpotAtLarp()) echo "<button class='tablinks' id='defaultOpen' onclick='openTab(event, \"BeforeLARP\")'>Inför lajvet</button>";
	    echo "</div>";
	    
	    
	}
	?>
	
	<?php 
	if (!empty($registration)) {
	    
	?>
	<div id="Characters" class="tabcontent">
	<?php 
	}
	?>
	

		<div class='itemselector'>
		<div class="header">
			<i class="fa-solid fa-person"></i>
			Karaktärer<?php help_icon("Karaktärer är fiktiva personer som man spelar i en kampanj. På olika lajv i kampanjen kan man spela en eller fler karaktärer."); ?>
		</div>
		<?php     		        
		if (!empty($roles)) {
    		foreach ($roles as $role) {
    		    echo "<div class='itemcontainer borderbottom'>";
        		//Namn på karaktären
        		echo "<div class='itemname'>";
        		echo $role->getViewLink();
 
        		if (!$role->isRegistered($current_larp) || $role->userMayEdit($current_larp)) {
        		    echo " " . $role->getEditLinkPen(false);
        		}
        		
        		if($role->isNeverRegistered()) {
        		    echo "&nbsp;<a href='logic/delete_role.php?id=" . $role->Id . "'><i class='fa-solid fa-trash' title='Ta bort karaktär'></i></a>";
        		}
        		//Karaktärsblad
        		if (!empty($registration) && $registration->SpotAtLARP==1) {
        		    echo " <a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
        		}
        		echo "<br>";
        		echo "</div>";
        		
        		if ($role->hasImage()) {
        		    echo "<br>";
        		    echo "<img width='80%' style='max-width: 400px;' src='../includes/display_image.php?id=$role->ImageId'/ >\n";
        		    echo "</a>";
        		    echo "<br>";
        		    echo "<a href='../common/logic/rotate_image.php?id=$role->ImageId'><i class='fa-solid fa-rotate-right' title ='Rotera bild'></i> Rotera bild</a><br>";
        		    echo "<a href='logic/delete_image.php?id=$role->Id&type=role'><i class='fa-solid fa-trash' title='Ta bort bild'></i>  Ta bort bild</a>\n";
        		}
        		else {
        		    echo "<br>";
        		    echo "<img width='100' src='../images/man-shape.png' / >\n";
        		    echo "<br>";
        		    echo "<a href='upload_image.php?id=$role->Id&type=role'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i> Ladda upp bild</a> \n";
        		}
        		echo "<br><br>";
        		
        		
        		
        		
        		
        		
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
    echo "<div class='center'><a href='role_form.php?action=insert'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-person'></i> &nbsp;Skapa karaktär</button></a></div>";
    echo "</div>\n";
		
		
		?>
		

	
	
	<div class='itemselector'>
		<div class="header">
			<i class="fa-solid fa-people-group"></i>
			Grupper<?php help_icon("Grupper är en samling av karaktärer som hör ihop i en kampanj. Tex, en familj eller en organisation. Gruppleadren den som är kontaktperson gentemot arrangörerna."); ?>
		</div>

		<?php 
	  if (!empty($groups)) {
	      foreach ($groups as $group) {
		  echo "<div class='itemcontainer'>";
		  echo "<div class='itemname'>";
		  echo  $group->getViewLink();
	      if($group->isNeverRegistered()) {
	          echo "&nbsp;<a href='logic/delete_group.php?id=" . $group->Id . "'><i class='fa-solid fa-trash' title='Ta bort grupp'></i></a>";
	      }
		  echo "</div>";
		  if (!$group->isRegistered($current_larp)) echo "<div class='center'><a href='group_registration_form.php?id=$group->Id'><button class='button-18'>Anmäl gruppen</button></a></div>";
		  if ($group->hasImage()) {
		      echo "<br><img width='30' src='../includes/display_image.php?id=$group->ImageId'/>\n";
		      echo " <a href='../common/logic/rotate_image.php?id=$group->ImageId'><i class='fa-solid fa-rotate-right'></i></a> <a href='logic/delete_image.php?id=$group->Id&type=group'>Ta bort bild</a></td>\n";
		  }
		  else {
		      echo "<br><a href='upload_image.php?id=$group->Id&type=group'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i> Ladda upp bild</a>\n";
		  }
		  if ($group->isRegistered($current_larp) && $current_larp->isEnded()) {
		      echo "<br><a href='larp_report_form.php?groupId=$group->Id'>Vad hände?</a> ".
		  		      showParticipantStatusIcon($group->hasRegisteredWhatHappened($current_larp), "Inte noterat vad som hände");
		  }
		  
		  
		  echo "</div>";
		}
		?>

	
	<?php 
	}
    echo "<div class='center'><a href='group_form.php?action=insert'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-people-group'></i> &nbsp;Skapa grupp</button></a><div class='center'>";
    ?>

	</div>
	
	</div>
	</div>

	
	
	<?php 
	if (!empty($registration)) {
	    
	?>
	</div>
	
	<div id="Registration" class="tabcontent">
	<div class='itemselector'>
		<div class="header">
			<i class="fa-solid fa-file"></i>
			Anmälan <?php help_icon("Här finns all information om anmälan för $current_person->Name till $current_larp->Name."); ?>
		</div>
		<div class='itemcontainer'>
	  <?php 
	   echo "<div class='itemname'>Status</div>";
        if ($person->isReserve($current_larp)) {
            echo "Reservlista";
        }
        else if ($person->isNotComing($current_larp)) {
            echo "Avbokad";
        }
        else {
            echo "Anmäld<br>";
            echo "<a href='view_registration.php?id=$person->Id'>Visa anmälan</a>";
        }
        echo "</div>";
        
        if (!$current_person->isNotComing($current_larp)) {

            
            //Utvärdering
            if ($current_larp->isEnded() && !(AccessControl::hasAccessLarp($current_person, $current_larp))) {
       
                echo "<div class='itemcontainer'>";
                echo "<div class='itemname'>Utvärdering</div>";
                if ($current_larp->isEvaluationOpen()) {
                    if ($registration->hasDoneEvaluation()) {
                        echo showStatusIcon(true);
                        echo "Utvärderingen är inlämnad";
                    } elseif ($current_larp->useInternalEvaluation()) {
                        echo showParticipantStatusIcon(false, "Utvärderingen är inte gjord");
                        echo "<br><a href='evaluation.php?PersonId=$current_person->Id'>Gör utvärdering";
                    } else {
                        echo "<a target='_blank' href='$current_larp->EvaluationLink'>Gör utvärdering (extern länk)";
                    }
                } else echo "Utvärderingen öppnar $current_larp->EvaluationOpenDate";
                echo "</div>\n";
                
            }
            
            //Ansvarig vuxen
            if ($current_person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian)  {
                echo "<div class='itemcontainer'>";
                echo "<div class='itemname'>Ansvarig vuxen</div>";
                if (empty($registration->GuardianId)) {
                    echo showParticipantStatusIcon(false, "Du saknar ansvarig vuxen");
                    echo "<br><a href='input_guardian.php?PersonId=$person->Id'>Ange ansvarig vuxen</a>";
                }
                else {
                    echo "<br>".$registration->getGuardian()->Name;
                }
                echo "</div>\n";                            
            }
            
            
            echo "<div class='itemcontainer'>";
            echo "<div class='itemname'>Betalat</div>";
            if ($current_person->hasPayed($current_larp)) {
                echo "Betalning mottagen";
            } else {
                echo showParticipantStatusIcon(false, "Du har inte betalat");
                $invoice = $registration->getInvoice();
                if (!empty($invoice)) {
                    echo "<br>Avgiften är del av en faktura som betalas av $invoice->Recipient";
                } else {
                    $campaign = $current_larp->getCampaign();
                    echo "<br>Betala <b>$registration->AmountToPay</b> SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>. Betalas senast ".$registration->paymentDueDate();
                    if (!empty($campaign->SwishNumber)) {
                        if ($isMob) echo "<br><a href = ".Swish::getSwishLink($registration, $campaign). "><button ><img style='padding:2px'  width='20' src='../images/Swish Logo.png'><span style='vertical-align: top'> Betala med swish </span></button></a>";
                        else echo "<br><img width='200' src='../includes/display_image.php?Swish=1&RegistrationId=$registration->Id&CampaignId=$campaign->Id'/>\n";
                    }
                    
                }
            }
            echo "</div>\n";

            
            echo "<div class='itemcontainer'>";
            echo "<div class='itemname'>Medlem</div>";
            
            if ($registration->isMember()) {
                echo "Medlemsskap betalat";
            } else {
                echo showParticipantStatusIcon(false, "$current_person->Name är inte medlem i Berghems Vänner");
                $currentYear = date("Y");
                $larpYear = substr($current_larp->StartDate, 0, 4);
                if ($currentYear == $larpYear) {
                    echo "<br><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>Betala medlemsavgiften</a>";
                } else {
                    echo "<br>Medlemsavgiften kan inte betalas än.";
                }
                
            }

            echo "</div>\n";
            
            echo "<div class='itemcontainer'>";
            echo "<div class='itemname'>Plats på lajvet</div>";
            
            if ($registration->hasSpotAtLarp()) {
                echo "$current_user->Name har plats på lajvet";
            } else {
                echo showParticipantStatusIcon(false, "$current_person->Name har inte fått en plats på lajvet");
            }
            echo "</div>";
            
            echo "<div class='itemcontainer'>";
            echo "<div class='itemname'>Boende</div>";

            if ($current_larp->isHousingReleased()) {
                $house = House::getHouseAtLarp($current_person, $current_larp);
                if (empty($house)) {
                    echo "Inget tilldelat";
                } else {
                    echo "<a href='view_house.php?id=$house->Id'>$house->Name</a>";
                }
            } else {
                echo "Inte klart än";
            }
            echo "</div>";

            
            
            echo "<div class='itemcontainer'>";
            echo "<div class='itemname'>Anmälda karaktärer</div>";
            
            $roles = $current_person->getRoles($current_larp);
            foreach ($roles as $role) {
                echo $role->getViewLink();
                echo "<br>";
                if ($role->isApproved()) echo "Karaktären är godkänd";
                else showParticipantStatusIcon(false, "$role->Name är inte godkänd");
                echo "<br><br>";
            }
            
            echo "</div>";

            $groups = $current_person->getGroups($current_larp);
            
            if (!empty($groups)) {
        
                echo "<div class='itemcontainer'>";
                echo "<div class='itemname'>Anmälda grupper</div>";
                
                foreach ($groups as $group) {
                    echo $group->getViewLink();
                    echo "<br>";
                    if ($group->isApproved()) echo "Karaktären är godkänd";
                    else showParticipantStatusIcon(false, "$group->Name är inte godkänd");
                    echo "<br><br>";
                }
                
                echo "</div>";
            }
            
        }

        ?>
		</div>
	</div>
	
	<?php } ?>

	<?php if (!empty($registration) && $registration->hasSpotAtLarp()) { ?>
	<div id="BeforeLARP" class="tabcontent">
	
	
	
	
  		<h3>Inför lajvet</h3>
  		<p>Tokyo is the capital of Japan.</p>
	</div>
	<?php } ?>





</body>


<script>
// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>