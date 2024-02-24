<?php
include_once 'header.php';



include 'navigation.php';
?>


	<div class="content">
		<h1>Ladda upp en Swish-rapport för matchning mot obetalade avgifter</h1>

        <form action="economy_payments_results.php" method="post" enctype="multipart/form-data">
        <input type="file" name="csv" value="" />
        <input type="submit" name="submit" value="Ladda upp" /></form>

    </div>
</body>
</html>
  
