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
        <h1>Användare</h1>
        <a href='user_unused.php'>Användare utan registrerad person</a>
        <?php
        
        $user_array = User::all();
        $resultCheck = count($user_array);
        if ($resultCheck > 0) {
            $tableId = "users";
            echo "<table id='$tableId' class='data'>";         
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(1, \"$tableId\");'>Email</th>".
                "<th onclick='sortTable(3, \"$tableId\");'>Aktivering</th>".
                "<th onclick='sortTable(4, \"$tableId\");'>Kan Logga in</th>".
                "<th onclick='sortTable(5, \"$tableId\");'>Senast inloggad</th>\n";
            foreach ($user_array as $user) {
                if ($user->Blocked) {
                    echo "<tr style = 'text-decoration:line-through;'>\n";
                } else {
                    echo "<tr>\n";
                }
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
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrerade användare</p>";
        }
        ?>
        
	</div>
</body>
</html>