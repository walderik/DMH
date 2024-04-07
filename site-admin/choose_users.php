<?php
 include_once 'header.php';
 
 if ($_SERVER["REQUEST_METHOD"] == "GET") {
     
     $operation = $_GET['operation'];
     
     if ($operation == "organizer") {
      $purpose = "arrangör"; 
      $url = "logic/organizer_save.php";
     }     
 }

 if (isset($_SERVER['HTTP_REFERER'])) {
     $referer = $_SERVER['HTTP_REFERER'];
 }
 else {
     $referer = "";
 }
 
 include "navigation.php";
?>


    <div class="content">   
        <h1>Lägg till <?php echo $purpose;?></h1>

    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
    		<input type ="hidden" id=CampaignId" name="CampaignId" value ="<?php echo $_GET['campaignId'] ?>">
    		    <table class='data'>
    		    <tr><th></th><th>Namn</th><th>Epost</th></tr>
    		    <?php 
    		    $users = User::all();
    		    foreach ($users as $user)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='UserId$user->Id' name='UserId[]' value='$user->Id'></td>";
    		        echo "<td>" . $user->Name . "</td>\n";
    		        echo "<td>" . $user->Email . "</td>\n";
    		        
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		
    		?>
    		<br>
			<input type="submit" value="Lägg till som <?php echo $purpose;?>"></form>
        
        
        
	</div>
</body>

</html>
