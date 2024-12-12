    	<div class="dropdown">
    		<button class="dropbtn"><i class="fa-solid fa-user" title="<?php echo $current_user->Name?>"></i> <?php echo $current_user->Name?>
    		<i class="fa fa-caret-down"></i>
    		</button>
		    <div class="dropdown-content">
		    	<a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut&nbsp;&nbsp;&nbsp;&nbsp;</a>
    		</div>
    	
    	</div>
	  
	  
	  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
	  </div>
    
    </div>
    
<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}


</script>

<?php include_once '../includes/error_handling.php'; ?>