<?php

require 'header.php';

if (!$current_larp->mayRegister()) {
    header('Location: index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['PersonId'])) {
        $PersonId = $_POST['PersonId'];
    }
    else {

        header('Location: index.php');
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['PersonId'])) {

        $PersonId = $_GET['PersonId'];
        
    }
    else {

        header('Location: index.php');
        exit;
    }
}

if (isset($PersonId)) {
    $current_person = Person::loadById($PersonId);
}
else {

    header('Location: index.php?error=no_person');
    exit;
}

if ($current_person->UserId != $current_user->Id) {
    header('Location: index.php');
    exit;
}


$roles = $current_person->getAliveRoles();

if (empty($roles)) {
    header('Location: index.php?error=no_role');
    exit;

}

//Kolla att minst en karaktär går att anmäla
$mayRegister = false;
foreach ($roles as $role) {
    if ($role->groupIsRegistered($current_larp)) $mayRegister = true;
}
if ($mayRegister == false) {
    header('Location: index.php?error=no_role_may_register');
    exit;
}

if ($current_person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAge) {
    header('Location: index.php?error=too_young_for_larp');
    exit;
}

include 'navigation.php';
?>

	<div class="content">
		<h1>Anmälan av <?php echo $current_person->Name;?> till <?php echo $current_larp->Name;?></h1>
		<form action="logic/person_registration_form_save.php" method="post"  
		   onsubmit="return confirm('Är allt rätt inmatat? Om du fortsätter kommer du inte längre att kunna redigera deltagaren eller någon av de anmälda karaktärerna.')">
    		<input type="hidden" id="operation" name="operation" value="insert"> 
    		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $current_larp->Id ?>">
    		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $current_person->Id ?>">


			<p>
			<strong>När anmälan är gjord går det varken att redigera deltagaren eller någon av de anmälda karaktärerna.</strong>
			</p>
				
		    <?php 
		    if ($current_person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<div class="question">
    			<label for="GuardianInfo">Ansvarig vuxen</label>&nbsp;<font style="color:red">*</font>
    			<div class="explanation">Eftersom <?php echo $current_person->Name; ?> bara är <?php  echo $current_person->getAgeAtLarp($current_larp); ?> år behövs en ansvarig vuxen. 
    			Den ansvarige måste vara tillfrågad och accepera ansvaret.<br>
    			Skriv in namn eller personnummer på den ansvarige. Personnummer anges på formen ÅÅÅÅMMDD-NNNN.
    			Om den ansvarige inte går att hitta kommer inte din anmälan att kunna godkännas förrän det är löst.
				</div>
				<input class="GuardianInfo" type="text" id="GuardianInfo" name="GuardianInfo" size="100" maxlength="25" >
            </div>
		    
		    <?php 
		    }
		    ?>
				
			<div class="question">
				<label for="RoleId">Karaktärer</label>&nbsp;<font style="color:red">*</font><br>
				<div class="explanation">Vilka karaktärer vill du spela på lajvet? Avmarkera checkboxen för de karaktärer som inte ska vara med.<br>
				     En av dina karaktärer är din huvudkaraktär. Vi måste veta vilken.<br>
				     Andra karaktärer är roller du spelar en liten kort tid under lajvet eller har som reserv om din huvudkaraktär blir ospelbar.<br>
				</div>
				<table class="list">
        			<?php 
        			foreach($roles as $role) {
        			    if ($role->groupIsRegistered($current_larp)) {
            			    echo "<tr><td>\n";
            			    echo "<input type='checkbox' id='roleId$role->Id' name='roleId[]' value='$role->Id' checked='checked'>";
            			    echo "\n";
            			    echo "<label for='roleId$role->Id'>$role->Name</label>\n";
            			    echo "</td><td>";
            			    echo "<input type='radio' id='mainRole$role->Id' name='IsMainRole' value='$role->Id' required>\n";
            			    echo "<label for='mainRole$role->Id'>Huvudkaraktär</label><br><br>\n";   			    
            			    echo '</td></tr>';
        			    }
        			    else {
        			        echo "<div class='role'>\n";
        			        echo "<h3>$role->Name</h3>";
        			        echo "Karaktären kan inte anmälas eftersom gruppen " . $role->getGroup()->Name . " inte är anmäld.";
        			        echo "</div>";
        			    }
        			}		

        			?>
        		</table>
        	</div>
			
			
			<div class="question">
    			<label for="HousingRequest">Boende</label>&nbsp;<font style="color:red">*</font>
    			<div class="explanation">Hur vill du helst bo? Vi kan inte garantera plats i hus. <br><?php HousingRequest::helpBox(true); ?></div>
                <?php
    
                HousingRequest::selectionDropdown(false,true);
                
                ?>
            </div>


			<div class="question">
    			<label for="NPCDesire">NPC</label>
    			<div class="explanation">Kan du tänka dig att ställa upp som NPC? Vad vill du i så fall göra?<br>
					NPC = Non Player Character, en karaktär som styrs helt/delvis av arrangörsgruppen och spelas en kortare stund under lajvet för att skapa scener/händelser.<br>
					Vi kommer återkomma till de som är intresserade, men skriv gärna en rad om du redan nu har några idéer.<br>
					<br>
					Om du inte är intresserad kan du lämna fältet tomt.
	    
				</div>
                <input class="input_field" type="text" id="NPCDesire" name="NPCDesire" size="100" maxlength="200">
            </div>


			<div class="question">
    			<label for="OfficialType">Intresseranmälan för funktionär</label>
    			<div class="explanation">
        			Det är mycket som behövs för att ett lajv ska fungera på plats. <br>   
                    Allt ifrån att någon måste laga mat till att någon måste se till att det finns toapapper på dassen.<br> 
                    Säkert finns det också något som du gärna kan hjälpa till med och som vi inte har tänkt på.<br> 
                    Beroende på arbetsbörda återbetalas delar eller hela anmälningsavgifter efter lajvet.<br>
    				<?php OfficialType::helpBox(true); ?>
				</div>
                <?php
    
                OfficialType::selectionDropdown(true,false);
                
                ?>
            </div>


			<div class="question">Godkända karaktärer&nbsp;<font style="color:red">*</font><br>
			<div class="explanation">
			  	Alla karaktärer ska godkännas. Du kommer att får ett mail när någon i arrangörsgruppen har 
			  	läst igenom din karaktär och godkänt den. Om den inte blir godkänd kommer du att få möjlighet 
			  	att ändra karaktären i samrbete med arrangörerna. Nu när du skickar in den är den så klart 
			  	ännu inte godkänd och därför kan du bara välja 'Nej'.
			  
			</div>
			<input type="radio" name="approval" value="" required />
  			<label for="approval">Nej</label> 
			</div>


			<div class="question">
					Regler&nbsp;<font style="color:red">*</font><br>
    			<div class="explanation">Genom att kryssa i denna ruta så lovar jag med heder och samvete att jag har läst igenom alla 
			<a href="https://dmh.berghemsvanner.se/" target="_blank">hemsidans regler</a>, har godkänt dem och är införstådd med vad som förväntas av mig som deltagare 
			på lajvet. Om jag inte har läst reglerna så kryssar jag inte i denna ruta.</div>

			<input type="checkbox" id="Rules" name="Rules" value="Ja" required>
  			<label for="Rules">Jag lovar</label> 
			</div>
			

			  <input type="submit" value="Anmäl">

		</form>
	</div>

</body>
</html>