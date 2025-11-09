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
    <div  style="float: right;">
<a href="magic.php"><i class="fa-solid fa-house"></i> Aktörer</a>
            <a href="../admin/roles.php">Karaktärer</a>
            <a href="../admin/groups.php">Grupper</a>
            <a href="../admin/subdivision_admin.php">Grupperingar</a>
            <a href="../admin/npc.php">NPC'er</a>
            <a href="../admin/search_role_selectiondata.php">Sökning på karaktärer</a>
            <a href="../admin/notcoming_roles.php">Avbokade</a>
            <a href="../admin/not_registered_roles.php">Inte anmälda</a>
            <a href="../admin/approval.php">Godkännande</a>
                        


    	<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="subFunction()">&#9776;</a>
    
	</div>
</div>
