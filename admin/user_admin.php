 <?php
 include_once 'header_subpage.php';
 
?>


    <div class="content">   
        <h1>Användare</h1>
        <?php
        
        $user_array = User::all();
        $resultCheck = count($user_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Namn</th><th>Email</th><th>Admin</th><th>Aktivering</th><th>Kan Logga in</th>\n";
            foreach ($user_array as $user) {
                if ($user->Blocked) {
                    echo "<tr style = 'text-decoration:line-through;'>\n";
                } else {
                    echo "<tr>\n";
                }
                echo "<td>$user->Name</td>\n";
                echo "<td>$user->Email ".contactEmailIcon($user->Name,$user->Email)."</td>\n";
                if ($current_user->Email == $user->Email ) {
                    echo "<td>" . showStatusIcon($user->IsAdmin) . "</td>\n";
                } else {
                    echo "<td><a href='logic/toggle_is_admin.php?user_id=$user->Id'>" . showStatusIcon($user->IsAdmin) . "</a>";
                }
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
                echo "</td>\n</tr>\n";
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