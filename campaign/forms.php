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
				
				<?php 
				$in_use = array();
				$not_in_use = array();
				
				$link = "<a href='selection_data_admin.php?type=larpertypes'>Typ av lajvare för karaktärer</a>";
				if (LarperType::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;

				$link = "<a href='selection_data_admin.php?type=religion'>Religion för karaktärer</a>";
				if (Religion::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;

				$link = "<a href='selection_data_admin.php?type=belief'>Hur troende en karaktär är</a>";
				if (Belief::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=race'>Ras för karaktärer</a>";
				if (Race::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=abilities'>Typ av förmågor för karaktärer</a>";
				if (Ability::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=rolefunction'>Karaktärens funktion</a>";
				if (RoleFunction::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=intriguetypes'>Typ av intriger för karaktärer och grupper</a>";
				if (IntrigueType::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=housingrequests'>Boendeönskemål för deltagare och grupper</a>";
				if (HousingRequest::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=wealth'>Rikedom för karaktärer och grupper</a>";
				if (Wealth::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=placeofresidence'>Var karaktärer / grupper bor</a>";
				if (PlaceOfResidence::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=grouptype'>Typ av grupp</a>";
				if (GroupType::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = " <a href='selection_data_admin.php?type=shiptype'>Typ av skepp för grupper</a>";
				if (ShipType::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = "<a href='selection_data_admin.php?type=typesoffood'>Matalternativ för deltagare</a>";
				if (TypeOfFood::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = " <a href='selection_data_admin.php?type=officialtypes'>Typ av funktionärer för deltagare</a>";
				if (OfficialType::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = " <a href='selection_data_admin.php?type=superpoweractive'>Superkrafter, aktiva, för karaktärer</a>";
				if (SuperPowerActive::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				$link = " <a href='selection_data_admin.php?type=superpowerpassive'>Superkrafter, passiva, för karaktärer</a>";
				if (SuperPowerPassive::isInUse($current_larp)) $in_use[] = $link;
				else $not_in_use[] = $link;
				
				if (!empty($in_use)) {
				    echo "<h3>Använda av kampanjen</h3>";
				    foreach ($in_use as $link) echo $link."<br>";
				}

				
				if (!empty($not_in_use)) {
				    echo "<h3>Inte använda</h3>";
				    foreach ($not_in_use as $link) echo $link."<br>";
				}
				
				
				?>
			   		    			
        <h2>Inställningar för lajvet, bland annat innehåll</h2>
        <a href ="larp_form.php?operation=update&id=<?php echo $current_larp->Id?>">Inställningar för lajvet</a>
        
        
        </div>
        
</body>

</html>        