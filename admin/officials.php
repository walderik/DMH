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
 	}?>
    <?php 
        echo "<h1>$header</h1>"; 
        echo "<a href='officials.php'>Alla funktionärer</a> &nbsp; &nbsp";
        $offical_types = OfficialType::allActive($current_larp);
        if (!empty($offical_types)) {
            foreach ($offical_types as $offical_type) {
                $ikon = contactAllOfficalTypeEmailIcon($offical_type);
                echo "<a href='officials.php?id=$offical_type->Id'>$offical_type->Name</a> $ikon &nbsp; &nbsp;";
            }
            echo "<br>\n";
            echo "<br>\n";
        }
    ?>
    <?php 

    echo "<table class='data'><tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Typ av funktionär</th><th></th></tr>";
    foreach($persons as $person) {
        $registration = $person->getRegistration($current_larp);
        echo "<tr><td>";
        echo "<a href ='view_person.php?id=$person->Id'>$person->Name</a>";
        echo "</td><td>$person->Email ".contactEmailIcon($person->Name,$person->Email)."</td><td>$person->PhoneNumber</td><td>".
        commaStringFromArrayObject($registration->getOfficialTypes()).
        "&nbsp;<a href='edit_official.php?id=$registration->Id'><i class='fa-solid fa-pen'></i></a>".
        "&nbsp;<a href='person_payment.php?id=$person->Id'><i class='fa-solid fa-money-check-dollar'></i></a></td><td>";
        ?>
        <form action="logic/official_save.php" method="post"><input type="hidden" id="Id" name="Id" value="<?php echo $registration->Id;?>"><input type="hidden" id="type" name="type" value="single"><input type="submit" value="Ta bort"></form>
    <?php     
        echo "</td></tr>";
    }
    echo "</table>";
    
    ?>
    <h2>Deltagare som vill vara funktionärer</h2>
    <?php 
    $persons = Person::getAllWhoWantToBeOffical($current_larp);
    echo "<table class='data'><tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Önskad typ av funktionär</th><th></th></tr>";
    foreach($persons as $person) {
        $registration = $person->getRegistration($current_larp);
        echo "<tr><td>";
        echo "<a href ='view_person.php?id=$person->Id'>$person->Name</a>";
        echo "</td><td>$person->Email ".contactEmailIcon($person->Name,$person->Email)."</td><td>$person->PhoneNumber</td><td>";
        echo commaStringFromArrayObject($registration->getOfficialTypes())."</td><td>";
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
