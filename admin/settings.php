<?php

include_once 'header.php';

include 'navigation_subpage.php';


?>
    <div class="content">   
        <h1>Inställningar</h1>

        <h2>Lajv</h2>
        <p>
		    <a href="campaign_admin.php">Inställningar för kampanjen</a> <br> 
		    <a href="larp_admin.php">Lajv i kampanjen</a> <br> 
        	<a href="payment_information_admin.php">Avgift för <?php echo $current_larp->Name ?></a>

        </p>
        
	    <h2>Basdata</h2>
	    	<p>
			    <a href="selection_data_admin.php?type=larpertypes">Typ av lajvare</a>	<br>		    			
			    <a href="selection_data_admin.php?type=intriguetypes">Typ av intriger</a>	<br>		    			 					    <a href="selection_data_admin.php?type=housingrequests">Boendeönskemål</a>	<br>		    			
			    <a href="selection_data_admin.php?type=wealth">Rikedom</a><br>
			    <a href="selection_data_admin.php?type=placeofresidence">Var karaktärer / grupper bor</a><br>
		    			
    		    <a href="selection_data_admin.php?type=typesoffood">Matalternativ</a><br>
			    <a href="selection_data_admin.php?type=officialtypes">Typ av funktionärer</a>	<br>		    			
		    </p>
        
        </div>
</body>

</html>        