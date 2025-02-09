<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class AlchemyRecipeLabels extends FPDF {
    
    public static $col_1_x = 7;
    
    public static $row_1_y = 20;
    public static $row_2_y = 115;
    public static $row_3_y = 213;
    
    
    public $row_y;
    
    function Header()
    {
        global $root;
        $this->Image($root . '/images/alkemi_preparat_standing_3_3.jpg',null,null,208);
    }
    
 
    function PrintLabel(int $x, int $y, Alchemy_Recipe $recipe, $alchemistName, $warning, $larpname) {
        if (strlen($recipe->Effect)<100) $size = 14;
        elseif (strlen($recipe->Effect)<200) $size = 12;
        elseif (strlen($recipe->Effect)<300) $size = 10;
        elseif (strlen($recipe->Effect)<400) $size = 8;
        //elseif (strlen($recipe->Effect)<500) $size = 6;
        else $size = 6;
        $this->SetFont('Times','',$size);
        $this->SetXY($x, $y);
        $this->MultiCell(37,4,encode_utf_to_iso($recipe->Effect),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        if (strlen($recipe->SideEffect)<100) $size = 14;
        elseif (strlen($recipe->SideEffect)<200) $size = 12;
        elseif (strlen($recipe->SideEffect)<300) $size = 10;
        elseif (strlen($recipe->SideEffect)<400) $size = 8;
        //elseif (strlen($recipe->Effect)<500) $size = 6;
        else $size = 6;
        $this->SetFont('Times','',$size);
        $this->SetXY($x+153, $y);
        $this->MultiCell(39,4,encode_utf_to_iso($recipe->SideEffect),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        $this->SetFont('Times','',14);
        $this->SetXY($x+48, $y-13);
        $this->MultiCell(40,5,encode_utf_to_iso($recipe->Name),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är

        $this->SetFont('Times','',18);
        $this->SetXY($x+81, $y+45);
        $this->MultiCell(29,3,encode_utf_to_iso($recipe->Level),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är

        
        // $this->SetFont('Times','',8);
        // $this->SetXY($x+40, $y+57);
        // $this->MultiCell(40,3,encode_utf_to_iso($larpname),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är

        if (!empty($alchemistName)) {
            $this->SetFont('Times','',8);
            $this->SetXY($x, $y+72);
            $this->MultiCell(40,3,encode_utf_to_iso("Tillverkad av " . $alchemistName),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        }
        
        if (!empty($warning)) {
            $this->SetFont('Times','',8);
            $this->SetXY($x, $y+57);
            $this->MultiCell(40,3,encode_utf_to_iso($warning),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        }
        
    }
     
    function PrintRecipeLabel(Alchemy_Recipe  $recipe, $alchemistName, $warning, LARP $larp) {
        global $row_y;
        if (!isset($row_y) || $row_y <= 0 || $row_y > 3) {
            $this->SetMargins(0, 0);
            $this->SetAutoPageBreak(false);
            $this->AddPage();
            $row_y = 1;
         }
        
        
        switch ($row_y) {
            case 1:
                //Ruta 1
                $this->PrintLabel(static::$col_1_x,static::$row_1_y,$recipe, $alchemistName, $warning, $larp->Name);
                break;
            case 2:
                //Ruta 2
                $this->PrintLabel(static::$col_1_x,static::$row_2_y,$recipe, $alchemistName, $warning, $larp->Name);
                break;
            case 3:
                //Ruta 3
                $this->PrintLabel(static::$col_1_x,static::$row_3_y,$recipe, $alchemistName, $warning, $larp->Name);
                break;
        }
        $row_y ++;
    }
    
     function PrintRecipeLabels(Alchemy_Recipe $recipe, LARP $larp) {
         $this->PrintRecipeLabel($recipe, "", "", $larp);
         $this->PrintRecipeLabel($recipe, "", "", $larp);
         $this->PrintRecipeLabel($recipe, "", "", $larp);
    }
    
    function PrintRecipeLabelsAmount(Alchemy_Recipe $recipe, int $amount, $alchemistName, $warning, LARP $larp) {
        for ($x = 0; $x < $amount; $x++) {
            $this->PrintRecipeLabel($recipe, $alchemistName, $warning, $larp);
        }
    }
    
    
    

	
	
}

