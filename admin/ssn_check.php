<?php

include_once 'header.php';

include 'navigation.php';
?>


	<div class="content">
		<h1>Medlemskontroll flera personnummer</h1>
			<form action="ssn_check_result.php" method="post">
				<p>Skriv ett personnumer per rad.</p>
				<textarea id="ssn_list" name="ssn_list" rows="20" cols="20" maxlength="60000" required></textarea><br>
				<input type="submit" value="Kontrollera">
				
				
			</form>
		
		

	</div>


</body>
</html>
				
		
