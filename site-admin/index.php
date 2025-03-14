<?php
require 'header.php';
$_SESSION['navigation'] = Navigation::OM_ADMIN;
include "navigation.php";

if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}
?>
	 
	<body>
		<div class="content">
			<h1>Omnes Mundi administration</h1>
			<p>			    
				<a href="campaign_admin.php">Kampanjer</a><br><br>
			    <a href="user_admin.php">Användare / Logins /Admin behörighet</a><br><br>
			    <a href="mail_admin.php">Alla skickade mail</a><br><br>
			    <a href="find_user.php">Hitta användare för person</a><br><br>
			    
			    <h3>Backup</h3>
				<a href="dev-tools/doBackup.php">Ta en backup</a><br>
				<strong>I delar</strong><br>
				<a href="dev-tools/doBackup.php?alt=1&num1=500&num2=900">Image, id <= 500</a><br>
				<a href="dev-tools/doBackup.php?alt=2&num1=500&num2=900">Image, 500 < id <= 900</a><br>
				<a href="dev-tools/doBackup.php?alt=3&num1=500&num2=900">Image, id > 900</a><br>
				<a href="dev-tools/doBackup.php?alt=4">Attachements</a><br>
				<a href="dev-tools/doBackup.php?alt=5">Resten</a>				
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