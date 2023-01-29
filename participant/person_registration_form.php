<?php

require 'header.php';

$current_person;

echo "Start";
echo $_SERVER["REQUEST_METHOD"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "post";
    if (isset($_POST['PersonId'])) {
        $PersonId = $_POST['PersonId'];
    }
    else {
        echo "Exit 1";
        //header('Location: index.php');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo "get";
    if (isset($_GET['PersonId'])) {
        echo "1";
        $PersonId = $_GET['PersonId'];
        echo $PersonId;
    }
    else {
        echo "Exit 2";
        //header('Location: index.php');
    }
}

if (isset($PersonId)) {
    $current_person = Person::loadById($PersonId);
}
else {
    echo "Exit 3";
    //header('Location: index.php?error=no_person');
}

$roles = $current_person->getRoles();

if (empty($roles)) {
    header('Location: index.php?error=no_role');
    exit;
}


?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


	<div class="content">
		<h1>Anmälan av <?php echo $current_person->Name;?> till <?php echo $current_larp->Name;?></h1>
		<form action="logic/person_registration_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="insert"> 
    		<input type="hidden" id="LarpId" name="Id" value="<?php echo $current_larp->Id ?>">
    		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $current_person->Id ?>">


			<p>När anmälan är gjort går det varken att redigera deltagaren eller någon av karaktärerna.
			   </p>
				
				
			<div class="question">
				<label for="RoleId">Karaktärer</label><br>
				<div class="explanation">Vilka karaktärer vill du spela på lajvet?<br>
				     För varje karaktär behöver du också ange vilken typ av karaktär det är.<br>
				     <?php RoleType::helpBox(true); ?><br>
				     <br>Och vilka intriger den karaktären vill ha<br>
				     <?php IntrigueType::helpBox(true); ?></div>
			
        			<?php 
        			foreach($roles as $role) {
        			    echo '<div class="role">';
        			    echo '<h3><input type="checkbox" id="role"'.$role->Id.'" name="role"'.$role->Id.'" value="Bike">';
        			    echo '<label for="role"'.$role->Id.'">'.  $role->Name . '</label></h3>';
        			    
        			    
        			    echo '<table border=0><tr><td valign="top">';
        			    RoleType::selectionDropdown(false,true);
        			    echo '</td><td>&nbsp;</td><td valign="top">';
        			    IntrigueType::selectionDropdown(true,false);
        			    echo '</td></tr></table>';
        			    echo '</div>';
        			}
        			
        			
        			
        			
        			
        			?>
			
			
			
			
			</div>
			
			
			<div class="question">
    			<label for="HousingRequest">Boende</label>
    			<div class="explanation">Hur vill du helst bo? Vi kan inte garantera plats i hus. <br><?php HousingRequest::helpBox(true); ?></div>
                <?php
    
                HousingRequest::selectionDropdown(false,true);
                
                ?>
            </div>





			<div class="question">
    			<label for="NPCDesire">NPC</label>
    			<div class="explanation">Kan du tänka dig att ställa upp som NPC?<br>
NPC = Non Player Character, en roll som styrs helt/delvis av arrangörsgruppen och spelas en kortare stund under lajvet för att skapa scener/händelser. Vi kommer återkomma till de som är intresserade, men skriv gärna en rad om du redan nu har några idéer.
				</div>
                <input type="text" id="NPCDesire" name="NPCDesire" size="100" maxlength="250">
            </div>


			<div class="question">
    			<label for="OfficialType">Funktionär</label>
    			<div class="explanation">Det är mycket som behövs för att ett lajv ska fungera på plats. <br>   
Allt ifrån att någon måste laga mat till att någon måste se till att det finns toapapper på dassen.   <br> 
Just nu söker vi någon som kan ta Trygghetsansvar, folk till saloonen och någon som kan hålla i spel på saloonen.  <br>  
Säkert finns det också något som du gärna kan hjälpa till med och som vi inte har tänkt på. Beroende på arbetsbörda återbetalas delar eller hela anmälningsavgifter efter lajvet.<br>
<?php OfficialType::helpBox(true); ?></div>
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
			hemsidans regler, har godkänt dem och är införstådd med vad som förväntas av mig som deltagare 
			på lajvet. Om jag inte har läst reglerna så kryssar jag inte i denna ruta.</div>

			<input type="checkbox" id="Rules" name="Rules" value="Ja" required>
  			<label for="Rules">Jag lovar</label> 
			</div>
			

			  <input type="submit" value="Anmäl">

		</form>
	</div>

</body>
</html>