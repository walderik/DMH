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

        <a href="intrigue_creator_page.php">Intrigskapare</a>
        <a href="intrigue_admin.php">Intrigspår</a>
    </div>
    <div class="right">	  
    	<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="subFunction()">&#9776;</a>
    
	</div>
</div>