<?php
require 'header.php';

include "navigation.php";

$current_year = date("Y");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $operation = "";
    if (isset($_POST['operation'])) $operation = $_POST['operation'];
    if ($operation == 'set_membership') {
        $person = Person::loadById($_POST['person_id']);
        $person->setManualMembership();
    } elseif ($operation == 'remove_membership') {
        $person = Person::loadById($_POST['person_id']);
        $person->removeManualMembership();
    }
}






if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}


?>
<h1>Medlemsskap</h1>

<h2>Manuellt tilldelade medlemsskap för <?php echo $current_year ?></h2>
<?php 
$persons = Person::allManualMemberships();
if (!empty($persons)) {
    echo "<table class='data'>";
    echo "<tr><th>Kampanj</th><th>Ta bort</th></tr>";
    foreach($persons as $person) {
        echo "<tr>";
        echo "<td>$person->Name</td>";
        echo "<td>";
        echo "<form action='membership.php' method='post' style='display:inline-block'>";
        echo "<input type='hidden' name='person_id' value='$person->Id'>\n";
        echo "<input type='hidden' name='operation' value='remove_membership'>\n";
        echo " <button class='invisible' type ='submit'><i class='fa-solid fa-trash-can' title='Ta bort medlemsskap'></i></button>\n";
        echo "</form>\n";
        echo "</td>";
    }
    echo "</table>";
} else echo "Inga tilldelade";
?>
<br><br>
Sätt medlemsskap för
	<form autocomplete="off" action="membership.php" method="post">
		<input type="hidden" id="operation" name="operation" value="set_membership"> 

		<?php 
		autocomplete_person_id('60%', false);
		?>
		<br>
		<input type='submit' value='Sätt medlemsskap'>
	</form>



<h2>Övrigt</h2>

<a href="../board/ssn_check.php">Medlemskontroll</a> 

