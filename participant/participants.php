<?php

require 'header.php';

if (!$current_user->isComing($current_larp)) {
    header('Location: index.php');
    exit;
}

//SESSION_START();

//kolla bredd på användarens skärm för att bestämma antal kolumner med karaktärer

// Check if the "mobile" word exists in User-Agent
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

if($isMob){
    $columns=2;
    $type="Mobile";
    // echo 'Using Mobile Device...';
}else{
    
    $columns=5;
    $type="Computer";
    // echo 'Using Desktop...';
}











$temp=0;
/*$_SESSION['tempwidth'] ="<script>document.write(screen.width); </script>";
 $width=intval($_SESSION['tempwidth']);
 if($width<=700)
 {
 //$columns=2;
 // echo "<script>window.location.replace('...'); </script>";   window.location.replace('...');
 
 } else
 {
 //$columns=5;
 $_SESSION['type']="computer";
 
 }
 
 //echo $width;
 if(isset($_SESSION['type']))
 {
 $type=$_SESSION['type'];
 //  echo $type;
 if ($type=="mobile")
 {
 $columns=2;
 } else
 {
 $columns=5;
 }
 }
 
 session_destroy();*/

function print_role($role) {
    global $current_larp;
    global $type;
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    if($type=="Computer")
    {
        echo "<li style='display:table-cell; width:19%;'>\n";
    } else
    {
        echo "<li style='display:table-cell; width:49%;'>\n";
    }
    
    echo "<div class='name'>$role->Name</div>\n";
    if($larp_role->IsMainRole == 0) echo "<div>Sidokaraktär</div>\n";
    $person = $role->getPerson();
    if ($person->hasPermissionShowName()) {
        echo "<div>Spelas av $person->Name</div>";
    }
    echo "<div class='description'>$role->DescriptionForOthers</div>\n";
    if (isset($role->ImageId) && !is_null($role->ImageId)) {
        $image = Image::loadById($role->ImageId);
        if (!is_null($image)) {
            
            echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
            if (!empty($image->Photographer) && $image->Photographer!="") {
                echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
            }
        }
    }
    else {
        echo "<img src='../images/man-shape.png' />\n";
        echo "<div class='photographer'><a href='https://www.flaticon.com/free-icons/man' title='man icons'>Man icons created by Freepik - Flaticon</a></div>\n";
    }
    echo "</li>\n\n";
    
}
?>
<HTML>

<HEAD>


		<meta charset="utf-8">
		<title><?php  echo $larp->Name; ?></title>
		<link href="../css/style.css" rel="stylesheet" type="text/css">
		<link href="../css/gallery.css" rel="stylesheet" type="text/css">

		<link rel="icon" type="image/x-icon" href="../images/<?php echo $current_larp->getCampaign()->Icon; ?>">




</HEAD>

<BODY>


	<DIV class="participants">

		<H1>Karaktärer i <?php  echo $larp->Name; ?></H1>

		<?php 
		$groups = Group::getAllRegisteredApproved($larp);
		foreach ($groups as $group) {
		    $roles = Role::getAllComingMainRolesInGroup($group, $larp);

		    
            if (!(empty($roles) && count($roles)>0)) {

                echo "<h2>$group->Name</h2>\n";
                if ($group->DescriptionForOthers !="") {
                    echo "<p>".nl2br($group->DescriptionForOthers)."</p>\n";
                }
                
                
                echo "<div class='container'>\n";
                
                echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
                foreach ($roles as $role) {
                    print_role($role);
                   $temp++;
                    if($temp==$columns)
                    {
                        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
                        $temp=0;
                    } 
                    
                }
                $temp=0;

                echo "</ul>\n";
            }
            echo "</DIV>\n";
            
            
		}
		
		
		
		/* Karaktärer utan grupp */	
		$roles = Role::getAllComingMainRolesWithoutGroup($larp);
		

		if ((!empty($roles) && count($roles)!=0)) {
		
		  echo "<h2>Karaktärer utan grupp</h2>\n";
		
		
    		echo "<div class='container'>\n";
    		if ((empty($roles) or count($roles)==0)) {
    		    echo "Inga anmälda i gruppen än.";
    		}
    		else {
    		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
    		    foreach ($roles as $role) {
    		        print_role($role);
    		        $temp++;
                    if($temp==$columns)
                    {
                        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
                        $temp=0;
                    }
    		    }
    		    echo "</ul><ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
    		    $temp=0;
    		    echo "</ul>\n";
    		}
    		
    		echo "</DIV>\n";
		}
		
		?>
	</DIV>



</BODY>

</HTML>
		
