<?php

include_once 'header.php';

include 'navigation.php';

$param = date_format(new Datetime(),"suv");

?>
    <div class="content">   
        <h1>Registrerings och anmälningsformulär</h1>
        <h2>Förhandsgranskning</h2>
        <a href="../participant/group_form.php?admin=1" target="_blank">Registrering av grupp</a><br>
        <a href="../participant/group_registration_form.php?admin=1" target="_blank">Anmälan av grupp</a><br>
        <a href="../participant/role_form.php?admin=1" target="_blank">Registrering av karaktär</a><br>
        <a href="../participant/person_registration_form.php?admin=1" target="_blank">Anmälan av deltagare</a><br>
        
	    <h2>Basdata för kampanjen</h2>

				Nedanstående påverkar vilka frågor som ställs i registrerings- och anmälningsformulären.<br>
				Om man inte fyller i några värden så kommer inte frågan att komma upp för deltagarna.<br>
				Tänk på att sätta upp det här innan man öppnar anmälan, annars kommer de som har anmält sig tidigare att sakna svar på de frågorna.<br><br>
			    <a href="selection_data_admin.php?type=larpertypes">Typ av lajvare för karaktärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=religion">Religion för karaktärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=belief">Hur troende en karaktär är</a><br>
			    <a href="selection_data_admin.php?type=race">Ras för karaktärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=abilities">Typ av förmågor för karaktärer</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=council">Byråd för karaktärer</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=guard">Markvakt för karaktärer</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=rolefunction">Karaktärens funktion</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=intriguetypes">Typ av intriger för karaktärer och grupper</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=housingrequests">Boendeönskemål för deltagare och grupper</a>	<br>		    			
			    <a href="selection_data_admin.php?type=wealth">Rikedom för karaktärer och grupper</a><br>
			    <a href="selection_data_admin.php?type=placeofresidence">Var karaktärer / grupper bor</a><br>

			    <a href="selection_data_admin.php?type=grouptype">Typ av grupp</a><br>
			    <a href="selection_data_admin.php?type=shiptype">Typ av skepp för grupper</a><br>
		    			
    		    <a href="selection_data_admin.php?type=typesoffood">Matalternativ för deltagare</a><br>
			    <a href="selection_data_admin.php?type=officialtypes">Typ av funktionärer för deltagare</a>	<br>		    			


        <h2>Inställningar för lajvet, bland annat innehåll</h2>
        <a href ="larp_form.php?operation=update&id=<?php echo $current_larp->Id?>">Inställningar för lajvet</a>
        
        
        </div>
        
</body>

</html>        