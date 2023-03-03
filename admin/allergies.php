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
        echo "Enbart " . $allergy->Name . "<br>";
        foreach($persons as $person) {
            echo $person->Name . " "  . $person->FoodAllergiesOther . $person->getTypeOfFood()->Name . "<br>";
        }
        echo "<br>";
    }
     
    
    //Multipla allergier
    $persons = Person::getAllWithMultipleAllergies($current_larp);
    echo "Multipla vanliga allergier<br>";
    foreach($persons as $person) {
        echo $person->Name . " " . commaStringFromArrayObject($person->getNormalAllergyTypes()) . " " . $person->FoodAllergiesOther . $person->getTypeOfFood()->Name . "<br>";
    }
    echo "<br>";
    
    
    //Hitta alla som inte har någon vald allergi, men som har en kommentar
    $persons = Person::getAllWithoutAllergiesButWithComment();
    echo "Special<br>";
    foreach($persons as $person) {
        echo $person->Name . " " . $person->FoodAllergiesOther . $person->getTypeOfFood()->Name . "<br>";
    }
    echo "<br>";
    
    
    ?>
</div>
