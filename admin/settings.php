<?php

include_once 'header.php';

include 'navigation.php';


?>
    <div class="content">   
        <h1>Inställningar</h1>

        <h2>Lajv</h2>
        <p>
		    <a href="campaign_admin.php">Inställningar för kampanjen</a> <br> 
		    <a href="larp_admin.php">Lajv i kampanjen</a> <br> 
        	<a href="payment_information_admin.php">Avgift för <?php echo $current_larp->Name ?></a>
			<a href="bookkeeping_account_admin.php">Bokföringskonton</a>	<br>

        </p>
        
	    <h2>Basdata för kampanjen</h2>
	    	<p>
				Nedanstående påverkar vilka frågor som ställs i registrerings- och anmälningsformulären.<br>
				Om man inte fyller i några värden så kommer inte frågan att komma upp för deltagarna.<br>
				Tänk på att sätta upp det här innan man öppnar anmälan, annars kommer de som har anmält sig tidigare att sakna svar på de frågorna.<br><br>
			    <a href="selection_data_admin.php?type=larpertypes">Typ av lajvare för karaktärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=intriguetypes">Typ av intriger för karaktärer och grupper</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=housingrequests">Boendeönskemål för deltagare och grupper</a>	<br>		    			
			    <a href="selection_data_admin.php?type=wealth">Rikedom för karaktärer och grupper</a><br>
			    <a href="selection_data_admin.php?type=placeofresidence">Var karaktärer / grupper bor</a><br>
		    			
    		    <a href="selection_data_admin.php?type=typesoffood">Matalternativ för deltagare</a><br>
			    <a href="selection_data_admin.php?type=officialtypes">Typ av funktionärer för deltagare</a>	<br>		    			
		    </p>
        
        </div>
</body>

</html>        