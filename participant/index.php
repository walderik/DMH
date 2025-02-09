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

	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }
	  if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }
	  ?>

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
	        //Sätt current_person till dne person som har lägst id
	        foreach ($persons as $person) {
	            if (empty($current_person)) $current_person = $person;
	            elseif ($person->Id  < $current_person->Id) $current_person = $person;
	        }
	        $_SESSION['PersonId'] = $current_person->Id;
	    }
	    
	    if (isset($current_larp)) {
    	    $groups = Group::getGroupsForPerson($current_person->Id, $current_larp->CampaignId);
    	    $roles = $current_person->getRoles($current_larp);
    	    $registration = $current_person->getRegistration($current_larp);
    	    $reserve_registration = $current_person->getReserveRegistration($current_larp);
	    } else {
	        $groups = array();
	        $roles = array();
	        $registration = null;
	        $reserve_registration = null;
	    }
	    
	    //Först den aktiva personen
	    $item = "<div class='itemcontainer'>";
	    $item .= "<input type='radio' id='PersonId_$current_person->Id' name='PersonId' value='$current_person->Id'";
	    $item .=  'checked="checked"';
	    $item .=  "> ";
	    $item .=  "<label class='itemname' for='PersonId_$current_person->Id'>" . $current_person->getViewLink();
	    if($current_person->isNeverRegistered() && (!isset($roles) or count($roles) == 0) && (!isset($groups) or count($groups) == 0)) {
	        $item .=  "&nbsp;<a href='logic/delete_person.php?id=" . $current_person->Id . "'><i class='fa-solid fa-trash' title='Ta bort deltagare'></i></a>";
	    }
	    $item .= "</label>";
	    if (empty($registration) && empty($reserve_registration) && !empty($current_larp) && $current_larp->mayRegister() && !empty($roles)) $item .= " &nbsp;<a href='person_registration_form.php'><button class='button-18'>Anmäl</button></a>";
	    if (!empty($reserve_registration)) $item .=  " (Står på reservlistan)";    
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

	if (!empty($registration)) {
	    echo "<div class='tab'>";
	    echo "<button class='tablinks' onclick='openTab(event, \"Characters\")'>Karaktärer</button>";
	    
	    echo "<button class='tablinks' ";
	    if (!$registration->hasSpotAtLarp()) echo "id='defaultOpen' ";
	    echo "onclick='openTab(event, \"Registration\")'>Anmälan</button>";
	    
	    if ($registration->hasSpotAtLarp()) echo "<button class='tablinks' ";
	    if ($registration->hasSpotAtLarp() && !$current_larp->isEnded()) echo "id='defaultOpen' ";
	    if ($registration->hasSpotAtLarp()) echo "onclick='openTab(event, \"BeforeLARP\")'>Inför lajvet</button>";
	    
	    if ($registration->hasSpotAtLarp() && $current_larp->isEnded()) echo "<button class='tablinks' id='defaultOpen' onclick='openTab(event, \"AfterLARP\")'>Efter lajvet</button>";
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
        		echo "<br>";
        		echo "</div>";
        		if ($role->isApproved()) {
        		    echo "Karaktären är godkänd.<br>";
         		}
        		
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
        		    $role_group_name = $role_group->getViewLink();
        		}
        		elseif (isset($role_group)) {
        		    $role_group_name = "$role_group->Name";
        		    if (!$role_group->isRegistered($current_larp)) $role_group_name .= " <a href='group_registration_form.php?id=$role_group->Id'><button class='button-18'>Anmäl gruppen</button></a>";
        		    
        		}
        		echo $role_group_name;
        		echo "<br>";
        		
        		//Specialkunskaper
        		if (Magic_Magician::isMagician($role)) {
        		    $magician = Magic_Magician::getForRole($role);
        		    echo "<a href='view_magician.php?id=$role->Id'>Magiker</a> ";
        		    echo "<a href='magic_magician_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för $role->Name'></i></a> ";
         		    echo "<br>";
        		}
        		if (Alchemy_Supplier::isSupplier($role)) {
        		    $supplier = Alchemy_Supplier::getForRole($role);
        		    echo "<a href='view_alchemy_supplier.php?id=$role->Id'>Löjverist</a> ";
        		    echo "<a href='alchemy_supplier_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Lövjeristblad för $role->Name'></i></a> ";
    		        echo "<br>";
        		}
        		if (Alchemy_Alchemist::isAlchemist($role)) {
        		    $alchemist = Alchemy_Alchemist::getForRole($role);
        		    echo "<a href='view_alchemist.php?id=$role->Id'>Alkemist</a> ";
        		    echo "<a href='alchemy_alchemist_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för $role->Name'></i></a> ";
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
		  echo "<div class='itemcontainer borderbottom'>";
		  echo "<div class='itemname'>";
		  echo  $group->getViewLink();
	      if($group->isNeverRegistered()) {
	          echo "&nbsp;<a href='logic/delete_group.php?id=" . $group->Id . "'><i class='fa-solid fa-trash' title='Ta bort grupp'></i></a>";
	      }
	      if ($current_person->isGroupLeader($group) && (!$group->isRegistered($current_larp) || $group->userMayEdit($current_larp))) {
	          echo " " . $group->getEditLinkPen(false);
	      }
	      echo "</div>";
		  if (!$group->isRegistered($current_larp)) echo "<div class='center'><a href='group_registration_form.php?id=$group->Id'><button class='button-18'>Anmäl $group->Name</button></a></div>";
		  if ($group->isApproved()) {
		      echo "Gruppen är godkänd.<br>";
		  }
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
	   if ($current_person->isReserve($current_larp)) {
            echo "Reservlista";
        }
        else if ($current_person->isNotComing($current_larp)) {
            echo "Avbokad";
        }
        else {
            echo "Anmäld<br>";
            echo "<a href='view_registration.php?id=$current_person->Id'>Visa anmälan</a>";
        }
        echo "</div>";
        
        if (!$current_person->isNotComing($current_larp)) {

            
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
                    echo "<br>Betala <b>$registration->AmountToPay</b> SEK till $campaign->Bankaccount.<br>Ange referens: <b>$registration->PaymentReference</b>.<br>Betalas senast ".$registration->paymentDueDate();
                    if (!empty($campaign->SwishNumber)) {
                        if ($isMob) echo "<br><button onclick='doSwish()'><img style='padding:2px'  width='20' src='../images/Swish Logo.png'><span style='vertical-align: top'> Betala med swish </span></button>";
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
                echo showParticipantStatusIcon(false, "Inte medlem i Berghems Vänner");
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
                echo "$current_person->Name har plats på lajvet";
            } else {
                echo showParticipantStatusIcon(false, "Inte fått en plats på lajvet");
            }
            echo "</div>";
            
            echo "<div class='itemcontainer'>";
            echo "<div class='itemname'>Anmälda karaktärer</div>";
            
            $roles = $current_person->getRolesAtLarp($current_larp);
            foreach ($roles as $role) {
                echo $role->getViewLink();
                if (sizeof($roles) > 1 && $role->isMain($current_larp)) echo " (Huvudkaraktär)";
                echo "<br>";
                if ($role->isApproved()) echo "Karaktären är godkänd";
                else echo showParticipantStatusIcon(false, "$role->Name är inte godkänd");
                echo "<br><br>";
            }
            
            echo "</div>";

            $groups = $current_person->getAllRegisteredGroups($current_larp);
            
            if (!empty($groups)) {
        
                echo "<div class='itemcontainer'>";
                echo "<div class='itemname'>Anmälda grupper</div>";
                
                foreach ($groups as $group) {
                    echo $group->getViewLink();
                    echo "<br>";
                    echo "<a href = 'view_group_registration.php?id=$group->Id'>Visa anmälan</a><br>";
                    if ($group->isApproved()) echo "Gruppen är godkänd";
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
	
	
	<?php 
	$registered_roles = $current_person->getRolesAtLarp($current_larp);
	
	
	echo "<div class='itemselector'>";
	echo "<div class='header'>";
	echo "<i class='fa-solid fa-house'></i> Boende";
	echo "</div>";
	echo "<div class='itemcontainer'>";
	
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
	echo "</div>";
	

    echo "<div class='itemselector'>";
    echo "<div class='header'>";
    echo "<i class='fa-solid fa-scroll'></i> Intriger";
    echo "</div>";
        
        if ($current_larp->isIntriguesReleased()) {
            foreach ($registered_roles as $role) {
            echo "<div class='itemcontainer'>";
            echo $role->getViewLink();
            $group = $role->getGroup();
            if (isset($group)) echo "<br>". $group->getViewLink();
            echo "</div>";
        }

        } else {
            echo "<div class='itemcontainer'>";
            echo "Intrigerna är inte klara än.";
            echo "</div>";
        }
        echo "</div>";
            
	?>
	
	

	<?php 
    	//Karaktärer med specialkunskaper

        
    	foreach ($registered_roles as $role) {
        	if (Magic_Magician::isMagician($role)) {
        	    echo "<div class='itemselector'>";
        	    echo "<div class='header'>";
        	    echo "<a href='view_magician.php?id=$role->Id'><i class='fa-solid fa-wand-sparkles'></i> Magiker $role->Name</a>";
        	    echo " <a href='magic_magician_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för $role->Name'></i></a> ";
        	    echo "</div>";
        	    
        	    echo "<div class='itemcontainer'>";
        	    $magician = Magic_Magician::getForRole($role);
        	    if ($magician->StaffApproved && $magician->hasDoneWorkshop())  echo "Allt godkänt";
        	    else {
        	        if (!$magician->StaffApproved) {
        	            echo showParticipantStatusIcon(false, "Magifokus är inte godkänt");
        	        }
        	        if (!$magician->hasDoneWorkshop()) {
        	            echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om magi");
        	        }
        	    }
        	    echo "</div>";
        	    echo "</div>";
        	}
        	if (Alchemy_Supplier::isSupplier($role)) {
        	    echo "<div class='itemselector'>";
        	    echo "<div class='header'>";
        	    echo "<a href='view_alchemy_supplier.php?id=$role->Id'><i class='fa-solid fa-leaf'></i> Löjverist $role->Name</a>";
        	    echo " <a href='alchemy_supplier_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Lövjeristblad för $role->Name'></i></a> ";
        	    echo "</div>";
        	    
 
        	    echo "<div class='itemcontainer'>";
        	    $supplier = Alchemy_Supplier::getForRole($role);
        	    $supplierLastDay = $current_larp->getLastDayAlchemySupplier();
        	    if ($supplier->allAmountOfIngredientsApproved($current_larp) &&
        	        $supplier->hasDoneWorkshop() &&
        	        $supplier->hasIngredientList($current_larp)) {
        	            echo "Allt godkänt";
        	            if (!empty($supplierLastDay) && $current_larp->isAlchemySupplierInputOpen()) echo "<br>Du kan lägga till ingredienser fram till den $supplierLastDay.<br>";
    	        } else {
    	            if (!$supplier->hasIngredientList($current_larp)) {
    	                echo showParticipantStatusIcon(false,"Du har ingen ingredienslista");
    	                if (!empty($supplierLastDay) && $current_larp->isAlchemySupplierInputOpen()) echo "<br>Sista dag att mata in ingredienser är $supplierLastDay.<br>";
    	            }
    	            if (!$supplier->allAmountOfIngredientsApproved($current_larp)) {
    	                echo showParticipantStatusIcon(false,"Antalet ingredienser är ännu inte godkänt");
    	                if (!empty($supplierLastDay) && $current_larp->isAlchemySupplierInputOpen()) echo "<br>Du kan ändra fram till den $supplierLastDay eller tills det är godkänt.<br>";
    	            }
    	            if (!$supplier->hasDoneWorkshop()) {
    	                echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om lövjeri");
    	            }
    	        }
    	        echo "</div>";
    	        echo "</div>";
        	}
        	if (Alchemy_Alchemist::isAlchemist($role)) {
        	    echo "<div class='itemselector'>";
        	    echo "<div class='header'>";
        	    echo "<a href='view_alchemist.php?id=$role->Id'><i class='fa-solid fa-flask'></i> Alkemist $role->Name</a>";
        	    echo " <a href='alchemy_alchemist_sheet.php?id=$role->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för $role->Name'></i></a> ";
        	    echo "</div>";
        	    
        	    echo "<div class='itemcontainer'>";
        	    
        	    $recipes = $alchemist->getRecipes(false);
        	    $alchemistLastDay = $current_larp->getLastDayAlchemy();
        	    if ($alchemist->recipeListApproved() && $alchemist->hasDoneWorkshop() &&
        	        !empty($recipes)) {
        	            echo "Allt godkänt";
        	            if (!empty($alchemistLastDay) && $current_larp->isAlchemyInputOpen()) echo "<br>Du kan skapa/önska recept fram till den $alchemistLastDay.<br>";
        	            
    	        } else {
    	            if (empty($recipes)) {
    	                echo showParticipantStatusIcon(false, "Din receptlista är tom");
    	                if (!empty($alchemistLastDay) && $current_larp->isAlchemyInputOpen()) echo "<br>Sista dag att skapa/önska recept är $alchemistLastDay.<br>";
    	            }
    	            if (!$alchemist->recipeListApproved()) {
    	                echo showParticipantStatusIcon(false,"Din receptlista är inte godkänd, än");
    	                if (!empty($alchemistLastDay) && $current_larp->isAlchemyInputOpen()) echo "<br>Sista dag att skapa/önska recept är $alchemistLastDay.<br>";
    	            }
    	            if (!$alchemist->hasDoneWorkshop()) {
    	                echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om alkemi");
    	            }
    	        }
    	        echo "</div>";
    	        echo "</div>";
        	}
        	if (Vision::hasVisions($current_larp, $role) && $current_larp->isIntriguesReleased()) {
        	    echo "<div class='itemselector'>";
        	    echo "<div class='header'>";
        	    echo "<i class='fa-solid fa-eye'></i> Syner för $role->Name";
        	    echo "</div>";
        	    
        	    echo "<div class='itemcontainer'>";
        	    
        	    echo "<a href='view_visions.php?id=$role->Id'>Syner</a> ";
        	    echo "</div>";
        	    echo "</div>";
        	}
        	
    	}
    	
    	
    	
    	//NPC'er
    	$npcs = NPC::getReleasedNPCsForPerson($current_person, $current_larp);
    	if (isset($npcs) && count($npcs) > 0) {
    	    
    	    echo "<div class='itemselector'>";
    	    echo "<div class='header'>";
    	    echo "<i class='fa-solid fa-person'></i> NPC";
    	    echo "</div>";
    	    

    	    foreach ($npcs as $npc)  {
    	        echo "<div class='itemcontainer'>";
    	        echo "<div class='itemname'><a href='view_npc.php?id=$npc->Id'>$npc->Name</a></div>";
    	        
    	        
    	        if ($npc->hasImage()) {
    	            echo "<img width='30' src='../includes/display_image.php?id=$npc->ImageId'/>\n";
    	            echo "<a href='logic/delete_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-trash' title='Ta bort bild'></i></a>\n";
    	        }
    	        else {
    	            echo "<a href='upload_image.php?id=$npc->Id&type=npc'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
    	        }
    	        
    	        
    	        if ($npc->IsInGroup()) {
    	            $npc_group = $npc->getNPCGroup();
    	            echo "<br><a href='view_npc_group.php?id=$npc->NPCGroupId'>$npc_group->Name</a>";
    	        }
    	        echo "</div>";
    	        
    	        
    	    }
    	    echo "</div>";
    	    
    	    
    	}
    	?>
    	<div class='itemselector'>
    	<div class="header">
    	
    	<i class="fa-solid fa-people-group"></i> Bildgallerier
    	</div>
    	<div class='itemcontainer'>
    	<a href='participants.php' target='_blank'>Deltagare på lajvet</a><br>
    	<a href='officials.php' target='_blank'>Funktionärer på lajvet</a>
    	</div>
    	</div>
    	
    	
    	<?php 
	  //Annonser	
      $adtypes = AdvertismentType::allActive($current_larp);
       if (!empty($adtypes)) {
           echo "<div class='itemselector'>";
           echo "<div class='header'>";
           
           echo "<i class='fa-solid fa-bullhorn'></i> Annonser";
           echo "</div>";
           echo "<div class='itemcontainer'>";
           
           
           echo "<a href='advertisments.php'><b>Se alla annonser</b></i></a>\n";
           $advertisment_array = $current_person->getAdvertismentsAtLarp($current_larp);
           if(isset($advertisment_array) && count($advertisment_array) > 0) {
               echo "<div>\n";
               echo "<br>Egna annonser:<br>\n";
               echo "<table class='data' id='ads' align='left'>";
               echo "<tr align='left'><th>Kontakt</th><th>Text</th><th></th>";
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
           echo "<div class='center'><a href='advertisment_form.php'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-bullhorn'></i> &nbsp;Skapa en annons</button></a></div>";
           
           
           echo "</div>";
           echo "</div>";
           
       }
       
       
    //Rykten
    if ($current_larp->hasRumours()) {
        echo "<div class='itemselector'>";
        echo "<div class='header'>";
        
        echo "<i class='fa-solid fa-comments'></i> Rykten";
        help_icon("Rykten är kul. Sprid om dig eller andra.");
        echo "</div>";
        echo "<div class='itemcontainer'>";

        $rumour_array = $current_person->getRumoursAtLarp($current_larp);
        $antal = (isset($rumour_array)) ? count($rumour_array) : 0;
        if($antal > 0) {
            echo "<details><summary>Du har spridit $antal rykten</summary> ";
            //     		        echo "<b>Rykten skapade av $current_user->Name:</b><br>\n";
            echo "<table class='data' id='rumours' align='left'>";
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
        
        echo "<div class='center'><a href='rumour_suggestion.php'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-comments'></i> &nbsp;Sprid ett rykte</button></a></div>";
        
        
        echo "</div>";
        echo "</div>";

    }
    
    //Brev
    if ($current_larp->hasLetters()) {
        echo "<div class='itemselector'>";
        echo "<div class='header'>";
        
        echo "<i class='fa-solid fa-envelope'></i> Brev";
        help_icon("Någon gång under lajvet kommer förhoppningsvis det här brevet nå sin mottagare.");
        echo "</div>";
        echo "<div class='itemcontainer'>";
        $letter_array = $current_person->getLettersAtLarp($current_larp);
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
        echo "<div class='center'><a href='letter_suggestion.php'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-envelope'></i> &nbsp;Skriv ett brev</button></a></div>";
        
        
        echo "</div>";
        echo "</div>";
    }
    
    //Telegram
    if ($current_larp->hasTelegrams()) {
        echo "<div class='itemselector'>";
        echo "<div class='header'>";
        
        echo "<i class='fa-brands fa-telegram'></i> Telegram";
        help_icon("På ett visst klockslag under lajvet kommer telegrammets mottagare att få ditt meddelande.");
        echo "</div>";
        echo "<div class='itemcontainer'>";
        $telegram_array = $current_person->getTelegramsAtLarp($current_larp);
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
 
        echo "<div class='center'><a href='telegram_suggestion.php'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-brands fa-telegram'></i> &nbsp;Skapa ett telegram</button></a></div>";
        
        
        echo "</div>";
        echo "</div>";
    }
    
        
        ?>
	
	</div>
	<?php } ?>

	<?php if (!empty($registration) && $registration->hasSpotAtLarp() && $current_larp->isEnded()) { ?>
	<div id="AfterLARP" class="tabcontent">

		<?php 

            //Utvärdering
            if ($current_larp->isEnded() && !(AccessControl::hasAccessLarp($current_person, $current_larp))) {
                echo "<div class='itemselector'>";
                echo "<div class='header'>";
                
                echo "<i class='fa-solid fa-person'></i> Utvärdering</div>";

                
                echo "<div class='itemcontainer'>";

                if ($current_larp->isEvaluationOpen()) {
                    if ($registration->hasDoneEvaluation()) {
                        echo "Utvärderingen är inlämnad";
                    } elseif ($current_larp->useInternalEvaluation()) {
                        echo "<a href='evaluation.php?PersonId=$current_person->Id'>Gör utvärdering</a><br>";
                        echo showParticipantStatusIcon(false, "Utvärderingen är inte gjord");

                    } else {
                        echo "<a target='_blank' href='$current_larp->EvaluationLink'>Gör utvärdering (extern länk)</a>";
                    }
                } else echo "Utvärderingen öppnar $current_larp->EvaluationOpenDate";
                echo "</div>\n";
                
            }

            echo "<div class='itemselector'>";
            echo "<div class='header'><i class='fa-solid fa-landmark'></i> Vad hände?";
            help_icon("Skriv in vad som hände så snart som möjligt och så detaljerat som möjligt. Det är till hjälp för dig nästa gång du ska spela karaktären och för arrangörerna när de ska skriva intriger."); 
            echo "</div>";
             foreach ($registered_roles as $role) {
                echo "<div class='itemcontainer borderbottom'>";
                //Namn på karaktären
                echo "<div class='itemname'>";
                echo $role->getViewLink();
                echo "</div>";
    		    echo "<a href='larp_report_form.php?roleId=$role->Id'>Vad hände?</a> ";
    		    if (!$role->hasRegisteredWhatHappened($current_larp))
    		  		    echo "<br>".showParticipantStatusIcon(false, "Inte noterat vad som hände");
    
      		    echo "</div>";
            }
            echo "</div>";
        		
        		?>

	</div>
	<?php } ?>




</body>


<script>
// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>

<script>

function doSwish() {
<!-- Deep link URL for existing users with app already installed on their device -->
<?php if (isset($registration)) {?>
window.location = '<?php echo Swish::getSwishLink($registration, $campaign)?>';
<?php } ?>
<!-- Download URL (TUNE link) for new users to download the app -->
setTimeout("window.location = 'index.php?error=swishNotInstalled';", 1000);
}

</script>