<?php
include_once 'header.php';
include 'navigation.php';
?>

<div class="content">
    <h1>Funktionärer</h1>
    <?php 
    $persons = Person::getAllOfficials($current_larp);
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
