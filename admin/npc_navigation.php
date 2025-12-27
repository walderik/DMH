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
    <div style="float: right;">	  
        <a href="npc.php"><i class="fa-solid fa-house"></i> NPC</a>
        <a href="npc_overview.php">Alla</a>
        <a href="npc_participants.php">Deltagare som vill spela NPC</a>
        <a href="npc_assignments.php">Uppdrag</a>
        <a href="npc_hidden_groups.php">Gömda grupper</a>
        <a href="edit_role.php?action=insert&type=npc"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC</a>&nbsp;  
        <a href="edit_group.php?operation=insert&hidden=1"><i class="fa-solid fa-file-circle-plus"></i>Skapa grupp</a>  
    	<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="subFunction()">&#9776;</a>
    
	</div>
</div>