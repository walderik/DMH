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
    <div class="right">
<a href="magic.php"><i class="fa-solid fa-house"></i> Magi</a>
        <a href="magic_schools_admin.php">Magiskolor</a>
        <a href="magic_spells_admin.php">Magier</a>
        <a href="magic_magician_admin.php">Magiker</a>
    </div>
    <div class="right">	  
    	<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="subFunction()">&#9776;</a>
    
	</div>
</div>
