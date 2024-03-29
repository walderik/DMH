<?php

require 'header.php';

$admin = false;
if (isset($_GET['admin'])) $admin = true;


if (!$current_larp->mayRegister() && !$admin) {
    header('Location: index.php');
    exit;
}

$current_groups = $current_user->getUnregisteredAliveGroupsForUser($current_larp);

if (empty($current_groups) && !$admin) {
    header('Location: index.php?error=no_group');
    exit;
}

if ($current_larp->RegistrationOpen == 0 && !$admin) {
    header('Location: index.php?error=registration_not_open');
    exit;   
}

$new_group = null;
if ($admin) $new_group = Group::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['new_group'])) {
        $new_group = Group::loadById($_GET['new_group']);
    }
}

include 'navigation.php';
?>

	<div class="content">
		<?php 
		if (isset($new_group) && !is_null($new_group)) {
            echo "<h1>Anmälan av gruppen '$new_group->Name' till $current_larp->Name</h1>";
        } else {
            echo "<h1>Anmälan av grupp till $current_larp->Name</h1>";
		} ?>
		<form action="logic/group_registration_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="insert"> 
    		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $current_larp->Id ?>">


			<p>När en grupp är anmäld till lajvet går det för karaktärer att anmäla sig som medlemmar i gruppen. <br>
			   Du som gruppansvarig, har möjlighet att ta bort någon ur gruppen om någon anmäler sig till den men inte hör till den.<br><br>
			   Efter anmälan går det inte längre att redigera gruppen.
			   </p>
				
				
			<div class="question">
				<label for="GroupId">Grupp</label>&nbsp;<font style="color:red">*</font><br>
				<?php 
				if (isset($new_group) && !is_null($new_group)) {
				    selectionByArray('Group', $current_groups, false, true, $new_group->Id);
				} else {
				    selectionByArray('Group', $current_groups, false, true);
				} ?>
				
			</div>
            <div class="question">
    			<label for="WantIntrigue">Vill gruppen ha intriger?</label>&nbsp;<font style="color:red">*</font>
    			<div class="explanation">Oavsett vad ni svarar på den här frågan kan det hända att ni får/inte får intriger. Men vi ska ha era önskemål i åtanke.</div>
    			<input type="radio" id="WantIntrigue_yes" name="WantIntrigue" value="1" checked="checked"> 
    			<label for="WantIntrigue_yes">Ja</label><br> 
    			<input type="radio" id="WantIntrigue_no" name="WantIntrigue" value="0"> 
    			<label for="WantIntrigue_no">Nej</label>
    		</div>
			<div class="question">
    			<label for="RemainingIntrigues">Kvarvarande intriger</label>
    			<div class="explanation">Har gruppen någon pågående/oavslutad intrig sedan tidigare? </div>
				<textarea class="input_field" id="RemainingIntrigues" name="RemainingIntrigues" rows="4" cols="100" maxlength="60000"></textarea>
            </div>

			
			<div class="question">
				<label for="ApproximateNumberOfMembers">Antal medlemmar</label>&nbsp;<font style="color:red">*</font> 
					<div class="explanation">Ungefär hur många
					gruppmedlemmar kommer ni att bli?</div>
					<input class="input_field" type="number"
					id="ApproximateNumberOfMembers"
					name="ApproximateNumberOfMembers"  maxlength="5" min="1" max="40" required>
			</div>
			<div class="question">
    			<label for="HousingRequest">Boende</label>&nbsp;<font style="color:red">*</font>
    			<div class="explanation">Hur vill gruppen helst bo? Vi kan inte garantera plats i hus.</div>
                <?php
    
                HousingRequest::selectionDropdown($current_larp, false,true);
                
                ?>
            </div>
            <div class="question">
    			<label for="NeedFireplace">Behöver ni eldplats?</label><br> 
    			<input type="radio" id="NeedFireplace_yes" name="NeedFireplace" value="1"> 
    			<label for="NeedFireplace_yes">Ja</label><br> 
    			<input type="radio" id="NeedFireplace_no" name="NeedFireplace" value="0" checked="checked"> 
    			<label for="NeedFireplace_no">Nej</label>
    		</div>


			<div class="question">
				<label for="TentType">Typ av tält</label>
				<div class="explanation">Om gruppen har in-lajv tält. Vilken typ av tält är det och vilken färg har det?</div>
				<input class="input_field" type="text" id="TentType" name="TentType"  maxlength="200">
			</div>

			<div class="question">
				<label for="TentSize">Storlek på tält</label> 
				<div class="explanation">Om gruppen har tält. Hur stort är tältet/tälten?</div>
				<input class="input_field" type="text" id="TentSize" name="TentSize"  maxlength="200">
			</div>
			
			<div class="question">
				<label for="TentHousing">Vilka ska bo i tältet</label> 
				<div class="explanation">Om gruppen har tält. Vilka ska bo i det?</div>
				<textarea class="input_field" id="TentHousing" name="TentHousing" rows="4" cols="100" maxlength="60000"></textarea>
			</div>

			<div class="question">
				<label for="TentPlace">Önskad placering</label> 
				<div class="explanation">Om gruppen har tält. Var skulle du vilja få slå upp det? Detta är ett önskemål och vi ska försöka ta hänsyn till det, men vi lovar inget.</div>
				<input class="input_field" type="text" id="TentPlace" name="TentPlace"  maxlength="200">
			</div>

			

			<div class="question">
			Genom att kryssa i denna ruta så lovar jag med
			heder och samvete att jag har läst igenom alla 
			<a href="<?php echo $current_larp->getCampaign()->Homepage?>" target="_blank">hemsidans regler</a> och
			förmedlat dessa till mina gruppmedlemmar. Vi har även alla godkänt
			dem och är införstådda med vad som förväntas av oss som grupp av
			deltagare på lajvet. Om jag inte har läst reglerna så kryssar jag
			inte i denna ruta.&nbsp;<font style="color:red">*</font><br>
			
			<input type="checkbox" id="rules" name="rules" value="Ja" required>
  			<label for="rules">Jag lovar</label> 
			</div>


			<div class="question">
			Efter att gruppen är anmäld kommer den att godkännas av arrangörerna innan den blir möjlig att välja för karaktärer. Du kommer att få ett mail när gruppen är godkänd.
			
			 
			</div>


			<?php 
			if ($admin) {
			    //Om bara tittar på formuläret som arrangör får man inte lyckas skicka in
			    $type = "button";
			} else {
			    $type = "submit";
		    }
		    
			    ?>

			  <input type="<?php echo $type ?>" value="Anmäl">

		</form>
	</div>

</body>
</html>