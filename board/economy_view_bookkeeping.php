<?php
include_once 'header.php';
// include_once '../includes/error_handling.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $BookkeepingId = $_GET['id'];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id'])) {
        $BookkeepingId = $_POST['id'];
        
    }
}


if (!isset($BookkeepingId)) {
    header('Location: index.php');
    exit;
}

$bookkeeping = Bookkeeping::loadById($BookkeepingId);



if ($bookkeeping->Amount > 0) $type = "inkomst";
else $type = "utgift";


include 'navigation.php';

?>
    


    <div class="content"> 
    <h1>Se <?php echo $type?></h1>
    
   
		<table>
			<tr>
				<td>Verifikation<br>nummer</td>
				<td><?php  echo $bookkeeping->Number ?></td>

          <?php 
          if ($bookkeeping->hasImage()) {
                $image = Image::loadById($bookkeeping->ImageId);
                if ($image->file_mime == "application/pdf") {
                    echo "</tr><tr><td>Kvitto</td><td><a href='view_pdf_receipt.php?id=$image->Id' target='_blank'>$image->file_name</a></td>";
                    
                }  else {
                    echo "<td rowspan='20' valign='top'>";
                    echo "<img width='400px' src='../includes/display_image.php?id=$bookkeeping->ImageId'/>\n";
                    echo "</td>";
                }
          }
            
            ?>
			</tr>
			<tr>
				<td>Rubrik</td>
				<td><?php  echo $bookkeeping->Headline ?></td>

			</tr>
			<tr>

				<td>Beskrivning</td>
				<td><?php echo nl2br(htmlspecialchars($bookkeeping->Text))?></td>
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
				<td><?php echo $bookkeeping->AccountingDate ?></td>
			</tr>
			<tr>
				<td>Ansvarig</td>
				<td><?php echo $bookkeeping->getPerson()->Name ?></td>
			</tr>
			
		</table>

	</form>
	</div>
    </body>

</html>