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
    </div>

</body>
</html>