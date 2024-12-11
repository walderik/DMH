<?php
include_once 'header.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'update') {
            $house = House::loadById($_GET['id']);
        }
    }
    
    if (empty($house) || !$house->IsHouse()) {
        header('Location: index.php?error=no_house');
    }
    
    include "navigation.php";
    
    ?>
    
     
<style>

img {
  float: right;
}
</style>

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-house"></i>
		Ändra husbrevet för <?php echo $house->Name ?>
	</div>

    	<form action="view_house.php?id=<?php echo $house->Id ?>&operation=updateNotesToUser" method="post">
    		<input type="hidden" id="operation" name="operation" value="updateNotesToUser"> 

    	<div class='itemcontainer'>
    	Husbrevet är en text du som husförvaltare skriver till de som skall bo i huset.<br>
    	Det skickas till alla som placeras i huset på något av Berghems lajv.<br>
    	Det bör innehålla information om vad man får och inte får göra i huset och hur huset fungerar på olika sätt.<br>
    	Alla husförvaltare har rätt att redigera den här texten.<br>
    	<textarea id="NotesToUsers" name="NotesToUsers" rows="17" cols="90" maxlength="600000" required><?php echo htmlspecialchars($house->NotesToUsers); ?></textarea>
      	</div>
  	
  		<div class='center'><input id="submit_button" class='button-18' type="submit" value="Ändra"></div>

    	</form>
    	</div>
    </body>

</html>