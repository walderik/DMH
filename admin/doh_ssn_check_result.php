<?php

include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $ssn_list = $_POST['ssn_list'];
    
    $ssn_array = explode("\n", $ssn_list);
    
    
    $res = Array();
    
    foreach ($ssn_array as $ssn) {
        $ssn = trim($ssn);
        if (strlen($ssn) > 6) {
            $val = check_membership($ssn, "2023");
            
            
            if ($val == 1) {
                $IsMember=1;
            }
            else {
                $IsMember = 0;
            }
            
            $res[] = Array ($ssn, $IsMember);
        }
    }
    
    
    
}
include 'navigation_subpage.php';
?>


	<div class="content">
		<h1>DOH 2023 medlemskontroll</h1>
		
		<table class="small_data">
		
		<?php 
		foreach($res as $item) {
		    echo "<tr><td>$item[0]</td><td>".ja_nej($item[1])."</td><td>".showStatusIcon($item[1])."</td></tr>";
		}
		
		
		?>
		
		
		
		</table>
		
	</div>


</body>
</html>
			