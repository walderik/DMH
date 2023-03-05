<?php
include_once 'header_subpage.php';
?>

<div class="content">
    <h1>Allergier</h1>

    Just nu är det <?php echo count(Registration::allBySelectedLARP()); ?> anmälda deltagare.<br>
    
    <?php 
    
    $allAllergies = NormalAllergyType::all();
    
    foreach($allAllergies as $allergy) {
        $persons = Person::getAllWithSingleAllergy($allergy, $current_larp);
        if (isset($persons) && count($persons) > 0) {
            echo "<h2>Enbart $allergy->Name</h2><table class='data'>";
            echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Övrigt</th><th>Vald mat</th></tr>";
            foreach($persons as $person) {
                echo "<tr><td>$person->Name</td><td>$person->Email</td><td>$person->PhoneNumber</td><td>$person->FoodAllergiesOther</td><td>".$person->getTypeOfFood()->Name."</td></tr>";
            }
            echo "</table>";
        }
    }
     
    
    //Multipla allergier
    $persons = Person::getAllWithMultipleAllergies($current_larp);
    echo "<h2>Multipla vanliga allergier</h2><table class='data'>";
    echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Allergier</th><th>Övrigt</th><th>Vald mat</th></tr>";
    foreach($persons as $person) {
        echo "<tr><td>$person->Name</td><td>$person->Email</td><td>$person->PhoneNumber</td><td>" . commaStringFromArrayObject($person->getNormalAllergyTypes()) . "</td><td>$person->FoodAllergiesOther</td><td>" . $person->getTypeOfFood()->Name . "</td></tr>";
    }
    echo "</table>";
    
    
    //Hitta alla som inte har någon vald allergi, men som har en kommentar
    $persons = Person::getAllWithoutAllergiesButWithComment($current_larp);
    echo "<h2>Special</h2><table class='data'>";
    echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Övrigt</th><th>Vald mat</th></tr>";
    foreach($persons as $person) {
        echo "<tr><td>$person->Name</td><td>$person->Email</td><td>$person->PhoneNumber</td><td>$person->FoodAllergiesOther</td><td>" . $person->getTypeOfFood()->Name . "</td></tr>";
    }
    echo "</table>";
    
    
    ?>
</div>
