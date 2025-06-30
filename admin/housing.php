<?php
include_once 'header.php';
include 'navigation.php';




function print_group(Group $group,$group_members, $house) {
    global $current_larp;
    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    $group_housing_requestId = $larp_group->HousingRequestId;
    $comments = array();
    $firstPaymentDate = null;
    $lastPaymentDate = date_create(substr($current_larp->StartDate,0,10));
    $lastPaymentDate->modify('-1 year');
    

    
    foreach($group_members as $group_member) {
        $comment = $group_member->getFullHousingComment($current_larp);
        if (!empty($comment)) $comments[] = $comment;
        $registration = $group_member->getRegistration($current_larp);
        if (isset($registration->Payed)) {
            $payedDate = new DateTime($registration->Payed);
             if (empty($firstPaymentDate) OR ($firstPaymentDate > $payedDate)) {
                $firstPaymentDate = $payedDate;
            }
            if (isset($lastPaymentDate) && $lastPaymentDate < $payedDate) {
                $lastPaymentDate = $payedDate;
            }
        } else {
            $lastPaymentDate = null;
        }
        
    }
    $id="group_$group->Id";
    if (isset($house)) $id = $id."_$house->Id";
    echo "<div class='group' id='$id' draggable='true' ondragstart='drag(event)'>\n";
    echo "  <div class='name' nowrap><a href='view_group.php?id=$group->Id' draggable='false'>$group->Name</a>";
    $houseId = "";
    if (isset($house)) $houseId = $house->Id;
    echo " <span onclick='show_hide_area(\"members_$id\", this)' name='hide'><i class='fa-solid fa-caret-left'></i></span>";
    echo "</div>\n";
    echo "  <div id='count_$id'>".count($group_members)." st";
    if (!empty($comments)) {
        echo " <i class='fa-solid fa-circle-info' title='".implode(", ", $comments)."'></i>";
    }
    echo "</div>\n";
    echo "  <div>";
    if (!empty($group_housing_requestId)) echo HousingRequest::loadById($group_housing_requestId)->Name;
    else echo "Inget boendealternativ valt";
    echo "</div>\n";
    echo "<div>";
    if (empty($firstPaymentDate)) echo "Betalning saknas";
    elseif (empty($lastPaymentDate)) echo "Första betalade ".date_format($firstPaymentDate,"Y-m-d").", alla har inte betalat";
    else echo "Betalat ".date_format($firstPaymentDate,"Y-m-d")." - ".date_format($lastPaymentDate,"Y-m-d")."";
    echo "</div>";
    echo "  <div class='hidden' id='members_$id'>\n";
    echo "    <div class='group_members clearfix' style='display:table; border-spacing:5px;'>\n";
    foreach($group_members as $person) {
        print_individual($person, $group, $house);
    }
    echo "    </div>\n";
    echo "  </div>\n";
    echo "</div>\n";
}

function print_individual(Person $person, $group, $house) {
    global $current_larp;
    $registration = $person->getRegistration($current_larp);
    $id="person_$person->Id";
    if (isset($group)) $id = $id."_$group->Id";
    else  $id = $id."_X";
    if (isset($house)) $id = $id."_$house->Id";
    
    echo "<div class='person' id='$id' draggable='true' ondragstart='drag(event)'>\n";
    echo "  <div class='name' draggable='false'>";
    echo $registration->isNotComing() ? 
        "<s>" . $person->getViewLink() . "</s>" 
        : $person->getViewLink();
    echo "\n";
    
    $comment = $person->getFullHousingComment($current_larp);
    if (!empty($comment)) {
        echo "   <i class='fa-solid fa-circle-info' title='$comment'></i>\n";
    }
    echo "  </div>\n";

    $housingRequest = $person->getRegistration($current_larp)->getHousingRequest();
    echo "  <div>";
    if (!empty($housingRequest)) echo $housingRequest->Name;
    echo "</div>\n";
    echo "<div>";
    if (empty($registration->Payed)) echo "Betalning saknas";
    else echo "Betalat $registration->Payed";
    echo "</div>\n";
    
    echo "</div>\n";
    
    
}


function print_house($house) {
    global $current_larp;
    $personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    $notComingWarning = false;
    foreach ($personsInHouse as $personInHouse) {
        if ($personInHouse->isNotComing($current_larp)) {
            $notComingWarning = true;
            break;
        }
    }
    $groupsInHouse = Group::getGroupsInHouse($house, $current_larp);
    
    echo "<div class='house' id='house_$house->Id' ondrop='drop_in_house(event, this)' ondragover='allowDrop(event)'>\n";
    echo "<div class='name'><a href='view_house.php?id=$house->Id'>$house->Name</a> ";
    if ($notComingWarning) echo showStatusIcon(false);
    
    echo "<button class='invisible' onclick='show_hide(\"house_$house->Id\")><i class='fa-solid fa-caret-left'></i></button></div>\n";
    if ($house->isHouse()) {
        echo "<div>Platser: $house->ComfortNumber (komfort), $house->MaxNumber (max)";
        $numInHouse = count($personsInHouse);
        if ($numInHouse <= $house->ComfortNumber) echo showStatusIcon(true);
        elseif ($numInHouse > $house->MaxNumber) echo showStatusIcon(false);
        else echo showWarningIcon();
        echo "</div>\n";
    }
    else echo "<div>Antal tältplatser: $house->NumberOfBeds</div>\n";

    echo "<div id='count_house_$house->Id'>".count($personsInHouse)." pers";
    echo "</div>";
    

    
    echo "<div id='in_house_$house->Id' class='in_house clearfix'>\n";
    foreach($groupsInHouse as $group) {
        
        $membersInHouse = Person::getGroupMembersInHouse($group, $house, $current_larp);
        print_group($group, $membersInHouse, $house);
        $personsInHouse = array_udiff($personsInHouse, $membersInHouse,
            function ($objOne, $objTwo) {
                return $objOne->Id - $objTwo->Id;
            });
    }
    foreach ($personsInHouse as $person) {
        print_individual($person, null, $house);
    }
    echo "</div>\n";
    
    echo "</div>\n";
}

