<?php
require 'header.php';
include_once '../includes/error_handling.php';
include "navigation.php";
?>
	<body>
		<div class="content">
			<h1>Omnes Mundi administration</h1>
			<p>			    
				<a href="campaign_admin.php">Kampanjer</a><br><br>
			    <a href="house_admin.php">Hus och lägerplatser</a><br><br>
			    <a href="user_admin.php">Användare / Logins /Admin behörighet</a><br><br>
			    <a href="mail_admin.php">Alla skickade mail</a><br><br>
			    <br><br>
				<a href="dev-tools/doBackup.php">Ta en backup</a>
		    </p>
		    <h2>Basdata</h2>
		    <p>	    			
    		    <a href="selection_data_general_admin.php?type=normalallergytypes">Vanliga allergier</a>	<br><br>
    		    <a href="selection_data_general_admin.php?type=experiences">Erfarenhet som lajvare</a>	<br>
		    </p>
		    <?php  if (Dbh::isLocal()) { ?>
		    <h2>Devtools</h2>
		    <p>	    			
    		    <a href="dev-tools/anonymise.php">Anonymisera databasen</a>	
		    </p>			
			<?php }?>		    
		</div>
	</body>
</html>