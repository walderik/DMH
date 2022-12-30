 <?php
 include_once 'header.php';
 ?>
 
     <h1>Logga in</h1>
     <form action="includes/login.inc.php" method="post">
     	<label for="name">Namn/E-post</label><input type="text" name="name"><br>
     	<label for="password">Lösenord</label><input type="password" name="password"><br>
     	
     	
     	<button type="submit" name="submit">Logga in</button>
     </form>
 
 
 

<?php
    include_once 'footer.php'; 
?>