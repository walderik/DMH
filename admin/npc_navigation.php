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
        <a href="alchemy.php"><i class="fa-solid fa-house"></i> NPC</a>
        <a href="npc_overview.php">Alla NPC</a>
        <a href="npc_participants.php">Deltagare som vill spela NPC</a>
        <a href="npc_played.php">NPC som spelas under lajvet</a>
        <a href="npc_hidden_groups.php">Gömda grupper</a>
    </div>
    <div class="right">	  
    	<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="subFunction()">&#9776;</a>
    
	</div>
</div>