<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['RumourId'])) $rumourIdArr=$_POST['RumourId'];
    
} else {
    header('Location: rumour_admin.php');
    exit;
}
    

include 'navigation.php';

?>


    <div class="content">
        <h1>Slumpmässig fördelning av rykten - sida 2 av 3</h1>
        <p>Den här guiden kommer att hjälpa dig att enkelt sprida rykten till slumpvis valda karaktärer.<br><br>
        <?php 
        if (empty($rumourIdArr)) {           
        ?>
        <strong>OBS!</strong> Du måste välja minst ett rykte. <br>
        <form action="rumour_wizard_pg1.php" method="post" >
        	<input type="submit" value="Tillbaka">
        	</form>
        <?php 
            exit;
        }?>
        
        Klicka i det som fördelningen ska begränsa till. Om du väljer typer måste gruppen/karaktären stämma in på alla, ex rikedom 2 eller 3 <strong>och</strong> intrigtyp handel eller deckargåtor.
        <form action="rumour_wizard_pg3.php" method="post" >
        <?php 
        foreach($rumourIdArr as $rumourId) {
            echo "<input type='hidden' name='RumourId[]' value='$rumourId'>";
        }
        ?>

        
        <h2>Vilka ska ryktet begränsas till?</h2>
			<div class="question">
				Grupper eller karaktärer?</label>&nbsp;<font style="color:red">*</font>
				<br> 
				<input type="radio" id="groups" name="groups_roles" value="groups" required>
				<label for="groups">Grupper</label><br>
				<input type="radio" id="roles" name="groups_roles" value="roles" required>
                <label for="roles">Karaktärer</label><br>
                <input type="radio" id="groups_roles_both" name="groups_roles" value="both" required checked="checked">
                <label for="groups_roles_both">Både grupper och karaktärer</label>
			</div>
			<div class="question">
				Vilken typ av karaktärer?</label>&nbsp;<font style="color:red">*</font>
				<br>Svaret spelar bara något roll om karaktärer ingår i svaret på förra frågan.
				<br> 
				<input type="radio" id="groups" name="main_nonmain" value="main" required checked="checked">
				<label for="groups">Huvudkaraktärer</label><br>
				<input type="radio" id="roles" name="main_nonmain" value="nonmain" required>
                <label for="roles">Sidokaraktärer</label><br>
                <input type="radio" id="all" name="main_nonmain" value="all" required>
                <label for="all">Alla karaktärer</label>
			</div>
        
        
			<?php 
			
			if (LarperType::isInUse($current_larp)){
		    ?>
			<div class="question">
				Typ av lajvare
				<br> 
                <?php LarperType::selectionDropdown($current_larp, true, false); ?>
			</div>
			<?php 
			}
			if (Wealth::isInUse($current_larp)) {
			?>

			<div class="question">
				Rikedom
				<br> 
                <?php Wealth::selectionDropdown($current_larp, true, false); ?>
			</div>
			<?php 
			}
			if (PlaceOfResidence::isInUse($current_larp)) {
			?>

			<div class="question">
				Var karaktärer / grupper bor
				<br> 
                <?php PlaceOfResidence::selectionDropdown($current_larp, true, false); ?>
			</div>
 			<?php 
			}
			if (IntrigueType::isInUse($current_larp)) {
			?>
        
			<div class="question">
				Intrigtyper
				<br> 
                <?php IntrigueType::selectionDropdown($current_larp, true, false); ?>
			</div>
			<?php }?>

        
        	<input type="submit" value="Nästa">
        </form>
            </div>
	
</body>

</html>