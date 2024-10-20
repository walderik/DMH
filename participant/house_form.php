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

    <div class="content"> 
    	<h1>Ändra husbrevet för <?php echo $house->Name ?></h1>
    	<div style="width:60%";>Husbrevet är en text du som husförvaltare skriver till de som skall bo i huset.<br>
    	Det skickas till alla som placeras i huset på något av Berghems lajv.<br>
    	Det bör innehålla information om vad man får och inte får göra i huset och hur huset fungerar på olika sätt.<br>
    	Alla huseförvaltare har rätt att redigera den här texten.</div>
    	<form action="view_house.php?id=<?php echo $house->Id ?>&operation=updateNotesToUser" method="post">
    		<input type="hidden" id="operation" name="operation" value="updateNotesToUser"> 
    		<table>
        		<tr>
            		<td>
            		<table>


            			<?php if ($house->IsHouse()) {?>
            			<tr>
            				<td>
            					<textarea id="NotesToUsers" name="NotesToUsers" rows="17" cols="90" maxlength="600000" required><?php echo htmlspecialchars($house->NotesToUsers); ?></textarea>
            				</td>
            			</tr>
            			
            			<tr>
            				<td></td><td><input id="submit_button" type="submit" value="Ändra"></td>
            			</tr>
            			<tr>
            				<td>&nbsp;</td>
            			</tr>
            			<?php }?>
            		</table>
            		</td>
    			</tr>
			</table>
    	</form>
    	</div>
    </body>

</html>