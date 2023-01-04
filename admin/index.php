<?php
require 'header.php';


?>
		<nav class="navtop">
			<div>
				<h1><?php echo $current_larp->Name;?></h1>
    			<a href="../participant/" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Deltagare</a>  
				<a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Anmälan till <?php echo $current_larp->Name;?></h2>
		</div>
	</body>
</html>