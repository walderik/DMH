<?php
include_once 'header.php';
include 'navigation_subpage.php';
?>

<div class="content">
    <h1>Boende</h1>
    
    <h2>Husförvaltare</h2>
    <?php 
    $care_takers = Person::getHouseCaretakers($current_larp);
    echo "<table>";
    foreach ($care_takers as $care_taker) {
        $group = $care_taker->getMainRole($current_larp)->getGroup();
        echo "<tr><td>$care_taker->Name</td><td>" . $care_taker->getHouse()->Name . "</td>";
        if (!empty($group)) {
            echo "<td>$group->Name</td>";
        }
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
	
	$persons=Person::getAllRegistered($current_larp);
	$groups = Group::getAllRegistered($current_larp);
	
	foreach ($groups as $group) {
	    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
	    $group_housing_requestId = $larp_group->HousingRequestId;
	    echo "<h3>$group->Name</h3>";
	    echo "Ungefär $larp_group->ApproximateNumberOfMembers st, ";
	    echo HousingRequest::loadById($group_housing_requestId)->Name;
	    echo "<br><br>";
	    
	    $group_members = Role::getRegisteredRolesInGroup($group, $current_larp);
	    foreach ($group_members as $group_member) {
	        $person = $group_member->getPerson();
	        $index_of_person = array_search($person, $persons);
	        if (!empty($index_of_person)) {
    	        echo "$person->Name ";
    	        $housing_requestId = $group_member->getRegistration($current_larp)->HousingRequestId;
    	        if ($housing_requestId != $group_housing_requestId) {
    	            echo HousingRequest::loadById($housing_requestId)->Name;
    	        }
    	        echo "<br>";
    	        unset($persons[$index_of_person]);
	        }


	    }
	}
	
	echo "<h3>Övriga</h3>";

	foreach ($persons as $person) {
	    echo "$person->Name ";
	    echo HousingRequest::loadById($person->getRegistration($current_larp)->HousingRequestId)->Name;
	    echo "<br>";
	}
	?>
</div>
