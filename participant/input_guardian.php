<?php

require 'header.php';

if (!$current_larp->mayRegister()) {
    header('Location: index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['PersonId'])) {
        $PersonId = $_POST['PersonId'];
    }
    else {
        
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['PersonId'])) {
        
        $PersonId = $_GET['PersonId'];
        
    }
    else {
        
        header('Location: index.php');
        exit;
    }
}

if (isset($PersonId)) {
    $current_person = Person::loadById($PersonId);
}
else {
    
    header('Location: index.php?error=no_person');
    exit;
}

if ($current_person->UserId != $current_user->Id) {
    header('Location: index.php');
    exit;
}



if ($current_person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAge) {
    header('Location: index.php?error=too_young_for_larp');
    exit;
}

include 'navigation.php';
?>

	<div class="content">
		<h1>Ange ansvarig vuxen för <?php echo $current_person->Name;?> till <?php echo $current_larp->Name;?></h1>
		<form action="logic/input_guardian_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="insert"> 
    		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $current_larp->Id ?>">
    		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $current_person->Id ?>">


			<div class="question">
    			<label for="GuardianInfo">Ansvarig vuxen</label>&nbsp;<font style="color:red">*</font>
    			<div class="explanation">Eftersom <?php echo $current_person->Name; ?> bara är <?php  echo $current_person->getAgeAtLarp($current_larp); ?> år behövs en ansvarig vuxen. 
    			Den ansvarige måste vara tillfrågad och accepera ansvaret.<br>
    			Skriv in namn eller personnummer på den ansvarige. Personnummer anges på formen ÅÅÅÅMMDD-NNNN.
    			Om den ansvarige inte går att hitta kommer inte din anmälan att kunna godkännas förrän det är löst.
				</div>
				<input class="GuardianInfo" type="text" id="GuardianInfo" name="GuardianInfo" size="100" maxlength="25" >
            </div>
		    
			  <input type="submit" value="Spara">

		</form>
	</div>

</body>
</html>