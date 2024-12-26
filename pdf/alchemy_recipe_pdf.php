<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class AlchemyRecipe_PDF extends FPDF {
    
    public $handfonts = ['xiparoslombard','YourRoyalMajesty','UncialAntiqua','TypographerRotunda','medievalsharp','rocksalt','Goticabastard',
        'ComicRunes'];
    
    public $fonts = ['xiparoslombard','YourRoyalMajesty','UncialAntiqua','TypographerRotunda','medievalsharp','rocksalt','Goticabastard',
        'ComicRunes'];
    
    function Header()
    {
        global $root;
        $this->Image($root . '/images/recipe.png',null,null,208);
    }
    
 
    function PrintRecipe(Alchemy_Recipe $recipe, $larpname) {
        $this->SetMargins(0, 0);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        
        //$font = $this->handfonts[array_rand($this->handfonts, 1)];
        $font = 'eaglelake';
        $space = 2;
        
        $left = 38;
        $width = $this->GetPageWidth()-2*$left;
        
        $this->AddFont($font);
        $size = 24;
        $name = $recipe->Name.", nivå ".$recipe->Level;
        if (strlen($name) > 20) $size = 18;
        if (strlen($name) > 30) $size = 16;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 107);
        $this->MultiCell(150,6,encode_utf_to_iso($name),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        /*
        $size = 18;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 135);
        $this->MultiCell(150,10,encode_utf_to_iso($recipe->Description),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        */

        $size = 9;

        
        //Ingredienser
        $ingredientName = "";
        $ingredientNamesArr = array();
        if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
            $ingredients = $recipe->getSelectedIngredients();
            foreach ($ingredients as $ingredient) {
                $ingredientName = "$ingredient->Name (Nivå $ingredient->Level)";
                if ($ingredient->isCatalyst()) $ingredientName .= " - Katalysator";
                $ingredientNamesArr[] = $ingredientName;
            }
        } elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
            
            $essences = Alchemy_Essence::all();
            
            $selectedEssences = $recipe->getSelectedEssenceIds();
            foreach($selectedEssences as $selectedEssenceArr) {
                $selectedEssence = null;
                foreach ($essences as $essence) {
                    if ($essence->Id == $selectedEssenceArr[0]) {
                        $selectedEssence = $essence;
                        break;
                    }
                }
                
                $ingredientName = "$selectedEssence->Name (Nivå ".$selectedEssenceArr[1].")";
                $ingredientNamesArr[] = $ingredientName;
            }
            $ingredientName = "Katalysator (Nivå $recipe->Level)";
            $ingredientNamesArr[] = $ingredientName;
            
        }
        
        $y = $this->GetY()+4;// + $space*3;
        $this->SetXY($left, $y);
        $this->SetFont($font,'',$size+2);
        $this->Cell($width, 6, encode_utf_to_iso("Tillverkas av"),0,0,'L');
        
        $txt = implode(", ", $ingredientNamesArr);
        
        $y = $this->GetY() + $space*2;
        $this->SetXY($left, $y);
        $this->SetFont($font,'',$size);
        $text = trim(encode_utf_to_iso($txt));
        $this->MultiCell($width, 5, $text, 0, 'L');
        $y = $this->GetY() + $space;
        $this->SetXY($left, $y);
        
        
        if (!empty(trim($recipe->Description))) {
            $y = $this->GetY() + $space*3;
            $this->SetXY($left, $y);
            $this->SetFont($font,'',$size+2);
            $this->Cell(150, 6, encode_utf_to_iso("Beskrivning"),0,0,'L');
            
            $y = $this->GetY() + $space*2;
            $this->SetXY($left, $y);
            $this->SetFont($font,'',$size);
            $text = trim(encode_utf_to_iso($recipe->Description));
            $this->MultiCell($width, 5, $text, 0, 'L');
            $y = $this->GetY() + $space;
            $this->SetXY($left, $y);
        }
        
        
        $y = $this->GetY(); // + $space*3;
        $this->SetXY($left, $y);
        $this->SetFont($font,'',$size+2);
        $this->Cell($width, 6, encode_utf_to_iso("Beredning"),0,0,'L');
        
        $y = $this->GetY() + $space*2;
        $this->SetXY($left, $y);
        $this->SetFont($font,'',$size);
        $text = trim(encode_utf_to_iso($recipe->Preparation));
        $this->MultiCell($width, 5, $text, 0, 'L');
        $y = $this->GetY() + $space;
        $this->SetXY($left, $y);
        
        $y = $this->GetY(); // + $space*3;
        $this->SetXY($left, $y);
        $this->SetFont($font,'',$size+2);
        $this->Cell($width, 6, encode_utf_to_iso("Effekt"),0,0,'L');
        
        $y = $this->GetY() + $space*2;
        $this->SetXY($left, $y);
        $this->SetFont($font,'',$size);
        $text = trim(encode_utf_to_iso($recipe->Effect));
        $this->MultiCell($width, 5, $text, 0, 'L');
        $y = $this->GetY() + $space;
        $this->SetXY($left, $y);
        
        
        if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
            $y = $this->GetY(); // + $space*3;
            $this->SetXY($left, $y);
            $this->SetFont($font,'',$size+2);
            $this->Cell($width, 6, encode_utf_to_iso("Bieffekt"),0,0,'L');
            
            $y = $this->GetY() + $space*2;
            $this->SetXY($left, $y);
            $this->SetFont($font,'',$size);
            $text = trim(encode_utf_to_iso($recipe->SideEffect));
            $this->MultiCell($width, 5, $text, 0, 'L');
            $y = $this->GetY() + $space;
            $this->SetXY($left, $y);
        }
        
        $size = 8;
        $this->SetFont($font,'',$size);
        $this->SetXY(120, 260);
        $this->MultiCell(100,6,encode_utf_to_iso($larpname),0); # 1- ger ram runt rutan så vi ser hur stor den är

        $this->SetXY($left-4, 260);
        $txt = "OFF: Alkemiskt recept nivå $recipe->Level. Har du hittat det är det en del av en intrig. Se till det används";
        $this->MultiCell(50,4,encode_utf_to_iso($txt),0); # 1- ger ram runt rutan så vi ser hur stor den är
        
    }
     
}

