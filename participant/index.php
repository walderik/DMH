<?php
require 'header.php';


?>
		<nav class="navtop">
			<div>
				<h1><?php echo $current_larp->Name;?></h1>
				<a href="person_registration.php"><i class="fa-solid fa-user"></i>Registrera person</a>
				<a href="character_registration.php"><i class="fa-regular fa-user"></i>Registrera karaktär</a>
				<a href="group_registration.php"><i class="fa-solid fa-user-group"></i>Registrera grupp</a>
				<!--  <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a> -->
    			<?php 
    			 if (isset($_SESSION['admin'])) {
    			 ?>
    			     <a href="../admin/" style="color: red"><i class="fa-solid fa-lock"></i>Admin</a>  
    			 <?php 
    			 }
    			 ?>
				<a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Anmälan till <?php echo $current_larp->Name;?></h2>
		</div>
	</body>
</html>