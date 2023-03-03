<?php
include_once 'header_subpage.php';
?>

<div class="content">
    <h1>Allergier</h1>

    Just nu är det <?php echo count(Registration::allBySelectedLARP()); ?> anmälda deltagare.<br>
    
    <?php 
    
    $allAllergies = NormalAllergyType::all();
    
    foreach($allAllergies as $allergy) {
        $persons = Person::getAllWithSingleAllergy($allergy);
        echo "Enbart " . $allergy->Name . "<br>";
        foreach($persons as $person) {
            echo $person->Name . " "  . $person->FoodAllergiesOther . $person->getTypeOfFood()->Name . "<br>";
        }
        echo "<br>";
    }
     
    
    //Multipla allergier
    $persons = Person::getAllWithMultipleAllergies();
    echo "Multipla vanliga allergier<br>";
    foreach($persons as $person) {
        echo $person->Name . " " . commaStringFromArrayObject($person->getNormalAllergyTypes()) . " " . $person->FoodAllergiesOther . $person->getTypeOfFood()->Name . "<br>";
    }
    echo "<br>";
    
    
    //TODO Hitta alla som inte har någon vald allergi, men som har en kommentar
    
    ?>
</div>
