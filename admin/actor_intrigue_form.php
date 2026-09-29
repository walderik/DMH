<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $intrigueActor = IntrigueActor::loadById($_GET['IntrigueActorId']);
    $name = $_GET['name'];
    

}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

if (isset($_GET['section'])) $section = $_GET['section'];
else $section = "";



include 'navigation.php';
include 'intrigue_navigation.php';
?>
    

    <div class="content"> 
    <h1>Intrig för <?php echo $name?> <a href="view_intrigue.php?Id=<?php echo $intrigueActor->IntrigueId?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/view_intrigue_logic.php" method="post">
		<input type="hidden" id="operation" name="operation" value="update_intrigue_actor"> 
		<input type="hidden" id="Id" name="Id" value="<?php echo $intrigueActor->IntrigueId ?>">
		<input type="hidden" id="IntrigueActorId" name="IntrigueActorId" value="<?php echo $intrigueActor->Id ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<!-- <input type="hidden" id="Section" name="Section" value="<?php echo $section;?>">  -->
		<table>
		
		    <tr><td>Gemensam text</td>
		    	<td>
           <?php 
           if (isset($intrigueActor->RoleId)) {
                echo "<input type='radio' id='HasCommonText:".$intrigueActor->Id."_yes' name='HasCommonText' value='1' ";
                if ($intrigueActor->hasCommonText()) echo " checked='checked' ";
                echo "/>";
                echo "<label for='HasCommonText:".$intrigueActor->Id."_yes'>Ska ha gemensam text</label>";
                echo "<br>";
                echo "<input type='radio' id='HasCommonText".$intrigueActor->Id."_no' name='HasCommonText' value='0' ";
                if (!$intrigueActor->hasCommonText()) echo " checked='checked' ";
                echo "/>";
                echo "<label for='HasCommonText".$intrigueActor->Id."_no'>Ska <b>inte</b> ha gemensam text</label>";
            } else {
                echo "<input type = 'checkbox' id='HasCommonText' name='HasCommonText' value ='$intrigueActor->Id' ";
                if ($intrigueActor->hasCommonText()) echo "checked";
                echo ">";
                echo "<label for='HasCommonText'> Ska ha gemensam text</label>";
            }
            ?>

   				</td>
   			</tr>
			<tr>
				<td><label for="IntrigueText">Intrigtext</label></td>
				<td><textarea id="IntrigueText" name="IntrigueText" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($intrigueActor->IntrigueText); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="OffInfo">Off-info<br>till deltagaren</label></td>
				<td><textarea id="OffInfo" name="OffInfo" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($intrigueActor->OffInfo); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om intrigen<br>(Visas bara för arrangörer)</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($intrigueActor->OrganizerNotes); ?></textarea></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="Spara">
	</form>
	
	</div>
    </body>

</html>