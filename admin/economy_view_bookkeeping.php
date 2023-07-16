<?php
include_once 'header.php';


include 'navigation.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $BookkeepingId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$bookkeeping = Bookkeeping::loadById($BookkeepingId);


if ($bookkeeping->LarpId != $current_larp->Id) {
    header('Location: index.php'); // hör inte till detta lajv
    exit;
}

if ($bookkeeping->Amount > 0) $type = "inkomst";
else $type = "utgift";

?>
    


    <div class="content"> 
    <h1>Se <?php echo $type?> <a href="economy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
		<table>
			<tr>
				<td>Verifikation<br>nummer</td>
				<td><?php  echo $bookkeeping->Number ?></td>

			</tr>
			<tr>
				<td>Rubrik</td>
				<td><?php  echo $bookkeeping->Headline ?></td>

			</tr>
			<tr>

				<td>Beskrivning</td>
				<td><?php echo htmlspecialchars(nl2br($bookkeeping->Text))?></td>
			</tr>
			<tr>

				<td>Konto</td>
				
				<td><?php echo $bookkeeping->getBookkeepingAccount()->Name;?></td>
			</tr>
			<tr>
				<td>
				<?php 
				    if ($bookkeeping->Amount > 0) echo "Från vem?";
				    else echo "Till vem?";
				    ?>
				</td>
				<td><?php echo $bookkeeping->Who ?></td>

			</tr>
			<tr>
				<td>Summa</td>
				<td><?php echo abs($bookkeeping->Amount)?></td>

			</tr>
			<tr>
				<td>Datum</td>
				<td><?php echo $bookkeeping->Date ?></td>
			</tr>
		</table>

	</form>
	</div>
    </body>

</html>