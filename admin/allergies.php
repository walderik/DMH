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
            echo $person->Name . " " . $person->getTypeOfFood()->Name . "<br>";
        }
        echo "<br>";
    }
     
    
    //Multipla allergier
       
    ?>
</div>
