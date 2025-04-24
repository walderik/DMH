<?php
include_once 'header.php';
include 'navigation.php';
?>

<div class="content">
    <h1>Köket</h1>
    <div class='linklist'>
    <a href="reports/matlista.php?variant=1" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla deltagares matval</a><br>  
    <a href="reports/matlista.php?variant=2" target="_blank"><i class="fa-solid fa-file-pdf"></i>Lista med alla deltagares matval samt allergier</a><br>  
    <a href="reports/allergy_list.php?variant=1" target="_blank"><i class="fa-solid fa-file-pdf"></i>Allergier & typ av mat (variant 1)</a><br>  
    <a href="reports/allergy_list.php?variant=2" target="_blank"><i class="fa-solid fa-file-pdf"></i>Allergier & typ av mat (variant 2)</a><br>  
    </div> 
    
    Totalt är det <?php echo Registration::countAllComing($current_larp); ?> anmälda personer.<br>
    <h2>Vald mat</h2>
    <?php 
    $foodChoises = Registration::getFoodVariants($current_larp);
    $hasFoodChoices = false;
    foreach($foodChoises as $foodChoise) {
        if (!empty($foodChoise[0])) $hasFoodChoices = true;
    }
    echo "<table class='small_data'>";
    foreach($foodChoises as $foodChoise) {
        echo "<tr>";
        if ($hasFoodChoices) echo "<td>".$foodChoise[0] . "</td>";
        echo "<td>" . $foodChoise[1] . "</td><td>" . $foodChoise[2] . " st</td></tr>"; 
    }
    echo "</table>";

    
    
    ?>

	<h2>Allergier</h2>
    
    <?php 
    if (NormalAllergyType::isInUse()){
        $allAllergies = NormalAllergyType::all();
        
        foreach($allAllergies as $allergy) {
            $persons = Person::getAllWithSingleAllergy($allergy, $current_larp);
            if (isset($persons) && count($persons) > 0) {
                echo "<h3>Enbart $allergy->Name</h3><table class='data'>";
                echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Övrigt</th><th>Vald mat</th>";
                if ($hasFoodChoices) echo "<th>Matalternativ</th>";
                echo "</tr>";
                foreach($persons as $person) {
                    $registration=$person->getRegistration($current_larp);
                    echo "<tr><td>$person->Name</td><td>$person->Email ".contactEmailIcon($person)."</td>";
                    echo "<td>$person->PhoneNumber</td><td>$person->FoodAllergiesOther</td><td>".$registration->getTypeOfFood()->Name."</td>";
                    if ($hasFoodChoices) echo "<td>$registration->FoodChoice</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
         
        
        //Multipla allergier
        $persons = Person::getAllWithMultipleAllergies($current_larp);
        if (!empty($persons) && count($persons) > 0) {
            echo "<h3>Multipla vanliga allergier</h3>";
            echo "<table class='data'>";
            echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Allergier</th><th>Övrigt</th><th>Vald mat</th>";
            if ($hasFoodChoices) echo "<th>Matalternativ</th>";
            echo "</tr>";
            foreach($persons as $person) {
                $registration=$person->getRegistration($current_larp);
                echo "<tr><td>$person->Name</td><td>$person->Email ".contactEmailIcon($person)."</td>";
                echo "<td>$person->PhoneNumber</td><td>" . commaStringFromArrayObject($person->getNormalAllergyTypes()) . "</td>";
                echo "<td>$person->FoodAllergiesOther</td><td>" . $registration->getTypeOfFood()->Name . "</td>";
                if ($hasFoodChoices) echo "<td>$registration->FoodChoice</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    //Hitta alla som inte har någon vald allergi, men som har en kommentar
    //TODO borde kanske sorteras per matalternativ
    
    $persons = Person::getAllWithoutAllergiesButWithComment($current_larp);
    if (!empty($persons) && count($persons) > 0) {
        echo "<h3>Special</h3><table class='data'>";
        echo "<tr><th>Namn</th><th>Epost</th><th>Telefon</th><th>Övrigt</th><th>Vald mat</th>";
        if ($hasFoodChoices) echo "<th>Matalternativ</th>";
        echo "</tr>";
        foreach($persons as $person) {
            $registration=$person->getRegistration($current_larp);
            echo "<tr><td>$person->Name</td><td>$person->Email ".contactEmailIcon($person)."</td>";
            echo "<td>$person->PhoneNumber</td><td>$person->FoodAllergiesOther</td><td>" . $registration->getTypeOfFood()->Name . "</td>";
            if ($hasFoodChoices) echo "<td>$registration->FoodChoice</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    ?>
</div>
