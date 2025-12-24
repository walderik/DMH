<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) $larp_house = Larp_House::loadById($_GET['id']);
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
		<input type="hidden" id="Id" name="Id" value="<?php echo $larp_house->IntrigueId ?>">
		<table>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om huset</label><br>(Visas bara för arrangörer)</td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($larp_house->OrganizerNotes); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="PublicComment">Kommentarer till boende</label></td>
				<td><textarea id="PublicComment" name="PublicComment" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($larp_house->PublicComment); ?></textarea></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="Spara">
	</form>
	
	</div>
    </body>

</html>