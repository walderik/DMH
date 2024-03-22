<?php
include_once 'header.php';


include 'navigation.php';
include 'magic_navigation.php';
?>

    <div class="content">
        <h1>Magi</h1>
        <p>
 		Totalt kommer 
		<?php echo count(Magic_Magician::allByComingToLarp($current_larp)); ?> 
		magiker på lajvet.        </p>
		
		<h3>Utskrifter</h3>
		<p>
				<a href='magic_magician_sheet.php' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för alla magiker'></i>Magikerblad för alla magiker</a>&nbsp;
		
		</p>
    </div>

</body>
</html>