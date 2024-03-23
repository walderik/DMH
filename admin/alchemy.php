<?php
include_once 'header.php';

include 'navigation.php';
include 'alchemy_navigation.php';
require_once $root . '/pdf/alchemy_ingredient_pdf.php';

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
		<h3>Utskrifter</h3>
		<p>
            <a href="logic/all_alchemy_ingredients_pdf.php?type=<?php echo ALCHEMY_INGREDIENT_PDF::Handwriting?>" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera ingredienskort till lövjeristerna (Handskrift)</a><br> 
            <a href="logic/all_alchemy_ingredients_pdf.php?type=<?php echo ALCHEMY_INGREDIENT_PDF::Calligraphy?>" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera ingredienskort till lövjeristerna (Kalligrafi)</a> <br>
			<a href='alchemy_alchemist_sheet.php' target='_blank'><i class='fa-solid fa-file-pdf' title='Alkemistblad för alla alkemister'></i>Alkemistblad för alla alkemister</a>&nbsp;
        
        </p>
    </div>

</body>
</html>