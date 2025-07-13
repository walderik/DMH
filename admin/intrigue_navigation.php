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

        <a href="magic_schools_admin.php">Intrigskapare</a>
        <a href="magic_spells_admin.php">Intrigspår</a>
    </div>
    <div class="right">	  
    	<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="subFunction()">&#9776;</a>
    
	</div>
</div>