<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $titledeed = Titledeed::newFromArray($_POST);
        $titledeed->create();
        if (isset($_POST['ProducesId'])) {
            $titledeed->setProduces($_POST['ProducesId']);
        }
        if (isset($_POST['RequiresId'])) {
            $titledeed->setRequires($_POST['RequiresId']);
        }
    } elseif ($operation == 'delete') {
        Titledeed::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $titledeed = Titledeed::loadById($_POST['Id']);
        $titledeed->setValuesByArray($_POST);
        $titledeed->update();
        $titledeed->deleteAllProduces();
        $titledeed->deleteAllRequires();
        if (isset($_POST['ProducesId'])) {
            $titledeed->setProduces($_POST['ProducesId']);
        }
        if (isset($_POST['RequiresId'])) {
            $titledeed->setRequires($_POST['RequiresId']);
        }
    } elseif ($operation == 'add_titledeed_owner_role') {
        $titledeed = Titledeed::loadById($_POST['id']);
        if (isset($_POST['RoleId'])) $titledeed->addRoleOwners($_POST['RoleId']);
      
    } elseif ($operation == 'add_titledeed_owner_group') {
        $titledeed = Titledeed::loadById($_POST['id']);
        if (isset($_POST['GroupId'])) $titledeed->addGroupOwners($_POST['GroupId']);
        
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
        if ($operation == 'delete') {
            Titledeed::delete($_GET['id']);
        }
        elseif ($operation == 'delete_owner_role') {
            $titledeeId=$_GET['titledeeId'];
            $roleId=$_GET['roleId'];
            $titledeed = Titledeed::loadById($titledeeId);
            $titledeed->deleteRoleOwner($roleId);
        }
        elseif ($operation == 'delete_owner_group') {
            $titledeeId=$_GET['titledeeId'];
            $groupId=$_GET['groupId'];
            $titledeed = Titledeed::loadById($titledeeId);
            $titledeed->deleteGroupOwner($groupId);
        }
    }
 
}

include 'navigation.php';
?>

    <div class="content">
        <h1>Lagfarter</h1>
            <a href="titledeed_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
       <?php
    
       $titledeed_array = Titledeed::allByCampaign($current_larp);
        if (!empty($titledeed_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Plats</th><th>Kan säljas</th><th>Handelsstation</th><th>Producerar</th><th>Behöver</th><th>Ägare</th><th></th><th></th></tr>\n";
            foreach ($titledeed_array as $titledeed) {
                $owner = "";
                if (isset($titledeed->RoleId)) {
                    $role = Role::loadById($titledeed->RoleId);
                    $owner = $role->Name;
                }
                
                echo "<tr>\n";
                echo "<td>" . $titledeed->Id . "</td>\n";
                echo "<td>" . $titledeed->Name . "</td>\n";
                echo "<td>" . $titledeed->Location . "</td>\n";
                echo "<td>" . ja_nej($titledeed->Tradeable) . "</td>\n";
                echo "<td>" . ja_nej($titledeed->IsTradingPost) . "</td>\n";
                echo "<td>" . commaStringFromArrayObject($titledeed->Produces()) . "</td>\n";
                echo "<td>" . commaStringFromArrayObject($titledeed->Requires()) . "</td>\n";
                echo "<td>";
                
                echo "<a href='choose_group.php?operation=add_titledeed_owner_group&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till grupp'></i></a><br>";
                $owner_groups = $titledeed->getGroupOwners();
                foreach ($owner_groups as $owner_group) {
                    echo "<a href='../admin/view_group.php?id=$owner_group->Id'>$owner_group->Name</a> <a href='titledeed_admin.php?operation=delete_owner_group&titledeeId=$titledeed->Id&groupId=$owner_group->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
                }
                
                echo "<a href='choose_role.php?operation=add_titledeed_owner_role&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till grupp'></i></a><br>";
                
                
                $owner_roles = $titledeed->getRoleOwners();
                foreach ($owner_roles as $owner_role) {
                    echo "<a href='../admin/view_role.php?id=$owner_role->Id'>$owner_role->Name <a href='titledeed_admin.php?operation=delete_owner_role&titledeeId=$titledeed->Id&roleId=$owner_role->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
                }
                
                echo "</td>\n";
                
                echo "<td>" . "<a href='titledeed_form.php?operation=update&id=" . $titledeed->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='titledeed_admin.php?operation=delete&id=" . $titledeed->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrarade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>