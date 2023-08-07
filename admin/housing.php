<?php
include_once 'header.php';
include 'navigation.php';

$individualcols = 3;
$individualwidth = "30%";
$groupcols = 3;
$groupwidth = "30%";
$housecols = 2;
$housewidth = "49%";



function print_group(Group $group,$group_members, $house) {
    global $current_larp, $groupwidth;
    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    $group_housing_requestId = $larp_group->HousingRequestId;
    $comments = array();
    foreach($group_members as $group_member) {
        if (!empty($group_member->HousingComment)) $comments[] = $group_member->HousingComment;
    }
    
    echo "<li style='display:table-cell; width:$groupwidth;' draggable='true' ondragstart='drag(event)'>\n";
    echo "<div class='name' nowrap><a href='view_group.php?id=$group->Id'>$group->Name</a>";
    if (isset($house)) {
        echo "<button class='invisible' type='submit'><i class='fa-solid fa-xmark' title='Ta bort från huset'></i></button>";        
    }
    $houseId = "";
    if (isset($house)) $houseId = $house->Id;
    echo " <span onclick='show_hide_area(\"Group_".$group->Id."_".$houseId."\", this)' name='hide'><i class='fa-solid fa-caret-left'></i></span>";
    echo "</div>\n";
    //echo "<button class='invisible' onclick='show_hide(\"Group_".$group->Id."_".$houseId."\")'><i class='fa-solid fa-caret-left'></i></button></div>\n";
    echo "<div>".count($group_members)." st";
    if (!empty($comments)) {
        echo " <i class='fa-solid fa-circle-info' title='".implode(", ", $comments)."'></i>";
    }
    echo "</div>\n";
    echo "<div>".HousingRequest::loadById($group_housing_requestId)->Name."</div>\n";
    
    echo "<div class='hidden' id='Group_".$group->Id."_".$houseId."'>";
    foreach($group_members as $person) {
        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
        print_individual($person, $house);
        echo "</ul>";
    }
    
    
    echo "</li>\n";
}

function print_individual(Person $person, $house) {
    global $current_larp, $individualwidth;
    echo "<li style='display:table-cell; width:$individualwidth;' draggable='true' ondragstart='drag(event)'>\n";
    echo "<div class='name'><a href='view_person.php?id=$person->Id'>$person->Name</a>";
    if (!empty($person->HousingComment)) {
        echo " <i class='fa-solid fa-circle-info' title='$person->HousingComment'></i>";
    }
    if (isset($house)) {
        echo "<button class='invisible' type='submit'><i class='fa-solid fa-xmark' title='Ta bort från huset'></i></button>";
    }
    echo "</div>\n";
    echo "<div>".HousingRequest::loadById($person->getRegistration($current_larp)->HousingRequestId)->Name."</div>\n";
    echo "</li>";
    
}

function print_house($house) {
    global $current_larp, $housewidth;
    echo "<li style='display:table-cell; width:$housewidth;' ondrop='drop(event)' ondragover='allowDrop(event)'>\n";
    echo "<div class='name'>$house->Name <button class='invisible' onclick='show_hide(\"house_$house->Id\")><i class='fa-solid fa-caret-left'></i></button></div>";
    echo "<div>Antal platser: $house->NumberOfBeds</div>";
    $personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    echo "<div>".count($personsInHouse)." st</div>";
    
    $groupsInHouse = Group::getGroupsInHouse($house, $current_larp);
    
    foreach($groupsInHouse as $group) {
        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
        
        $membersInHouse = Person::getGroupMembersInHouse($group, $house, $current_larp);
        print_group($group, $membersInHouse, $house);
        $personsInHouse = array_udiff($personsInHouse, $membersInHouse,
            function ($objOne, $objTwo) {
                return $objOne->Id - $objTwo->Id;
            });
        echo "</ul>";
    }
     
    
    
    foreach ($personsInHouse as $person) {
        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
        
        print_individual($person, $house);
        echo "</ul>";

    }
 
    echo "</li>";
}

?>

<?php 

include_once '../javascript/table_sort.js';
include_once '../javascript/show_hide_area.js';

?>

<script>
function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
  ev.preventDefault();
  var data = ev.dataTransfer.getData("text");
  alert(data);
  //ev.target.appendChild(document.getElementById(data));
}
</script>

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
        echo "<tr><td><a href='view_person-php?$care_taker->Id'>$care_taker->Name ".contactEmailIcon($care_taker->Name,$care_taker->Email)."</a></td>";
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
	<table><tr><td width='45%'>
	<h2>Deltagare som inte har tilldelats boende</h2>
	<?php 
	
	$personsWithoutHousing=Person::getAllRegisteredWithoutHousing($current_larp);

	$groups = Group::getAllRegistered($current_larp);
	
	echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	$temp = 0;
	foreach ($groups as $group) {
	    $group_members = Person::getPersonsInGroup($group, $current_larp);

	    $group_members_without_housing = array_uintersect($personsWithoutHousing, $group_members, 
	        function ($objOne, $objTwo) {
	            return $objOne->Id - $objTwo->Id;
	        });
	    $personsWithoutHousing = array_udiff($personsWithoutHousing, $group_members_without_housing,
	        function ($objOne, $objTwo) {
	            return $objOne->Id - $objTwo->Id;
	        });
	    
	    if (!empty($group_members_without_housing)) {
    	    print_group($group, $group_members_without_housing, null);
    	    $temp++;
    	    if ($temp == $groupcols) {
    	        echo"</ul>\n";
    	        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
    	        $temp = 0;
    	    }
	    }

	}
	echo"</ul>\n";
	
	if (!empty($personsWithoutHousing)) {
    	echo "<h3>Individer</h3>";
    
    	echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
    	$temp = 0;
    	foreach ($personsWithoutHousing as $person) {
    	    print_individual($person, null);
    	    $temp++;
    	    if ($temp == $individualcols) {
    	        echo"</ul>\n";
    	        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
    	        $temp = 0;
    	    }
    	    
    	}
    	echo "</ul>\n";

	}
	?>
	</td><td width='45%'>
	<h2>Tilldelat boende</h2>
	<div>
	<h3>Hus</h3>
	<?php 
	$houses=House::all();
	echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	$temp = 0;
	
	foreach($houses as $house) {
	    print_house($house);
	    $temp++;
	    if ($temp == $housecols) {
	        echo"</ul>\n";
	        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	        $temp = 0;
	    }
	}
	echo "</ul>\n";
	
	?>
	</div>
	<div>
	<h3>Lägerplatser</h3>
	</div>
	</td></tr></table>
</div>
