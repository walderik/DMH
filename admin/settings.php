<?php

include_once 'header.php';

include 'navigation.php';

$param = date_format(new Datetime(),"suv");

?>
    <div class="content">   
        <h1>Inställningar</h1>
        <div>
        <table>
	    <tr><td style="font-weight: normal;">
            Anmälan är 
            <?php if ($current_larp->RegistrationOpen == 1) {
                echo "öppen";
                echo "<br>Efter sista anmälningsdatum går alla anmälningar in på reservlistan så länge anmälan är öppen.";
                $openButton = "Stäng";
            }
            else {
                echo "stängd";
                $openButton = "Öppna";
            }
                  
                ?>
                </td><td>
			<form action="logic/toggle_larp_registration_open.php">
            <input type="submit" value="<?php echo $openButton;?>"></form>
            </td></tr>
            <tr><td style="font-weight: normal;">
	
		Intrigerna är 
            <?php if ($current_larp->isIntriguesReleased()) {
                echo "släppta";
                echo "<br>Ett mail med alla intriger har skickats ut och deltagarna kan se intrigerna när de loggar in.";
            }
            else {
                echo "inte släppta.<br>När man skickar ut intrigerna första gången släpps de även inne i systemet så att användare som loggar in kan läsa sina intriger.";
            }
                  
                ?>
                </td><td>
				<form action='contact_email.php'  method="post"  
			    onsubmit="return confirm('Är du säker?\nEpost kommer att skickas till alla deltagarna med alla intriger som de ser ut just nu.')">
				<input type=hidden name="send_intrigues" valie=<?php echo $param ?>>
                <input type='submit' value='Skicka ut intrigerna'></form>
                <br>
                <?php if (!$current_larp->isIntriguesReleased()) { ?>
				<form action='logic/release_intrigues.php'  method="post">
                <input type='submit' value='Släpp intrigerna (utan att skicka ut dem)'></form>
				<?php }?>
        </td></tr>
        <tr><td style="font-weight: normal;">
		Boendet är 
            <?php if ($current_larp->isHousingReleased()) {
                echo "släppt";
                echo "<br>Ett mail med hur alla bor har skickats ut och deltagarna kan se var de ska bo när de loggar in.";
            }
            else {
                echo "inte släppt.";
                echo "<br>När man skickar ut boendet första gången släpps de även inne i systemet så att användare som loggar in kan läsa hur de ska bo.";
            }
                  
                ?>
        </td>
        <td>
		<form action='contact_email.php'  method="post" 
	    onsubmit="return confirm('Är du säker?\nEpost kommer att skickas till alla deltagarna med boendet så som det är fördelat just nu.')">
				<input type=hidden name="send_housing" valie=<?php echo $param ?>>
			
        <input type='submit' value='Skicka ut boendet'>
		</form>
		</td></tr>
		</table>



        <h2>Lajv</h2>
        <p>
		    <a href="campaign_admin.php">Inställningar för kampanjen</a> <br> 
		    <a href="larp_admin.php">Lajv i kampanjen</a> <br> 
        	<a href="payment_information_admin.php">Avgift för <?php echo $current_larp->Name ?> inklusive matavgifter</a><br>
			<a href="bookkeeping_account_admin.php">Bokföringskonton</a>	<br>

        </p>
        
	    <h2>Basdata för kampanjen</h2>
	    	<p>
				Nedanstående påverkar vilka frågor som ställs i registrerings- och anmälningsformulären.<br>
				Om man inte fyller i några värden så kommer inte frågan att komma upp för deltagarna.<br>
				Tänk på att sätta upp det här innan man öppnar anmälan, annars kommer de som har anmält sig tidigare att sakna svar på de frågorna.<br><br>
			    <a href="selection_data_admin.php?type=larpertypes">Typ av lajvare för karaktärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=religion">Religion för karaktärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=race">Ras för karaktärer</a>	<br>		    			
			    <a href="selection_data_admin.php?type=abilities">Typ av förmågor för karaktärer</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=council">Byråd för karaktärer</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=guard">Markvakt för karaktärer</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=intriguetypes">Typ av intriger för karaktärer och grupper</a>	<br>		    			 					    
			    <a href="selection_data_admin.php?type=housingrequests">Boendeönskemål för deltagare och grupper</a>	<br>		    			
			    <a href="selection_data_admin.php?type=wealth">Rikedom för karaktärer och grupper</a><br>
			    <a href="selection_data_admin.php?type=placeofresidence">Var karaktärer / grupper bor</a><br>
		    			
    		    <a href="selection_data_admin.php?type=typesoffood">Matalternativ för deltagare</a><br>
			    <a href="selection_data_admin.php?type=officialtypes">Typ av funktionärer för deltagare</a>	<br>		    			
		    </p>
        <p>
        Se ut de olika formulären blir.<br>
        <a href="../participant/group_form.php?admin=1" target="_blank">Registrering av grupp</a><br>
        <a href="../participant/group_registration_form.php?admin=1" target="_blank">Anmälan av grupp</a><br>
        <a href="../participant/role_form.php?admin=1" target="_blank">Registrering av karaktär</a><br>
        <a href="../participant/person_registration_form.php?admin=1" target="_blank">Anmälan av deltagare</a><br>
        </div>
</body>

</html>        