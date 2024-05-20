<?php
include_once 'header.php';
include 'navigation.php';
?>

<div class="content">
 	<?php 
 	$header = 'Funktionärer';
 	if (isset($_GET['id'])) {
        $official_type = OfficialType::loadById($_GET['id']);
        if (isset($official_type)) {
            $persons = Person::getAllOfficialsByType($official_type, $current_larp);
            $header = $official_type->Name;
        }
 	} else {
 	    $persons = Person::getAllOfficials($current_larp);
 	}
 	
 	

    echo "<h1>$header</h1>";
    
    $emailArr = array();
 	foreach ($persons as $person) {
 	    $personIdArr[] = $person->Id;
 	}
 	echo contactSeveralEmailIcon("", $personIdArr, "Funktionär på $current_larp->Name", "Meddelande till alla funktionärer i $current_larp->Name");
    echo "<a href='officials.php'>Alla funktionärer</a> &nbsp; &nbsp";
    
    
    $offical_types = OfficialType::allActive($current_larp);
    if (!empty($offical_types)) {
        foreach ($offical_types as $offical_type) {
            
            $officials_by_type = Person::getAllOfficialsByType($offical_type, $current_larp);
            $emailArr = array();
            foreach($officials_by_type as $person) {
                $personIdArr[] = $person->Id;
            }
            $ikon = contactSeveralEmailIcon('', $personIdArr, "$offical_type->Name funktionär", "Meddelande till alla $offical_type->Name i $current_larp->Name");

            echo "$ikon<a href='officials.php?id=$offical_type->Id'>$offical_type->Name</a> &nbsp; &nbsp ";
        }
        echo "<br>\n";
        echo "<br>\n";
    }

    echo "<table class='data'><tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Typ av funktionär</th><th></th></tr>\n";
    foreach($persons as $person) {
        $registration = $person->getRegistration($current_larp);
        echo "<tr><td>\n";
        echo "<a href ='view_person.php?id=$person->Id'>$person->Name</a></td>\n";
        echo "<td>$person->Email ".contactEmailIcon($person)."</td><td>$person->PhoneNumber</td><td>\n";
        if (OfficialType::isInUse($current_larp)) echo commaStringFromArrayObject($registration->getOfficialTypes());
        echo "&nbsp;<a href='edit_official.php?id=$registration->Id' title='Redigera vald funktionärstyp'><i class='fa-solid fa-pen'></i></a>\n".
        "&nbsp;<a href='person_payment.php?id=$person->Id' title='Justera betalning till $person->Name'><i class='fa-solid fa-money-check-dollar'></i></a></td><td>\n";
        ?>
        <form action="logic/official_save.php" method="post">
        <input type="hidden" id="Id" name="Id" value="<?php echo $registration->Id;?>">
        <input type="hidden" id="type" name="type" value="single"><input type="submit" value="Ta bort"></form>
    <?php     
        echo "</td></tr>\n";
    }
    echo "</table>\n";
    
    ?>
    <h2>Deltagare som vill vara funktionärer</h2>
    <?php 
    $persons = Person::getAllWhoWantToBeOffical($current_larp);
    echo "<table class='data'><tr><th>Namn</th><th>Epost</th><th>Telefon</th>";
    if (OfficialType::isInUse($current_larp)) echo "<th>Önskad typ av funktionär</th>";
    echo "<th></th></tr>";
    foreach($persons as $person) {
        $registration = $person->getRegistration($current_larp);
        echo "<tr><td>";
        echo "<a href ='view_person.php?id=$person->Id'>$person->Name</a>";
        echo "</td><td>$person->Email ".contactEmailIcon($person)."</td><td>$person->PhoneNumber</td>";
        if (OfficialType::isInUse($current_larp)) echo "<td>".commaStringFromArrayObject($registration->getOfficialTypes())."</td>";
        echo "<td>";
        ?>
        <form action="logic/official_save.php" method="post"><input type="hidden" id="Id" name="Id" value="<?php echo $registration->Id;?>"><input type="hidden" id="type" name="type" value="single"><input type="submit" value="Lägg till"></form>
    <?php     
        echo "</td></tr>";
    }
        echo "</table>";
    
    ?>
	
	<a href="choose_persons.php?operation=officials">Välj bland alla deltagare</a>
	
	</div>


</body>
</html>
