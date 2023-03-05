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
            echo "<tr><th>Id</td><th>Email</th><th>Admin</th><th>Aktivering</th>\n";
            foreach ($user_array as $user) {
                echo "<tr>\n";
                echo "<td>" . $user->Id . "</td>\n";
                echo "<td>" . $user->Email . "</td>\n";
                $ikon = showStatusIcon($user->IsAdmin);
                if ($current_user->Email == $user->Email ) {
                    echo "<td>" . $ikon . "</td>\n";
                } else {
                    echo "<td><a href='logic/toggle_is_admin.php?user_id=$user->Id'>" . $ikon . "</a>";
                }
                echo "<td>";
                if ($user->ActivationCode == 'activated') {
                    echo "Aktiverad</td>\n";
                }
                else {
                    echo "<a href='logic/toggle_user_activated.php?user_id=$user->Id'>Aktivera</a></td>\n";
                }
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