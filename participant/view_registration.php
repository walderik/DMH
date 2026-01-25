<?php

include_once 'header.php';

$person = $current_person;

$registration = Registration::loadByIds($person->Id, $current_larp->Id);

if (!isset($registration)) {
    header('Location: index.php'); // personen är inte anmäld
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
        if ($operation == "updateArrivalDate") {
            $registration->ArrivalDate = $_POST['ArrivalDate'];
            $registration->update();
        }
    }
}


include 'navigation.php';

?>
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-file"></i>
			Anmälan för <?php echo $person->Name;?>
		</div>
   		<div class='itemcontainer'>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Ansvarig vuxen</div>
				<?php if (!empty($registration->GuardianId)) echo $registration->getGuardian()->Name; else echo showStatusIcon(false); ?>
    			</div>
		    <?php 
		    }
		    ?>

		    <?php 
		    $minors = $person->getGuardianFor($current_larp);
		    if (!empty($minors)) {
		        echo "<div class='itemcontainer'>";
		        echo "<div class='itemname'>Ansvarig vuxen för</div>";
		        $minor_str_arr = array();
		        foreach ($minors as $minor) {
		            $minor_str_arr[] = $minor->Name;
		        }
		        echo implode(", ", $minor_str_arr);
		        echo "</div>";
		    }
		    ?>

			<?php if (TypeOfFood::isInUse($current_larp)) { ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Typ av mat</div>
				<?php echo TypeOfFood::loadById($registration->TypeOfFoodId)->Name;?>
    			</div>
			<?php } ?>

			<?php if (isset($registration->FoodChoice)) { ?>
	   	   		<div class='itemcontainer'>
               	<div class='itemname'>Matalternativ</div>
				<?php echo $registration->FoodChoice; ?>
    			</div>
			<?php } ?>

			<?php if ($current_person->getAgeAtLarp($current_larp) >= $current_larp->MinimumAgeNPC) {?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>NPC åtagande</div>
    			<?php echo $registration->NPCDesire;?>
    			</div>
			<?php } ?>

			<?php if (HousingRequest::isInUse($current_larp)) { ?>
	   	   		<div class='itemcontainer'>
               	<div class='itemname'>Önskat boende</div>
				<?php 
				    $housingrequest = $registration->getHousingRequest();
				    if(!empty($housingrequest)) echo $housingrequest->Name;
			    ?>
    			</div>
			<?php } ?>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Boendehänsyn</div>
			<?php echo nl2br(htmlspecialchars($registration->LarpHousingComment)); ?>
			</div>

			<?php if ($current_larp->hasArrivalDateQuestion()) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'><label for="ArrivalDate">När anländer du till lajvområdet?</label> <font style="color:red">*</font></div>
				<?php 
				$today = date("Y-m-d");
				if ($today > $current_larp->ArrivalDateLatestChange) {
				    echo $registration->ArrivalDate;
				} else {
    				if (!empty(trim($current_larp->ArrivalDateText))) echo nl2br(htmlspecialchars($current_larp->ArrivalDateText))."<br>";
    				if (isset($current_larp->ArrivalDateLatestChange)) echo "Du har möjlighet att ändra på det här fram till $current_larp->ArrivalDateLatestChange.<br>";
    
                    $formatter = new IntlDateFormatter(
                        'sv-SE',
                        IntlDateFormatter::FULL,
                        IntlDateFormatter::FULL,
                        'Europe/Stockholm',
                        IntlDateFormatter::GREGORIAN,
                        'EEEE d MMMM'
                        );
                    
                    $arrivalEnd = new DateTime(substr($current_larp->StartDate,0,10));
                    $arrivalStart = new DateTime(substr($current_larp->StartDate,0,10));
                    $arrivalStart   = date_modify($arrivalStart,"-".$current_larp->ArrivalDateChoice." days");
                    
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='operation' value='updateArrivalDate'>";
                    for($i = $arrivalStart; $i <= $arrivalEnd; $i->modify('+1 day')){
                        $datestr = $i->format("Y-m-d");
    
                        echo "<input type='radio' id='ArrivalDate$datestr' name='ArrivalDate' value='$datestr'";
                        if ($registration->ArrivalDate == $datestr) echo "checked='checked'";
                        echo ">";
                        echo "<label for='day$datestr'> ".$formatter->format($i)."</label><br>";
                    }
				}
				echo "<button type='submit'>Ändra</button>";
				echo "</form>";
                ?>
            	</div>
		
			
			
			<?php }?>




			<?php if ($current_larp->hasTentQuestions()) {?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Typ av tält</div>
    			<?php echo nl2br(htmlspecialchars($registration->TentType)); ?>
    			</div>
    
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Storlek på tält</div>
    			<?php echo nl2br(htmlspecialchars($registration->TentSize)); ?>
    			</div>
    
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Vilka ska bo i tältet</div>
    			<?php echo nl2br(htmlspecialchars($registration->TentHousing)); ?>
    			</div>
    
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Önskad placering</div>
    			<?php echo nl2br(htmlspecialchars($registration->TentPlace)); ?>
    			</div>
			<?php } ?>
	
			<?php if ($current_person->getAgeAtLarp($current_larp) >= $current_larp->MinimumAgeOfficial) {?>
    			<?php if (OfficialType::isInUse($current_larp)) { ?>
    	   	   		<div class='itemcontainer'>
                   	<div class='itemname'>Funktionärsönskemål</div>
    				<?php echo commaStringFromArrayObject($registration->getOfficialTypes());?>
        			</div>
    			<?php } ?>
			<?php } ?>
			
			<?php if ($current_larp->hasPhotograph()) { ?>
			<div class='itemcontainer'>
	       	<div class='itemname'>Tillåter fotografering</div>
    			<?php echo ja_nej($registration->approvesPhotography()); ?>
    			</div>
			<?php } ?>
			

	   		<div class='itemcontainer'>
           	<div class='itemname'>Anmälda karaktärer</div>
			<?php 
			$roles = $person->getRolesAtLarp($current_larp);
			foreach($roles as $role) {
			    echo $role->getViewLink();
			    if ($role->isMain($current_larp)) echo " (Huvudkaraktär)";
			    echo "<br>";
			}
			
			 ?>
			</div>
		</div>	
	</div>


</body>
</html>
