<?php
include_once 'header.php';
include 'navigation.php';
?>

<div class="content">
    <h1>Köket</h1>
    Totalt är det <?php echo count(Registration::allBySelectedLARP($current_larp)); ?> anmälda deltagare.<br>
    <h2>Vald mat</h2>
    <?php 
    $count = TypeOfFood::countByType($current_larp);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
    
    
    ?>

	<h2>Allergier</h2>
    
    <?php 
    if (NormalAllergyType::isInUse()){
        $allAllergies = NormalAllergyType::all();
        
        foreach($allAllergies as $allergy) {
            $persons = Person::getAllWithSingleAllergy($allergy, $current_larp);
            if (isset($persons) && count($persons) > 0) {
                echo "<h3>Enbart $allergy->Name</h3><table class='data'>";
                echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Övrigt</th><th>Vald mat</th></tr>";
                foreach($persons as $person) {
                    $registration=$person->getRegistration($current_larp);
                    echo "<tr><td>$person->Name</td><td>$person->Email ".contactEmailIcon($person->Name,$person->Email)."</td>";
                    echo "<td>$person->PhoneNumber</td><td>$person->FoodAllergiesOther</td><td>".$registration->getTypeOfFood()->Name."</td></tr>";
                }
                echo "</table>";
            }
        }
         
        
        //Multipla allergier
        $persons = Person::getAllWithMultipleAllergies($current_larp);
        if (!empty($persons) && count($persons) > 0) {
            echo "<h3>Multipla vanliga allergier</h3>";
            echo "<table class='data'>";
            echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Allergier</th><th>Övrigt</th><th>Vald mat</th></tr>";
            foreach($persons as $person) {
                $registration=$person->getRegistration($current_larp);
                echo "<tr><td>$person->Name</td><td>$person->Email ".contactEmailIcon($person->Name,$person->Email)."</td>";
                echo "<td>$person->PhoneNumber</td><td>" . commaStringFromArrayObject($person->getNormalAllergyTypes()) . "</td>";
                echo "<td>$person->FoodAllergiesOther</td><td>" . $registration->getTypeOfFood()->Name . "</td></tr>";
            }
            echo "</table>";
        }
    }
    
    //Hitta alla som inte har någon vald allergi, men som har en kommentar
    $persons = Person::getAllWithoutAllergiesButWithComment($current_larp);
    if (!empty($persons) && count($persons) > 0) {
        echo "<h3>Special</h3><table class='data'>";
        echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Övrigt</th><th>Vald mat</th></tr>";
        foreach($persons as $person) {
            $registration=$person->getRegistration($current_larp);
            echo "<tr><td>$person->Name</td><td>$person->Email ".contactEmailIcon($person->Name,$person->Email)."</td>";
            echo "<td>$person->PhoneNumber</td><td>$person->FoodAllergiesOther</td><td>" . $registration->getTypeOfFood()->Name . "</td></tr>";
        }
        echo "</table>";
    }
    
    ?>
</div>
