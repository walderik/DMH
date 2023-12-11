<?php

include_once 'header.php';

include 'navigation.php';

$param = date_format(new Datetime(),"suv");

?>
    <div class="content">   
        <h1>Status</h1>
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
				<input type=hidden name="send_intrigues" value=<?php echo $param ?>>
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
				<input type=hidden name="send_housing" value=<?php echo $param ?>>
			
        <input type='submit' value='Skicka ut boendet'>
		</form>
		</td></tr>
		</table>


</body>

</html>        