<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RegistrationId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}


$registration = Registration::loadById($RegistrationId);
$person = Person::loadById($registration->PersonId);

include 'navigation_subpage.php';

?>


	<div class="content">
		<h1><?php echo $person->Name;?> <a href="officials.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
		<form action="logic/edit_official_save.php" method="post">
    		<input type="hidden" id="RegistrationId" name="RegistrationId" value="<?php echo $registration->Id; ?>">
		<table>
    		
 			<tr><td valign="top" class="header">Typ av funktionär</td><td><?php OfficialType::selectionDropdown($current_larp, true,false,$registration->getSelectedOfficialTypeIds());?></td></tr>

		</table>		
			<input type="submit" value="Spara">

			</form>


	</div>


</body>
</html>
