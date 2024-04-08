<?php
include_once 'header.php';

global $purpose;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } elseif (isset($_POST['Id'])) {
        $id = $_POST['Id'];
    }
    
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $operation = $_GET['operation'];
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } elseif (isset($_GET['Id'])) {
        $id = $_GET['Id'];
    }
    
}

$multiple=false;

if ($operation == "add_intrigue_vision") {
    $purpose = "Lägg till syner till intrig";
    $url = "logic/view_intrigue_logic.php";
    $multiple=true;
}


if ($multiple) {
    $type = "checkbox";
    $array="[]";
    
} else {
    $type="radio";
    $array="";
}

/*
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}
*/
include 'navigation.php';
?>


    <div class="content">   
        <h1><?php echo $purpose;?></h1>
            <a href="vision_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Skapa ny syn</a>  
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    			<!--  <input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">  -->
     		<?php 
     		if (isset($id)) {
     		    echo "<input type='hidden' id='id' name='id' value='$id'>";
     		    echo "<input type='hidden' id='Id' name='Id' value='$id'>";
     		}
     		
     		$visions = Vision::allBySelectedLARP($current_larp);
     		if (empty($visions)) {
    		    echo "Inga registrerade syner";
    		} else {
    		    ?> 
    		    <table class='data'>
    		    <tr><th></th><th>När</th><th>Syn</th><th>Källa</th><th>Bieffekt</th><th>Vilka<br>som har</th><th>Används<br>i intrig</th></tr>
    		    <?php 
    		    foreach ($visions as $vision)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Vision$vision->Id' name='VisionId$array' value='$vision->Id'>";
    		        
    		        echo "<td>".$vision->getWhenStr() . "</td>\n";
    		        

		            echo "<td>" . nl2br(htmlspecialchars($vision->VisionText)) . "</td>\n";

    		        echo "<td>" . nl2br(htmlspecialchars($vision->Source)) . "</td>\n";
    		        echo "</td>\n";
    		        echo "<td>" . nl2br(htmlspecialchars($vision->SideEffect)) . "</td>\n";
    		        echo "</td>\n";
    		        echo "<td>";
    		        echo "<a href='choose_role.php?operation=add_has_role_admin&id=$vision->Id'><i class='fa-solid fa-plus' title='Lägg till karaktär'></i><i class='fa-solid fa-user' title='Lägg till karaktär'></i></a><br>";
    		        
    		        $has_roles = $vision->getHas();
    		        foreach ($has_roles as $has_role) {
    		            echo "<a href='view_role.php?id=$has_role->Id'>$has_role->Name</a>  ";
    		            echo "<a href='vision_admin.php?operation=delete_has&id=$vision->Id&hasId=$has_role->Id'><i class='fa-solid fa-trash'></i></a><br>";
    		            
    		        }
    		        echo "</td>\n";
    		        echo "<td>";
    		        $intrigues = $vision->getAllIntriguesForVision();
    		        if (!empty($intrigues)) {
    		            foreach ($intrigues as $intrigue) {
    		                echo "<a href='view_intrigue.php?Id=$intrigue->Id'>$intrigue->Number. $intrigue->Name</a><br>";
    		            }
    		        }
    		        echo "</td>";
    		    }
    		    echo "</table>";
    		    echo "<br>";
    		    echo "<input type='submit' value='$purpose'>";
    		}
        
        ?>
        
	</div>
</body>

</html>