?>


<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>
<script src="../javascript/assign_to_house_ajax.js"></script>
<script src="../javascript/show_hide_area.js"></script>



<style>
div.housing-group {
    border: 1px solid #ccc;
	border-radius: 10px;
    padding: 5px;
    margin-bottom: 5px;
}


.person, .group, .house {
    border: 1px solid #ccc;
	border-radius: 10px;
    padding: 5px;
	background-color: #fff;
	width: 45%;
	float: left;
	box-sizing:border-box;
	margin: 5px;
}

.clearfix::after { 
   content: " ";
   display: block; 
   height: 0; 
   clear: both;
}
</style>


<div class="content">
    <h1>Boende</h1>
    
    <a href="reports/housing_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf</a><br><br>  
    Först på sidan kommer information om husförvaltare och önskat boende. Nedanför det görs själva husfördelningen. Den görs genom att man drar och släpper grupper eller karaktärer till hus eller lägerplatser. Den fungerar enbart på dator. På husen/lägerplatserna står information om ungefär hur många som kan bo där och allt eftersom man lägger till grupper och karaktärer som uppdateras siffran med hur många man faktiskt har lagt i huset/lägerplatsen.
    
    <h2>Översikt husförvaltare</h2>
    Här är alla husförvaltare som är anmälda till lajvet, vilka hus de förvaltar samt vilken grupp deras huvudkaraktär är med i.
    <?php 
    $caretaking_persons = Person::getPersonsWhoIsHouseCaretakers($current_larp);
    $tableId = "caretakers";
    $colnum = 0;
    echo "<table id='$tableId' class='data'>";
    echo "<tr>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Hus</th>";
    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Grupp</th>".

        "</tr>";
    foreach ($caretaking_persons as $person) {
        $group = $person->getMainRole($current_larp)->getGroup();
        echo "<tr><td>" . $person->getViewLink().contactEmailIcon($person)."</td>\n";
        $houses = $person->housesOf();
        $houseslinks = array();
        foreach ($houses as $house) {
            $houseslinks[] = "<a href='view_house.php?id=$house->Id'>".$house->Name."</a>";
        }
        echo "<td>".implode(",", $houseslinks)."</td>";
        
        if (!empty($group)) {
            echo "<td>";
            echo $group->getViewLink();
            echo "</td>\n";
        } else echo "<td></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    ?>

	<h2>Översikt önskat boende</h2>
	<?php 
	$count = HousingRequest::countByType($current_larp);
	foreach($count as $item) {
	    echo $item['Name'].": ".$item['Num']." pers<br>\n";
	}
	echo "<br>\n";
	?>
	<table width='100%'><tr><td width='45%'>
	<h2>Deltagare som inte har tilldelats boende</h2>
	<?php 
	
	$personsWithoutHousing=Person::getAllRegisteredWithoutHousing($current_larp);

	$groups = Group::getAllRegistered($current_larp);
	
	echo "<h3>Grupper</h3>";
	echo "<div id='unassigned_groups' class='housing-group clearfix' ondrop='drop_unassigned_group(event, this)' ondragover='allowDrop(event)'>\n";
	foreach ($groups as $group) {
	    $group_members_without_housing = Person::getPersonsInGroupWithoutHousing($group, $current_larp);
	    $personsWithoutHousing = array_udiff($personsWithoutHousing, $group_members_without_housing,
	        function ($objOne, $objTwo) {
	            return $objOne->Id - $objTwo->Id;
	        });
	    
	    if (!empty($group_members_without_housing)) {
    	    print_group($group, $group_members_without_housing, null);
	    }

	}
	echo"</div>\n";
	
	echo "<h3>Individer</h3>";

	echo "<div id='unassigned_persons' class='housing-group clearfix' ondrop='drop_unassigned_person(event, this)' ondragover='allowDrop(event)'>\n";
	foreach ($personsWithoutHousing as $person) {
	    print_individual($person, null, null);
	}
	echo "</div>\n";

	?>
	</td><td width='45%'>
	<h2>Tilldelat boende</h2>
	Om det är ett <?php echo showStatusIcon(false)?> efter namnet på huset/lägerplatsen betyder det att minst en person i huset har blivit avbokad.<br>
	Ikonen efter antalet platser i huset anger hur fullt huset är. Om det är <?php echo showStatusIcon(false)?> är det över max-antal i huset.
	<div class='housing-group clearfix'>
	
	<h3>Hus <?php echo " <span onclick='show_hide_area(\"houses\", this)' name='hide'><i class='fa-solid fa-caret-down'></i></span>";?></h3>
	
	<?php 
	$houses=House::getAllHouses();
	echo "<div id='houses'>\n";
	
	foreach($houses as $house) {
	    print_house($house);
	}
	echo "</div>\n";
	?>
	</div>
	<div class='housing-group clearfix'>
	<h3>Lägerplatser <?php echo " <span onclick='show_hide_area(\"camps\", this)' name='hide'><i class='fa-solid fa-caret-down'></i></span>"; ?></h3>
	<?php 
	$camps=House::getAllCamps();
	echo "<div id='camps'>\n";
	foreach($camps as $camp) {
	    print_house($camp);
	}
	
	echo "</div>\n";
	?>
	</div>
	</td></tr></table>
</div>

