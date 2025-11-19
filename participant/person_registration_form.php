<?php

require 'header.php';

$admin = false;
if (isset($_GET['admin'])) $admin = true;

if ($admin) {
    $roles = array();
    $role = Role::newWithDefault();
    $role->Name = "Karaktärens namn";
    $roles[] = $role;
}
else {
    if (!$current_larp->mayRegister()) {
        header('Location: index.php');
        exit;
    }

    if ($current_person->isRegistered($current_larp)) {
        header('Location: index.php?error=already_registered');
        exit;
        
    }

    
    
    $roles = $current_person->getAliveRoles($current_larp);
    
    if (empty($roles)) {
        header('Location: index.php?error=no_role');
        exit;
    
    }
    
    //Kolla att minst en karaktär går att anmäla
    $registerable_roles = array();
    foreach ($roles as $role) {
        if ($role->groupIsRegisteredApproved($current_larp)) $registerable_roles[] = $role;
    }
    if (empty($registerable_roles)) {
        header('Location: index.php?error=no_role_may_register');
        exit;
    }
    
    if ($current_person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAge) {
        header('Location: index.php?error=too_young_for_larp');
        exit;
    }
}

$age = $current_person->getAgeAtLarp($current_larp);

include 'navigation.php';
?>

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-user"></i>
		Anmälan av <?php echo $current_person->Name;?><br>till <?php echo $current_larp->Name;?>
	</div>

	<form action="logic/person_registration_form_save.php" method="post"  
	   onsubmit="return confirm('Är allt rätt inmatat? Om du fortsätter kommer du inte längre att kunna redigera någon av de anmälda karaktärerna.')">
		<input type="hidden" id="operation" name="operation" value="insert"> 
		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $current_larp->Id ?>">
		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $current_person->Id ?>">


		<div class='itemcontainer'>
		<strong>När anmälan är gjord går det inte att ändra på de anmälda karaktärerna.</strong>
		</div>
				
			<?php 
			if (!empty($current_larp->ContentDescription)) {

			?>
			<div class='itemcontainer'>
	       	<div class='itemname'>Lajvets innehåll <font style="color:red">*</font></div>
			<?php echo nl2br(linkify(htmlspecialchars($current_larp->ContentDescription))) ?><br>

			<input type="checkbox" id="Content" name="Content" value="Ja" required>
  			<label for="Content">Jag är införstådd med vad det är för typ av lajv</label> 
			</div>
			
			
		    <?php 
		    }
		    ?>

		    <?php 
		    if ($age < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="GuardianInfo">Ansvarig vuxen</label> <font style="color:red">*</font></div>
			Eftersom <?php echo $current_person->Name; ?> bara är <?php  echo $current_person->getAgeAtLarp($current_larp); ?> år behövs en ansvarig vuxen. 
    			Den ansvarige måste vara anmäld till lajvet, tillfrågad och accepera ansvaret.<br>
    			Skriv in namn ELLER personnummer på den ansvarige. Personnummer anges på formen ÅÅÅÅMMDD-NNNN. Den ansvarige måste redan vara anmäld. Man kan bara ha en ansvarig vuxen.
    			Om den ansvarige inte går att hitta kommer inte din anmälan att kunna godkännas förrän det är löst.
				<br>
			<input type="text" id="GuardianInfo" name="GuardianInfo" size="100" maxlength="200" placeholder="Ange namn ELLER personnummer på den som är ansvarig vuxen">
            </div>
		    
		    <?php 
		    }
		    ?>
			
			<?php if ($current_larp->NoRoles == 1) {
			    
			    echo "<hr>";
				
    	        print_participant_question_start(
    		            "Karaktär",
    		            "Vilken karaktär vill du spela på lajvet?",
    		            true,
    		            false,
    		            false);
    	        selectionDropDownByArray('roleId' , $registerable_roles, true);
		        print_participant_question_end(false);
    		    
    				
    		    print_participant_textarea(
    		        "Intrigideer",
    		        "Är det någon typ av spel du särskilt önskar eller något som du inte önskar spel på?  Exempel kan vara 'Min karaktär har: en skuld till en icke namngiven karaktär/mördat någon/svikit sin familj/ett oäkta barn/lurat flera personer på pengar.'",
    		        "IntrigueIdeas",
    		        "",
    		        "rows='4' maxlength='60000'",
    		        false,
    		        false);
    
    		    if (IntrigueType::isInUseForRole($current_larp)) {
    		        print_participant_question_start(
    		            "Intrigtyper",
    		            "Vilken typ av intriger vill du ha?",
    		            false,
    		            false,
    		            false);
    		        IntrigueType::selectionDropdownRole($current_larp, true, false);
    		        print_participant_question_end(false);
    		    }
			}
		?>

			<hr>
			<?php if (TypeOfFood::isInUse($current_larp)) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'><label for="TypesOfFoodId">Viken typ av mat vill du äta?</label> <font style="color:red">*</font></div>
    			<?php TypeOfFood::selectionDropdown($current_larp, false, true); ?>
    			</div>
			<?php } ?>
			
			
			<?php 
			$paymentInformation = PaymentInformation::get(date("Y-m-d"), $age, $current_larp);
			if (!empty($paymentInformation->FoodDescription)) {
			    echo "<div class='itemcontainer'>";
			    echo "<div class='itemname'><label for='FoodChoice'>Vilket matalternativ väljer du?</label>&nbsp;<font style='color:red'>*</font></div>";
			    foreach ($paymentInformation->FoodDescription as $i => $description) {
			        echo "<input type='radio' id='FoodChoice_$description' name='FoodChoice' value='$description' required>";
			        echo "<label for='FoodChoice_$description'>$description, ".$paymentInformation->FoodCost[$i]." SEK</label><br>";
			    }
			    echo "</div>";
			}
			?>
			
			<?php 
			if ($current_larp->chooseParticipationDates()) {
			    echo "<div class='itemcontainer'>";
			    echo "<div class='itemname'><label for='ChooseParticipationDates'>Vilka dagar kommer du att närvara?</label></div>";
			    echo "Informationen används både för matplanering och intrigskapande.<br>";
			    
			    
			    $formatter = new IntlDateFormatter(
			        'sv-SE',
			        IntlDateFormatter::FULL,
			        IntlDateFormatter::FULL,
			        'Europe/Stockholm',
			        IntlDateFormatter::GREGORIAN,
			        'EEEE d MMMM'
			        );
			    
			    $begin = new DateTime(substr($current_larp->StartDate,0,10));
			    $end   = new DateTime(substr($current_larp->EndDate,0,10));
			    
			    for($i = $begin; $i <= $end; $i->modify('+1 day')){
			        $datestr = $i->format("Y-m-d");
			        echo "<input type='checkbox' id='day$datestr' name='ChooseParticipationDates[]' value='$datestr' checked='checked'>";
			        echo "<label for='day$datestr'> ".$formatter->format($i)."</label><br>";
			    }
			    echo "</div>";
			}
			?>
			<hr>
			
			<?php if (HousingRequest::isInUse($current_larp)) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'><label for="HousingRequest">Boende</label> <font style="color:red">*</font></div>
				Hur vill du helst bo? Vi kan inte garantera plats i hus.<br>
                <?php HousingRequest::selectionDropdown($current_larp, false,true); ?>
            	</div>
			<?php } ?>
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="LarpHousingComment">Boendehänsyn på <?php echo $current_larp->Name?></label></div>
			Vill du bo i ett särskilt hus eller dela hus med några särskilda personer? 
				Är där någon du inte vill bo med? Finns det något som bör tas hänsyn till vid fördelning av sovplats? Kan du tänka dig att dela bädd (skriv i så fall ifall du har några preferenser eller om det bara gäller vissa personer och i så fall vilka).
				Skriv det här så gör vi vad vi kan för att uppfylla önskemålen. 
				Fyller du inte i något blir du placerad där vi tror det blir bra.
				<br>
				Om du inte har något, lämna fältet tomt. Du behöver inte heller skriva om du vill bo med din grupp.<br>
				<input type="text" id="LarpHousingComment" name="LarpHousingComment" value="" size="100" maxlength="200" >
			</div>
			
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="TentType">Typ av tält</label></div>
			Om du har med in-lajv tält. Vilken typ av tält är det och vilken färg har det?<br>
			<input type="text" id="TentType" name="TentType"  maxlength="200">
			</div>

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="TentSize">Storlek på tält</label></div>
			Om du har med tält. Hur stort är tältet?<br>
			<input type="text" id="TentSize" name="TentSize"  maxlength="200">
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="TentHousing">Vilka ska bo i tältet</label></div>
			Om du har med tält. Vilka ska bo i det?<br>
			<textarea id="TentHousing" name="TentHousing" rows="4" cols="100" maxlength="60000"></textarea>
			</div>

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="TentPlace">Önskad placering</label></div>
			Om du har med tält. Var skulle du vilja få slå upp det? Detta är ett önskemål och vi ska försöka ta hänsyn till det, men vi lovar inget.<br>
			<input type="text" id="TentPlace" name="TentPlace"  maxlength="200">
			</div>

			<hr>
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="NPCDesire">NPC</label></div>
			Kan du tänka dig att ställa upp som NPC? Vad vill du i så fall göra?<br>
					NPC = Non Player Character, en karaktär som styrs helt/delvis av arrangörsgruppen och spelas en kortare stund under lajvet för att skapa scener/händelser.<br>
					Vi kommer återkomma till de som är intresserade, men skriv gärna en rad om du redan nu har några idéer.<br>
					<br>
					Om du inte är intresserad kan du lämna fältet tomt.
	    
				<br>
                <input type="text" id="NPCDesire" name="NPCDesire" size="100" maxlength="200">
            </div>

			<?php if (OfficialType::isInUse($current_larp)) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'><label for="OfficialType">Intresseranmälan för funktionär</label></div>
				Det är mycket som behövs för att ett lajv ska fungera på plats. <br>   
                    Allt ifrån att någon måste laga mat till att någon måste se till att det finns toapapper på dassen.<br> 
                    Säkert finns det också något som du gärna kan hjälpa till med och som vi inte har tänkt på.<br> 
                    Beroende på arbetsbörda återbetalas delar eller hela anmälningsavgifter efter lajvet.
				<br>
                <?php OfficialType::selectionDropdown($current_larp, true,false); ?>
            	</div>
			<?php } ?>
			
			
		
			<hr>
			<div class='itemcontainer'>
	       	<div class='itemname'>Godkända karaktärer&nbsp;<font style="color:red">*</font></div>
			Alla karaktärer ska godkännas. Du kommer att får ett mail när någon i arrangörsgruppen har 
			  	läst igenom din karaktär och godkänt den. Om den inte blir godkänd kommer du att få möjlighet 
			  	att ändra karaktären i samarbete med arrangörerna.
			  
			<br>
			<input type="checkbox" id="approval" name="approval" value="" required>
  			<label for="approval">Jag förstår</label> 
			</div>


			<div class='itemcontainer'>
	       	<div class='itemname'>Regler&nbsp;<font style="color:red">*</font></div>
			Genom att kryssa i denna ruta så lovar jag med heder och samvete att jag har läst igenom alla 
			<a href="<?php  echo $current_larp->getCampaign()->Homepage?>" target="_blank">hemsidans regler</a>, har godkänt dem och är införstådd med vad som förväntas av mig som deltagare 
			på lajvet. Om jag inte har läst reglerna så kryssar jag inte i denna ruta.<br>

			<input type="checkbox" id="Rules" name="Rules" value="Ja" required>
  			<label for="Rules">Jag lovar</label> 
			</div>
			
			
			<?php 
			if ($admin) {
			    //Om bara tittar på formuläret som arrangör får man inte lyckas skicka in
			    $type = "button";
			} else {
			    $type = "submit";
		    }
	       ?>
			

			  <div class='center'><input type="<?php echo $type ?>" class='button-18' value="Anmäl"></div>

		</form>
	</div>

</body>
</html>