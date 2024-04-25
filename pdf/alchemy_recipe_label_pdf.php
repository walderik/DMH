<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class AlchemyRecipeLabels extends FPDF {
    
    
    function Header()
    {
        global $root;
        $this->Image($root . '/images/alkemi_preparat_standing_3_2.jpg',null,null,208);
    }
    
 
    function PrintLabel(int $x, int $y, Alchemy_Recipe $recipe, $larpname) {
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
        $this->SetXY($x+133, $y);
        $this->MultiCell(39,4,encode_utf_to_iso($recipe->SideEffect),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        $this->SetFont('Times','',14);
        $this->SetXY($x+40, $y-5);
        $this->MultiCell(40,5,encode_utf_to_iso($recipe->Name),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är

        $this->SetFont('Times','',18);
        $this->SetXY($x+72, $y+29);
        $this->MultiCell(29,3,encode_utf_to_iso($recipe->Level),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är

        
        $this->SetFont('Times','',8);
        $this->SetXY($x+40, $y+57);
        $this->MultiCell(40,3,encode_utf_to_iso($larpname),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        /*
        $left = 21;
        if (!is_null($when)) {
            $this->SetXY(150,60);
            $this->Cell(80,10,$when,0,1);
        }
        $this->SetXY($left, 68);
        # http://www.fpdf.org/en/doc/cell.htm
        # https://stackoverflow.com/questions/3514076/special-characters-in-fpdf-with-php
        $this->Cell(80,10,encode_utf_to_iso($sender),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
        $this->SetXY($left, 88);
        $this->Cell(80,10,encode_utf_to_iso($receiver),0,1);
        */
    }
     
     function PrintRecipeLabels(Alchemy_Recipe $recipe, LARP $larp) {
        $this->SetMargins(0, 0);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        
        $col_1_x = 18;
        
        $row_1_y = 32;
        $row_2_y = 127;
        $row_3_y = 222;
        

        //Ruta 1 
        $this->PrintLabel($col_1_x,$row_1_y,$recipe, $larp->Name);

        //Ruta 2 
        $this->PrintLabel($col_1_x,$row_2_y,$recipe, $larp->Name);
        
        //Ruta 3
        $this->PrintLabel($col_1_x,$row_3_y,$recipe, $larp->Name);
        
        
        
         
    }
    

	
	
}

