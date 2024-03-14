<?php
include_once 'header.php';

include 'navigation.php';
include 'alchemy_navigation.php';
?>


    <div class="content">
        <h1>Alkemi</h1>
        <p>
        <?php 
		$approval_count = count (Alchemy_Supplier::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count lövjerister har ingredienslistor som väntar på <a href='alchemy_supplier_admin.php'>godkännande</a>.<br>";

		$approval_count = count (Alchemy_Ingredient::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count alkemiska ingredienser väntar på <a href='alchemy_ingredient_admin.php'>godkännande</a>.<br>";
		
		$approval_count = count (Alchemy_Alchemist::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count alkemister har receptlistor som väntar på <a href='alchemy_alchemist_admin.php'>godkännande</a>.<br>";
		
		$approval_count = count (Alchemy_Recipe::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count alkemiska recept väntar på <a href='alchemy_recipe_admin.php'>godkännande</a>.<br>";
        
		
	
		?>
		<br>
		Totalt kommer 
		<?php echo count(Alchemy_Supplier::allByComingToLarp($current_larp)); ?> 
		lövjerister och 
		<?php echo count(Alchemy_Alchemist::allByComingToLarp($current_larp)); ?> 
		 alkemister på lajvet.
        </p>
    </div>

</body>
</html>