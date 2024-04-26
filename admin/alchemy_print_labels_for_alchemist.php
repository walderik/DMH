<?php

include_once 'header.php';
include_once '../includes/selection_data_control.php';

include 'navigation.php';
?>

<style>
input[type="number"] {
  width: 75px;
  text-align: right;
}
</style>

	<div class="content">
		<h1>Skapa pdf med etiketter för alkemist</h1>
		<form action="reports/recipe_labels_pdf.php" method="POST" target="_blank">
		<table>
		<tr>
		<td>Alchemist</td>
		<td>
		<?php 
		$alchemists = Alchemy_Alchemist::allByComingToLarp($current_larp);

		echo "<select name='alchemistId' id='alchemistId' onchange='showRecipes()'>";
		echo "<option value=''>Etiketter utan alkemistnamn</option>";
		foreach ($alchemists as $alchemist) {
		    $name = $alchemist->getRole()->Name;
		    echo "<option value='$alchemist->Id'>$name</option>";
		}
		echo "</select>";
		
		?>
		
		
		
		</td>
		</tr>
		<tr>
		<td>Recept</td>
		<td>
			<span id='result'>
				<?php 
				
				
				$recipes = Alchemy_Recipe::allByCampaign($current_larp);
				
				foreach($recipes as $recipe) {
				    echo "<input type='hidden' name='recipeId[]' value='$recipe->Id'>";
				    echo "<input type='number' id='Recipe_$recipe->Id' name='Recipe_$recipe->Id' value = '0' min='0' step='1'> ";
				    echo "<label for='Recipe_$recipe->Id'>$recipe->Name, nivå $recipe->Level</label>";
				    echo "<br><br>";
				}
				
				
				?>
	
	
			</span>
		</td>
		<td></td><td><button type="submit">Skapa pdf</button></td></tr>
		</table>
		</form>
	
	


	</div>
<script>
function showRecipes() {

    var alchemistId_select = document.getElementById('alchemistId');

    alchemistId_select.disabled  =  true ;
    var alchemistId = alchemistId_select.value;
	

	var callString = "../ajax/alchemy_recipes_for_alchemist.php?alchemistId="+alchemistId;

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			//var result = this.responseText;
			var res_div = document.getElementById('result');
			res_div.innerHTML = "";

			if (this.responseText) {
				var recipe_html = "";
    			var resultArr = this.responseText.split(";");
    	        for (i=0; i < resultArr.length; i++) {
    		        //alert(i + " " + resultArr[i]);
    		        var options = resultArr[i].split(":");
    		        

					
    		        recipe_html += "<input type='hidden' name='recipeId[]' value='"+options[0]+"'>";
    		        recipe_html += "<input type='number' id='Recipe_"+options[0]+"' name='Recipe_"+options[0]+"' value = '0' min='0' step='1'> ";
    		        recipe_html += "<label for='Recipe_"+options[0]+"'>"+options[1]+"</label>";
    		        recipe_html += "<br><br>";
    	        }
    	        res_div.innerHTML = recipe_html;
			} else {
				res_div.innerHTML = "Alkemisten saknar recept";
			}
			
			alchemistId_select.disabled = false;
		}
	};
	xmlhttp.open("GET", callString, true);
	xmlhttp.send();

}


	

</script>

</body>
</html>
