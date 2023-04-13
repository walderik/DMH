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
	?>
	<h2>Deltagare som inte har tilldelats boende</h2>
	<?php 
	
	
	$persons=Person::getAllRegisteredWithoutHousing($current_larp);
	$groups = Group::getAllRegistered($current_larp);
	
	foreach ($groups as $group) {
	    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
	    $group_housing_requestId = $larp_group->HousingRequestId;
	    echo "<h3>$group->Name</h3>";
	    echo "<table><tr><td>";
	    echo "Ungefär $larp_group->ApproximateNumberOfMembers st, ";
	    echo HousingRequest::loadById($group_housing_requestId)->Name;
	    echo "</td><td>";
	    echo "<form method='post' action='assign_to_house.php'>";
	    echo "<input type='hidden' name='type' value='group'>";
	    echo "<input type='hidden' name='id' value='$group->Id'>";
	    echo "<input type='submit' value='Tilldela nedanstående medlemmar till hus'>";
        echo "</form>";
        echo "</td></tr></table>";

	    
	    $group_members = Person::getPersonsInGroup($group, $current_larp);
	    echo "<table class='list'>";
	    foreach ($group_members as $person) {
	        $index_of_person = array_search($person, $persons);
	        if (!empty($index_of_person)) {
	            echo "<tr><td>";
    	        echo $person->Name;
    	        echo "</td><td>";
    	        $housing_requestId = $person->getRegistration($current_larp)->HousingRequestId;
    	        if ($housing_requestId != $group_housing_requestId) {
    	            echo HousingRequest::loadById($housing_requestId)->Name;

    	        }
    	        echo "</td><td>";
    	        
    	        if (!empty($person->HousingComment)) {
    	            echo $person->HousingComment;

    	        }
    	        echo "</td><td>";
    	        echo "<form method='post' action='assign_to_house.php'>";
    	        echo "<input type='hidden' name='type' value='person'>";
    	        echo "<input type='hidden' name='id' value='$person->Id'>";
    	        echo "<input type='submit' value='Tilldela till hus'>";
    	        echo "</form>";
    	        
    	        echo "</td></tr>";
    	        unset($persons[$index_of_person]);
	        }

	    }
	    echo "</table>";
	}
	
	echo "<h3>Övriga</h3>";

	echo "<table>";
	foreach ($persons as $person) {
	    echo "<tr><td>";
	    echo $person->Name;
	    echo "</td><td>";
	    echo HousingRequest::loadById($person->getRegistration($current_larp)->HousingRequestId)->Name;
	    echo "</td><td>";
	    if (!empty($person->HousingComment)) {
	        echo ", ".$person->HousingComment;
	    }
	    echo "</td><td>";
	    
	    $role = $person->getMainRole($current_larp);
	    echo "Spelar: $role->Name, $role->Profession"; 
	    echo "</td><td>";
	    echo "<form method='post' action='assign_to_house.php'>";
	    echo "<input type='hidden' name='type' value='person'>";
	    echo "<input type='hidden' name='id' value='$person->Id'>";
	    echo "<input type='submit' value='Tilldela till hus'>";
	    echo "</form>";
	    echo "</td></tr>";

	}
	echo "</table>";
	?>
	
	<h2>Husen</h2>
	<?php 
	$houses=House::all();
	foreach($houses as $house) {
	    echo "<h3>$house->Name</h3>";
        echo "Antal platser: ".$house->NumberOfBeds."<br>";
        $personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
        echo "<table>";
        foreach ($personsInHouse as $person) {
            $group = $person->getMainRole($current_larp)->getGroup();   
            echo "<tr><td>";
            echo $person->Name;
            echo "</td><td>";
            if (!empty($group)) {
                echo "($group->Name)";
            }
            echo "</td><td>";
            echo HousingRequest::loadById($person->getRegistration($current_larp)->HousingRequestId)->Name;
	        echo "</td><td>";
            if (!empty($person->HousingComment)) {
                echo $person->HousingComment;
            }
            echo "</td><td>";
            echo "<form method='post' action='logic/remove_housing.php'>";
            echo "<input type='hidden' name='id' value='$person->Id'>";
            echo "<input type='submit' value='Ta bort från hus'>";
            echo "</form>";
            echo "</td></tr>";
        }
        echo "</table>";
	}
	
	?>
</div>
