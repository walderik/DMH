<?php
include_once 'header.php';



include 'navigation.php';
?>


	<div class="content">
		<h1>Ladda upp en Swish-rapport för matchning mot obetalade avgifter</h1>
        <form action="economy_payments_results.php" method="post" enctype="multipart/form-data">
		<table>
        <tr><td>Filformat</td>
        <td>
        <select id="file_format" name="file_format">
        <option value='swish'>Swishrapport</option>
        <option value='transaction'>Transaktionsfil</option>
        </select>
        </td></tr>
        <tr><td>Fil</td>
        <td>
        <input type="file" name="csv" value="" />
        <input type="submit" name="submit" value="Ladda upp" />
        </td>
        </tr>
        </table>
		</form>
    </div>
</body>
</html>
  
