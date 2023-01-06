<?php
require 'header.php';


?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="person_registration.php"><i class="fa-solid fa-user"></i>Registrera person</a></li>
            <li><a href="character_registration.php"><i class="fa-regular fa-user"></i>Registrera karaktär</a></li>
            <li><a href="group_registration.php"><i class="fa-solid fa-user-group"></i>Registrera grupp</a></li>
        	<?php 
        	 if (isset($_SESSION['admin'])) {
        	 ?>
        	     <li><a href="../admin/" style="color: red"><i class="fa-solid fa-lock"></i>Admin</a></li>  
        	 <?php 
        	 }
        	 ?>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>
		<div class="content">
			<h1>Anmälan till <?php echo $current_larp->Name;?></h1>
		</div>
	</body>
</html>