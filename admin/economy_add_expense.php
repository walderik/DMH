<?php
include_once 'header.php';


include 'navigation.php';

$booking_accounts = Bookkeeping_Account::allActive($current_larp);
?>
    


    <div class="content"> 
    <h1>Lägg till utgift <a href="economy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
	<form action="economy.php" method="post" enctype="multipart/form-data">
		<input type="hidden" id="operation" name="operation" value="add_expense"> 
		<input type="hidden" id="Photographer" name="Photographer" value="Kvitto"> 
		
 		
		<table>
			<tr>
				<td><label for="Headline">Rubrik</label></td>
				<td><input type="text" id="Headline" name="Headline" value="" size="100" maxlength="250" required></td>

			</tr>
			<tr>

				<td><label for="Text">Beskrivning</label></td>
				<td><textarea id="Text" name="Text" rows="4" cols="100"></textarea></td>
			</tr>
			<tr>

				<td><label for="BookkeepingAccountId">Konto</label></td>
				
				<td><?php  selectionDropDownByArray("BookkeepingAccountId", $booking_accounts);?></td>
			</tr>
			<tr>
				<td><label for="Who">Till vem?</label></td>
				<td><input type="text" id="Who" name="Who" value="" size="100" maxlength="250" ></td>

			</tr>
			<tr>
				<td><label for="Amount">Summa</label></td>
				<td><input type="number" id="Amount" name="Amount" min="0" size="100" maxlength="250" ></td>

			</tr>
			<tr>
				<td><label for="Date">Datum</label></td>
				<td><input type="date" id="Date"
    					name="Date" value="<?php echo date("Y-m-d") ?>" size="50" required></td>
			</tr>
			<tr>
				<td><label for="upload">Kvitto</label></td>
				<td><input type="file" name="upload" required></td>
			</tr>
		</table>
          	<br><br>

		<input id="submit_button" type="submit" value="Spara">
	</form>
	</div>
    </body>

</html>