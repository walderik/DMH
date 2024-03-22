<?php
include_once 'header.php';

global $purpose;



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $intrigueActor = IntrigueActor::loadById($_GET['IntrigueActorId']);
    $intrigue=$intrigueActor->getIntrigue();
}

if (isset($_GET['section'])) $section = $_GET['section'];
else $section = "";

include 'navigation.php';
?>


    <div class="content">   
        <h1>Välj föremål som aktören känner till</h1>
	    <form action="logic/view_intrigue_logic.php" method="post">
	    <input type="hidden" id="operation" name="operation" value="choose_intrigue_knownprops">
	    <input type='hidden' id='IntrigueActorId' name='IntrigueActorId' value='<?php echo $intrigueActor->Id?>'>
		<input type="hidden" id="Section" name="Section" value="<?php echo $section;?>">
        <h2>Rekvisita</h2>
     		<?php 
     		$intrigue_props = $intrigue->getAllProps();
     		if (empty($intrigue_props)) {
     		    echo "Ingen registrerad rekvisita";
     		} else {
     		    ?>
    		    <table class='data'>
    		    <tr><th>Namn</th></tr>
    		    <?php 
    		    foreach ($intrigue_props as $intrigue_prop)  {
    		        $prop=$intrigue_prop->getProp();
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='Intrigue_Prop$prop->Id' name='Intrigue_PropId[]' value='$intrigue_prop->Id'>";

    		        echo "<label for='Prop$intrigue_prop->Id'>$prop->Name</label></td>\n";

    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="Lägg till">
			
        <h2>PDF</h2>
     		<?php 
     		$intrigue_pdfs = $intrigue->getAllPdf();
     		if (empty($intrigue_pdfs)) {
     		    echo "Inga uppladdade PDF'er";
     		} else {
     		    ?>
    		    <table class='data'>
    		    <tr><th>Namn</th></tr>
    		    <?php 
    		    foreach ($intrigue_pdfs as $intrigue_pdf)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='Intrigue_Pdf$intrigue_pdf->Id' name='Intrigue_PdfId[]' value='$intrigue_pdf->Id'>";

    		        echo "<label for='Pdf$intrigue_pdf->Id'>$intrigue_pdf->Filename</label></td>\n";

    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
			
    		<br>
			<input type="submit" value="Lägg till">
			
			
			
			</form>
        
     		
	</div>
</body>

</html>
