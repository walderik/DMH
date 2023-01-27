<?php
require 'header.php';
include_once '../includes/error_handling.php';

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Admin</a></li>
            <li><a href="../participant/" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Deltagare</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


		<div class="content">
			<h1>Administration för alla lajv</h1>
			<p>			    
			    <a href="larp_admin.php">Lajv</a> <br> 
			    <a href="house_admin.php">Hus i byn</</a><br>
			    </p>
			    <h2>Basdata</h2>
			    <p>
			    <a href="selection_data_admin.php?type=wealth">Rikedom</a><br>
			    <a href="selection_data_admin.php?type=typesoffood">Matalternativ</a><br>
			    <a href="selection_data_admin.php?type=placeofresidence">Var karaktärer/grupper bor</a><br>
			    <a href="selection_data_admin.php?type=normalallergytypes">Vanliga allergier</a>	<br>		    			
			    <a href="selection_data_admin.php?type=larpertypes">Typ av lajvare</a>	<br>		    			
			    <a href="selection_data_admin.php?type=officialtypes">Typ av funktionärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=intriguetypes">Typ av intriger</a>	<br>		    			 			
			    <a href="selection_data_admin.php?type=roletypes">Typ av karaktärer</a>	<br>		    			 			
			    <a href="selection_data_admin.php?type=housingrequests">Boendeönskemål</a>	<br>		    			
			    <a href="selection_data_admin.php?type=experiences">Erfarenhet som lajvare</a>	<br>		    			
			</p>
		</div>
	</body>
</html>