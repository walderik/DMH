<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $larp_house = Larp_House::loadById($_GET['id']);
    }
    elseif (isset($_GET['houseId'])) {
        $larp_house = Larp_House::newWithDefault();
        $larp_house->LARPId = $current_larp->Id;
        $larp_house->HouseId = $_GET['houseId'];
    }
    $house = $larp_house->getHouse();
}



if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation.php';
?>
    

    <div class="content"> 
    <h1>Kommentarer för  <?php echo $house->Name ?></h1>
	<form action="logic/edit_larp_house_save.php" method="post">
		<input type="hidden" id="Id" name="Id" value="<?php echo $larp_house->Id ?>">
		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $larp_house->LARPId ?>">
		<input type="hidden" id="HouseId" name="HouseId" value="<?php echo $larp_house->HouseId ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer ?>">
		<table>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om huset</label><br>(Visas bara för arrangörer)<br>Det som skrivs på första raden kommer visas vid husfördelningen. Håll det gärna kort, 1-2 ord.</td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($larp_house->OrganizerNotes); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="PublicNotes">Kommentarer till boende</label><br>Visas för deltagare som bor i huset, både vid utskick av boendet och inne i Omnes Mundi.</td>
				<td><textarea id="PublicNotes" name="PublicNotes" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($larp_house->PublicNotes); ?></textarea></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="Spara">
	</form>
	
	</div>
    </body>

</html>