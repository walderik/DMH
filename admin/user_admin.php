 <?php
 include_once 'header.php';
 
?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>

    <div class="content">   
        <h1>Användare</h1>
        <?php
        
        $user_array = User::all();
        $resultCheck = count($user_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Id</td><th>Email</th><th>Admin</th><th>ActivationCode</th>\n";
            foreach ($user_array as $user) {
                echo "<tr>\n";
                echo "<td>" . $user->Id . "</td>\n";
                echo "<td>" . $user->Email . "</td>\n";
                echo "<td>" . $user->IsAdmin . "</td>\n";
                echo "<td>" . $user->ActivationCode . "</td>\n";
                
//                 echo "<td>" . "<a href='larp_form.php?operation=update&id=" . $larp->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
//                 echo "<td>" . "<a href='larp_admin.php?operation=delete&id=" . $larp->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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