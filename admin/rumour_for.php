<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['RoleId'])) {
        $role = Role::loadById($_POST['RoleId']);
        if (isset($_POST['RumourId'])) {
            $rumourIds = $_POST['RumourId'];
            foreach ($rumourIds as $rumourId) {
                if (Rumour_knows::roleKnows($rumourId, $role->Id)) continue;
                $rumour_knows = Rumour_knows::newWithDefault();
                $rumour_knows->RoleId = $role->Id;
                $rumour_knows->RumourId = $rumourId;
                $rumour_knows->create();
            }
        }
        header("Location: " . $role->getLink());
        exit;
        
    }
    if (isset($_POST['GroupId'])) {
        $group = Group::loadById($_POST['GroupId']);
        if (isset($_POST['RumourId'])) {
            $rumourIds = $_POST['RumourId'];
            foreach ($rumourIds as $rumourId) {
                if (Rumour_knows::groupKnows($rumourId, $group->Id)) continue;
                $rumour_knows = Rumour_knows::newWithDefault();
                $rumour_knows->GroupId = $group->Id;
                $rumour_knows->RumourId = $rumourId;
                $rumour_knows->create();
            }
        }
        header("Location: " . $group->getLink());
        exit;
        
    }
    
}
    

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['RoleId'])) {
        $role = Role::loadById($_GET['RoleId']);
        $name = $role->Name;
    } elseif (isset($_GET['GroupId'])) {
        $group = Group::loadById($_GET['GroupId']);
        $name = $group->Name;
    }
}

if (empty($role) && empty($group)) {
    header('Location: index.php');
    exit;
}

include 'navigation.php';
?>


    <div class="content">
        <h1>Tilldela rykten till <?php echo $name ?></h1>
		<form action="rumour_for.php" method="post">
		<?php 
		if (!empty($role)) echo "<input type='hidden' id='RoleId' name='RoleId' value='$role->Id'>";
		elseif (!empty($group)) echo "<input type='hidden' id='GroupId' name='GroupId' value='$group->Id'>";
		
		?>
		    
		<br>	

        <?php
    
        $rumour_array = Rumour::allBySelectedLARP($current_larp);
        if (empty($rumour_array)) {
            echo "Det finns inga rykten att lägga till.";
        } else {
        echo "<table id='rumours' class='data'>";
        echo "<tr><th>Text</th><th>Gäller</th><th>Antal som<br>känner till</th><th>Används<br>i intrig</th><th>Ok</th></tr>\n";
        foreach ($rumour_array as $rumour) {
            echo "<tr>\n";
            echo "<td>";
            echo "<input type='checkbox' id='Rumour$rumour->Id' name='RumourId[]' value='$rumour->Id'> ";
            echo mb_strimwidth(nl2br(htmlspecialchars($rumour->Text)), 0, 100, "...") . "</td>\n";
            echo "<td>";
            $concerns_array = $rumour->getConcerns();
            $concers_str_arr = array();
            foreach ($concerns_array as $concern) {
                $concers_str_arr[] = $concern->getViewLink();
            }
            echo implode(", ", $concers_str_arr);
            echo "</td>";
            echo "<td>";
            echo $rumour->getKnowsCount();
            echo "</td>";
            echo "<td>";
            if (isset($rumour->IntrigueId)) {
                $intrigue = $rumour->getIntrigue();
                echo "<a href='view_intrigue.php?Id=$intrigue->Id'>$intrigue->Number. $intrigue->Name</a>";
            }
            echo "</td>";
            
            echo "<td>" . showStatusIcon($rumour->Approved) . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>";
        
        ?>
        <input type="submit" value="Lägg till rykten till <?php echo $name ?>"></form>
        <?php  } ?>
    </div>
	
</body>

</html>