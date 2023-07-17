<?php
include_once 'header.php';
include_once '../includes/error_handling.php';



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


if (isset($_FILES["upload"])) {
    
    $error = Image::maySave();
    if (!isset($error)) {
        $id = Image::saveImage();
        $bookkeeping->ImageId = $id;
        $bookkeeping->update();
        
        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
        header('Location: economy_view_bookkeeping.php?message=image_uploaded&id='.$BookkeepingId);
        exit;
    }
    else {
        $error_code = $error;
        $error_message = getErrorText($error_code);
        
    }
}




if ($bookkeeping->LarpId != $current_larp->Id) {
    header('Location: index.php'); // hör inte till detta lajv
    exit;
}

if ($bookkeeping->Amount > 0) $type = "inkomst";
else $type = "utgift";


include 'navigation.php';

?>
    


    <div class="content"> 
    <h1>Se <?php echo $type?> <a href="economy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
    
   
		<table>
			<tr>
				<td>Verifikation<br>nummer</td>
				<td><?php  echo $bookkeeping->Number ?></td>

          <?php 
          if ($bookkeeping->hasImage()) {
              $image = Image::loadById($bookkeeping->ImageId);
                echo "<td rowspan='20' valign='top'>";
                echo '<img src="data:image/jpeg;base64,'.base64_encode($image->file_data).'"/>';
                echo "</td>";
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
				<td><?php echo $bookkeeping->Date ?></td>
			</tr>
			<?php 
			if ($type == "utgift" && !$bookkeeping->hasImage()) {
			    echo "<form method='post' enctype='multipart/form-data'>";
			    echo "<input type='hidden' id='id' name='id' value='$bookkeeping->Id'>";
			    echo "<input type='hidden' id='Photographer' name='Photographer' value='Kvitto'>";
			    
			    echo "<tr>";
			    echo "<td><label for='upload'>Ladda upp kvitto</label></td>";
			    echo "<td><input type='file' name='upload' required>";
			    echo "<input type='submit' name='submit' value='Ladda upp kvitto'>";
                echo "</td>";
			    echo "</tr>";
			    echo "</form>";
			}
			
			?>
			
		</table>

	</form>
	</div>
    </body>

</html>