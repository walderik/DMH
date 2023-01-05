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
			<h2>Administration av <?php echo $current_larp->Name;?></h2>
			<p>
			    <a href="telegram_admin.php">Telegram</a> <br> 
			</p>
			<h2>Administration för alla lajv</h2>
			<p>			    
			    <a href="larp_admin.php">Lajv</a> <br> 
			    <a href="selection_data_admin.php?type=wealth">Rikedom</a><br>
			    <a href="selection_data_admin.php?type=typesoffood">Matalternativ</a><br>
			    <a href="selection_data_admin.php?type=origins">Ursprung?</a><br>
			    <a href="selection_data_admin.php?type=normalallergytypes">Vanliga allergier</a>	<br>		    			
			    <a href="selection_data_admin.php?type=larpertypes">Typ av lajvare</a>	<br>		    			
			    <a href="selection_data_admin.php?type=officialtypes">Typ av funktionärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=intriguetypes">Typ av intriger</a>	<br>		    			
			    <a href="selection_data_admin.php?type=interests">Intressen</a>			  <br>  			
			    <a href="selection_data_admin.php?type=housingrequests">Boendeönskemål</a>	<br>		    			
			    <a href="selection_data_admin.php?type=experiences">Erfarenhet</a>	<br>		    			
			</p>
		</div>
	</body>
</html>