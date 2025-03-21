<?php
 include_once 'header.php';
 include "navigation.php";
?>

<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>Användare utan registrerad person</h1>
        <?php
        
        $user_array = User::allWithoutPersons();
        $resultCheck = count($user_array);
        if ($resultCheck > 0) {
            $tableId = "users";
            echo "<table id='$tableId' class='data'>";         
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Id</th>".
                "<th onclick='sortTable(1, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(2, \"$tableId\");'>Email</th>".
                "<th onclick='sortTable(3, \"$tableId\");'>Aktivering</th>".
                "<th onclick='sortTable(4, \"$tableId\");'>Kan Logga in</th>".
                "<th onclick='sortTable(5, \"$tableId\");'>Senast inloggad</th>".
                "<th></th>".
                "</tr>\n";
            foreach ($user_array as $user) {
                if ($user->Blocked) {
                    echo "<tr style = 'text-decoration:line-through;'>\n";
                } else {
                    echo "<tr>\n";
                }
                echo "<td>$user->Id</td>\n";
                echo "<td>$user->Name <a href='logic/become_user.php?UserId=$user->Id'>su</a></td>\n";
                echo "<td>$user->Email</td>\n";
                echo "<td>";
                if ($user->ActivationCode == 'activated') {
                    echo "Aktiverad</td>\n";
                }
                else {
                    echo "<a href='logic/toggle_user_activated.php?user_id=$user->Id'>Aktivera</a></td>\n";
                }
                echo "<td>";
                if ($current_user->Email == $user->Email ) {
                    echo showStatusIcon(!$user->Blocked);
                } else {
                    echo "<a href='logic/toggle_user_blocked.php?user_id=$user->Id'>" . showStatusIcon(!$user->Blocked) . "</a>";
                }
                echo "</td>\n";
                echo "<td>$user->LastLogin</td>\n";
                echo "<td>" . "<a href='logic/delete_user.php?id=" . $user->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga användare utan registrerade personer</p>";
        }
        ?>
        
	</div>
</body>
</html>