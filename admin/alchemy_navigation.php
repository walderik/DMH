<script>
function subFunction() {
	  var x = document.getElementById("subTopnav");
	  if (x.className === "topnav") {
	    x.className += " responsive";
	  } else {
	    x.className = "topnav";
	  }
	}


</script>

<div class="topnav" id="subTopnav">
    <div class="left">
        <a href="alchemy.php"><i class="fa-solid fa-house"></i> Alkemi</a>
        <a href="alchemy_essence_admin.php">Essenser</a>
        <a href="alchemy_ingredient_admin.php">Ingredienser</a>
        <a href="alchemy_recipe_admin.php">Recept</a>
        <a href="alchemy_supplier_admin.php">Löjverister</a>
        <a href="alchemy_alchemist_admin.php">Alkemister</a>
    </div>
    <div class="right">	  
    	<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="subFunction()">&#9776;</a>
    
	</div>
</div>