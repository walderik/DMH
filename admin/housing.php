<?php
include_once 'header.php';
include 'navigation.php';


function print_group(Group $group,$group_members, $house) {
    global $current_larp;
    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    $group_housing_requestId = $larp_group->HousingRequestId;
    $comments = array();
    foreach($group_members as $group_member) {
        if (!empty($group_member->HousingComment)) $comments[] = $group_member->HousingComment;
    }
    $id="group_$group->Id";
    if (isset($house)) $id = $id."_$house->Id";
    echo "<div class='group' id='$id' draggable='true' ondragstart='drag(event)'>\n";
    echo "<div class='name' nowrap><a href='view_group.php?id=$group->Id' draggable='false'>$group->Name</a>";
    $houseId = "";
    if (isset($house)) $houseId = $house->Id;
    echo " <span onclick='show_hide_area(\"Group_".$group->Id."_".$houseId."\", this)' name='hide'><i class='fa-solid fa-caret-left'></i></span>";
    echo "</div>\n";
    echo "<div id='count_$id>".count($group_members)." st";
    if (!empty($comments)) {
        echo " <i class='fa-solid fa-circle-info' title='".implode(", ", $comments)."'></i>";
    }
    echo "</div>\n";
    echo "<div>".HousingRequest::loadById($group_housing_requestId)->Name."</div>\n";
    
    echo "<div class='hidden' id='Group_".$group->Id."_".$houseId."'>";
    echo "<div class='group_members clearfix' style='display:table; border-spacing:5px;'>";
    foreach($group_members as $person) {
        print_individual($person, $group, $house);
        echo "<br>";
    }
    echo "</div>";
    
    
    echo "</div>\n";
    echo "</div>\n";
}

function print_individual(Person $person, $group, $house) {
    global $current_larp;
    $id="person_$person->Id";
    if (isset($group)) $id = $id."_$group->Id";
    else  $id = $id."_X";
    if (isset($house)) $id = $id."_$house->Id";
    
    echo "<div class='person' id='person_$person->Id' draggable='true' ondragstart='drag(event)'>\n";
    echo "<div class='name'><a href='view_person.php?id=$person->Id' draggable='false'>$person->Name</a>";
    if (!empty($person->HousingComment)) {
        echo " <i class='fa-solid fa-circle-info' title='$person->HousingComment'></i>";
    }
    echo "</div>\n";
    echo "<div>".HousingRequest::loadById($person->getRegistration($current_larp)->HousingRequestId)->Name."</div>\n";
    echo "</div>";
    
}

function print_house($house) {
    global $current_larp;
    echo "<div class='house' id='house_$house->Id' ondrop='drop_in_house(event, this)' ondragover='allowDrop(event)'>\n";
    echo "<div class='name'>$house->Name <button class='invisible' onclick='show_hide(\"house_$house->Id\")><i class='fa-solid fa-caret-left'></i></button></div>\n";
    echo "<div>Antal platser: $house->NumberOfBeds</div>\n";
    $personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    echo "<div id='count_house_$house->Id'>".count($personsInHouse)." st</div>";
    
    $groupsInHouse = Group::getGroupsInHouse($house, $current_larp);
    
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

<?php 

include_once '../javascript/table_sort.js';
include_once '../javascript/show_hide_area.js';

?>

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
    
    <h2>Husförvaltare</h2>
    <?php 
    $care_takers = Person::getHouseCaretakers($current_larp);
    $tableId = "caretakers";
    $colnum = 0;
    echo "<table id='$tableId' class='data'>";
    echo "<tr>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Hus</th>";
    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Grupp</th>".

        "</tr>";
    foreach ($care_takers as $care_taker) {
        $group = $care_taker->getMainRole($current_larp)->getGroup();
        echo "<tr><td><a href='view_person.php?id=$care_taker->Id'>$care_taker->Name ".contactEmailIcon($care_taker->Name,$care_taker->Email)."</a></td>";
        $house = $care_taker->getHouse();
        echo "<td><a href='view_house.php?id=$house->Id'>$house->Name</a></td>";
        
        if (!empty($group)) {
            echo "<td><a href='view_group.php?id=$group->Id'>$group->Name</a></td>";
        } else echo "<td></td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>

	<h2>Önskat boende</h2>
	<?php 
	$count = HousingRequest::countByType($current_larp);
	foreach($count as $item) {
	    echo $item['Name'].": ".$item['Num']." st<br>";
	}
	echo "<br>";
	?>
	<table width='100%'><tr><td width='45%'>
	<h2>Deltagare som inte har tilldelats boende</h2>
	<?php 
	
	$personsWithoutHousing=Person::getAllRegisteredWithoutHousing($current_larp);

	$groups = Group::getAllRegistered($current_larp);
	
	echo "<div id='unassigned_groups' class='housing-group clearfix' ondrop='drop_unassigned_group(event, this)' ondragover='allowDrop(event)'>";
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
	
	if (!empty($personsWithoutHousing)) {
    	echo "<h3>Individer</h3>";
    
    	echo "<div id='unassigned_persons' class='housing-group clearfix' ondrop='drop_unassigned_person(event, this)' ondragover='allowDrop(event)'>";
    	foreach ($personsWithoutHousing as $person) {
    	    print_individual($person, null, null);
    	}
    	echo "</div>\n";

	}
	?>
	</td><td width='45%'>
	<h2>Tilldelat boende</h2>
	<div class='housing-group clearfix'>
	
	<h3>Hus <?php echo " <span onclick='show_hide_area(\"houses\", this)' name='hide'><i class='fa-solid fa-caret-down'></i></span>";?></h3>
	
	<?php 
	$houses=House::all();
	echo "<div id='houses'>";
	
	foreach($houses as $house) {
	    print_house($house);
	}
	echo "</div>";
	?>
	</div>
	<div class='housing-group clearfix'>
	<h3>Lägerplatser <?php echo " <span onclick='show_hide_area(\"camps\", this)' name='hide'><i class='fa-solid fa-caret-down'></i></span>"; ?></h3>
	<?php 
	echo "<div id='camps'>";
	
	echo "</div>";
	?>
	</div>
	</td></tr></table>
</div>
