<?php
require 'header.php';

if (!$current_person->isComing($current_larp)) {
    header('Location: index.php');
    exit;
}

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
    global $type;
    if($type=="Computer")
    {
        echo "<li style='display:table-cell; width:19%;'>\n";
    } else
    {
        echo "<li style='display:table-cell; width:49%;'>\n";
    }
    
    //Utgår från en persons huvudkaraktär, så därför finns alltid person
    echo "<div class='name'>".$role->getPerson()->Name."</div>\n";
    
    
    echo "<div>Spelar ".$role->Name."</div>";
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
		<title><?php  echo $current_larp->Name; ?></title>
		<link href="../css/style.css" rel="stylesheet" type="text/css">
		<link href="../css/gallery.css" rel="stylesheet" type="text/css">

		<link rel="icon" type="image/x-icon" href="images/<?php echo $current_larp->getCampaign()->Icon; ?>">




</HEAD>

<BODY>


	<DIV class="participants">

		<H1>Funktionärer på <?php  echo $current_larp->Name; ?></H1>

		<?php 
		$officialTypes = OfficialType::allActive($current_larp);
		
		foreach ($officialTypes as $officialType) {
		    $persons = Person::getAllOfficialsByType($officialType, $current_larp);
		    $temp=0;


		    
		    echo "<h2>$officialType->Name</h2>\n";


            echo "<div class='container'>\n";
            if ((empty($persons) or count($persons)==0)) {
                echo "Inga utsedda än.";
            }
            else {
                echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
                foreach ($persons as $person) {
                    $role = $person->getMainRole($current_larp);
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
		
