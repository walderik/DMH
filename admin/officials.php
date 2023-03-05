<?php
include_once 'header_subpage.php';
?>

<div class="content">
    <h1>Funktionärer</h1>
    <?php 
    $persons = Person::getAllOfficials($current_larp);
    echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Typ av funktionär</th></tr>";
    foreach($persons as $person) {
        echo "<tr><td>$person->Name</td><td>$person->Email</td><td>$person->PhoneNumber</td><td>".commaStringFromArrayObject($person->getRegistration($current_larp)->getOfficialTypes())."</td></tr>";
    }
    echo "</table>";
    
    ?>
    <h2>Deltagare som vill vara funktionärer</h2>
    <?php 
    $persons = Person::getAllWhoWantToBeOffical($current_larp);
    echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Önskad typ av funktionär</th></tr>";
    foreach($persons as $person) {
        echo "<tr><td>$person->Name</td><td>$person->Email</td><td>$person->PhoneNumber</td><td>".commaStringFromArrayObject($person->getRegistration($current_larp)->getOfficialTypes())."</td></tr>";
    }
    echo "</table>";
    
    ?>

