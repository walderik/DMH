<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rumourIdArr=$_POST['RumourId'];
    
    $groups_roles=$_POST['groups_roles'];
    $main_nonmain=$_POST['main_nonmain'];
    
    if (isset($_POST['LarperTypeId'])) $larpertypeArr = $_POST['LarperTypeId'];
    if (isset($_POST['WealthId'])) $wealthArr = $_POST['WealthId'];
    if (isset($_POST['PlaceOfResidenceId'])) $placeofresidenceArr = $_POST['PlaceOfResidenceId'];
    if (isset($_POST['IntrigueTypeId'])) $intriguetypeArr = $_POST['IntrigueTypeId'];
    
} else {
    header('Location: rumour_admin.php');
    exit;
}
    

include 'navigation.php';

?>




    <div class="content">
        <h1>Slumpmässig fördelning av rykten - sida 2 av 3</h1>
        <p>Den här guiden kommer att hjälpa dig att enkelt sprida rykten till slumpvis valda karaktärer.
         
        <form action="logic/rumour_wizard_save.php" method="post" >
            <?php 
            foreach($rumourIdArr as $rumourId) {
                echo "<input type='hidden' name='RumourId[]' value='$rumourId'>";
            }
            ?>

			<input type="hidden" id="groups_roles" name="groups_roles" value="<?php echo $groups_roles; ?>">
			<input type="hidden" id="main_nonmain" name="main_nonmain" value="<?php echo $main_nonmain; ?>">
			
			<?php 
			if (isset($larpertypeArr)) { 
			    foreach($larpertypeArr as $larpertype) {
			        echo "<input type='hidden' name='LarperTypeId[]' value='$larpertype'>";
			    }
			} 
			if (isset($wealthArr)) {
			    foreach($wealthArr as $wealth) {
			        echo "<input type='hidden' name='WealthId[]' value='$wealth'>";
			    }
			}
			if (isset($placeofresidenceArr)) {
			    foreach($placeofresidenceArr as $placeofresidence) {
			        echo "<input type='hidden' name='PlaceOfResidenceId[]' value='$placeofresidence'>";
			    }
			}
			if (isset($intriguetypeArr)) {
			    foreach($intriguetypeArr as $intriguetype) {
			        echo "<input type='hidden' name='IntrigueTypeId[]' value='$intriguetype'>";
			    }
			}
            ?>

           <h2>Hur stor andel ska få ryktet?</h2>
			<div class="question">
				<input type="number" id="percent" name="percent" value="5" style='text-align:right' required> %
			</div>
        
           <h2>Vilken ålder ska spelaren ha som får ryktet?</h2>
			<div class="question">
                <input type="checkbox" id="age0-6" name="age0-6" value="age0-6">
                <label for="age0-6"> 0 - 6 år</label><br>
                <input type="checkbox" id="age7-15" name="age7-15" value="age7-15" checked>
                <label for="age7-15"> 7 - 15 år</label><br>
                <input type="checkbox" id="age16-" name="age16-" value="age16-" checked>
                <label for="age16-"> 16 år och uppåt</label><br>
			</div>
        
        
        	<input type="submit" value="Slumpa ut">
        </form>
            </div>
	
</body>

</html>