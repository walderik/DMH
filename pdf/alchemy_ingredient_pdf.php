<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class ALCHEMY_INGREDIENT_PDF extends PDF_MemImage {
    
    
    public $margin = 10;
    public $title;
    public $handfonts = ['cherish','dancingscript','daniel','dawningofanewday','ekologiehand','homemadeapple','mynerve',
                         'reeniebeanie','simplyglamorous','splash','sueellenfrancisco','zeyada'];
    
    public $calligraphyfonts = ['BelweGotisch', 'AliceInWonderland', 'DSCaslonGotisch', 'eaglelake', 'UncialAntiqua'];
    
    public const Handwriting = 0;
    public const Calligraphy = 1;
    
    public $rut_width;
    public $rut_height;
    
    function all_ingredients_for_all_suppliers(Array $alchemy_suppliers, $type, LARP $larp)
    {
        
        $this->SetAutoPageBreak(true , 1.5);
        $this->AddFont('Smokum','');
        $this->AddFont('specialelite','');
        if ($type == ALCHEMY_INGREDIENT_PDF::Handwriting) foreach ($this->handfonts as $font) $this->AddFont($font,'');
        elseif ($type == ALCHEMY_INGREDIENT_PDF::Calligraphy) foreach ($this->calligraphyfonts as $font) $this->AddFont($font,'');
        //         $this->AddPage('L','A5',270);
        foreach ($alchemy_suppliers as $alchemy_supplier) {
            $this->all_ingredients_for_one_suppliers($alchemy_supplier, $type, $larp);
        }
    }
    
    function one_resource_on_one_page(Alchemy_Ingredient $ingredient, $type, LARP $larp) 
    {
        
        $this->SetAutoPageBreak(true , 1.5);
        $this->AddFont('Smokum','');
        $this->AddFont('specialelite','');
        if ($type == ALCHEMY_INGREDIENT_PDF::Handwriting) foreach ($this->handfonts as $font) $this->AddFont($font,'');
        elseif ($type == ALCHEMY_INGREDIENT_PDF::Calligraphy) foreach ($this->calligraphyfonts as $font) $this->AddFont($font,'');
        
        $this->print_many_of_ingredients(array($ingredient), $type, 21, $larp);
    }
    
    
    
    
    
    
    function Header()
    {
        global $root;
        
        $this->Line($this->margin, $this->margin, $this->GetPageWidth()-$this->margin, $this->margin); # Topp
        $this->Line($this->margin, $this->GetPageHeight()-$this->margin, $this->GetPageWidth()-$this->margin, $this->GetPageHeight()-$this->margin); # Bottom
        $this->Line($this->margin, $this->margin, $this->margin, $this->GetPageHeight()-$this->margin); # Vänster
        $this->Line($this->GetPageWidth()-$this->margin, $this->margin, $this->GetPageWidth()-$this->margin, $this->GetPageHeight()-$this->margin); # Höger
        
        # Header på sidan
        $this->SetXY($this->margin, 0);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,10,encode_utf_to_iso($this->title),0,1,'L');
    }
    
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-10);
        // Select Arial italic 8
        $this->SetFont('Arial','I',8);
        // Print centered page number
        $this->Cell(0, 10, 'Sidan '.$this->PageNo(), 0, 0, 'R');
    }
    
    function all_ingredients_for_one_suppliers(Alchemy_Supplier $supplier, $type, Larp $larp) {
        global $root;
        
        
        $supplier_ingredients = $supplier->getIngredientAmounts($larp);
        
        $amount_to_print = 0;
        foreach ($supplier_ingredients as $supplier_ingredient) {
            if (!$supplier_ingredient->IsApproved) continue;
            if ($supplier_ingredient->Amount > 0) $amount_to_print += $supplier_ingredient->Amount;
        }
        if ($amount_to_print <= 0) return;
        
        $seller_name = $supplier->getRole()->Name;
 
        $this->print_many_supplier_ingredients($type, $supplier_ingredients, $seller_name, $larp, $amount_to_print); 
    }
    
    
    function print_many_supplier_ingredients($type, $supplier_ingredients, $owner_name, $larp, $amount_to_print)
    {
        if ($type == ALCHEMY_INGREDIENT_PDF::Calligraphy) {
            $font = $this->calligraphyfonts[array_rand($this->calligraphyfonts, 1)];
        } else {
            $font = $this->handfonts[array_rand($this->handfonts, 1)];
        }
        
        $this->title = $owner_name;
        $this->AddPage();

        
        $this->rut_width  = ($this->GetPageWidth()  - 2*$this->margin) / 3;
        $this->rut_height = ($this->GetPageHeight() - 2*$this->margin) / 7;
        
        # Header på sidan
        $this->SetXY($this->margin, 0);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,10,encode_utf_to_iso($owner_name),0,1,'L');
        
        
        # Rita rutor
        $x_nr = 0;
        $y_nr = 0;
        $total_printed = 0;
        $this->Line($this->margin, $this->margin+($this->rut_height), $this->GetPageWidth()-$this->margin, $this->margin+($this->rut_height)); # Första Horisontell linje under
        
        foreach ($supplier_ingredients as $supplier_ingredient) {
            if (!$supplier_ingredient->IsApproved) continue;
            if ($supplier_ingredient->Amount <= 0) continue;
            
            $ingredient = $supplier_ingredient->getIngredient();
            if (!$ingredient->IsApproved) continue;
            
            for ($i = 0; $i < $supplier_ingredient->Amount; $i++){
               
                $this->print_ingredient_patch($ingredient, $font, $x_nr, $y_nr, $larp, $owner_name);
                $total_printed += 1;
                
                $x_nr += 1;
                
                if ($x_nr >= 3 && $total_printed < $amount_to_print) {
                    # Ny rad
                    $x_nr = 0;
                    $y_nr += 1;
                    $this->Line($this->margin, $this->margin+(($y_nr+1)*$this->rut_height), $this->GetPageWidth()-$this->margin, $this->margin+(($y_nr+1)*$this->rut_height)); # Horisontell linje under
                } elseif ($x_nr < 3 ) {
                    # Dra vertikal linje i slutet av rutan
                    $this->Line($this->margin+(($x_nr)*$this->rut_width), $this->margin+(($y_nr)*$this->rut_height), $this->margin+(($x_nr)*$this->rut_width), $this->margin+(($y_nr+1)*$this->rut_height));
                    
                }
                # Gör en ny sida om vi är på botten av sidan, men inte om allt är printat
                if ($y_nr > 6 && $total_printed < $amount_to_print) {
                    $this->AddPage();
                    $x_nr = 0;
                    $y_nr = 0;
                    $this->Line($this->margin, $this->margin+($this->rut_height), $this->GetPageWidth()-$this->margin, $this->margin+($this->rut_height)); # Första Horisontell linje under
                }
            }
            
        }
    }

    
    function print_many_of_ingredients($ingredients, $type, $amount, $larp)
    {
        if ($type == ALCHEMY_INGREDIENT_PDF::Calligraphy) {
            $font = $this->calligraphyfonts[array_rand($this->calligraphyfonts, 1)];
        } else {
            $font = $this->handfonts[array_rand($this->handfonts, 1)];
        }
        
        $this->title = 'Ingredienskort';
        $this->AddPage();
        
        
        $this->rut_width  = ($this->GetPageWidth()  - 2*$this->margin) / 3;
        $this->rut_height = ($this->GetPageHeight() - 2*$this->margin) / 7;
        
        # Header på sidan
        $this->SetXY($this->margin, 0);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,10,encode_utf_to_iso('Ingredienskort'),0,1,'L');
        
        $amount_to_print = count($ingredients) * $amount;
        
        # Rita rutor
        $x_nr = 0;
        $y_nr = 0;
        $total_printed = 0;
        $this->Line($this->margin, $this->margin+($this->rut_height), $this->GetPageWidth()-$this->margin, $this->margin+($this->rut_height)); # Första Horisontell linje under
        foreach ($ingredients as $ingredient) {
            for ($i = 0; $i < $amount; $i++){
                
                $this->print_ingredient_patch($ingredient, $font, $x_nr, $y_nr, $larp, '');
                $total_printed += 1;
                
                $x_nr += 1;
                
                if ($x_nr >= 3 && $total_printed < $amount_to_print) {
                    # Ny rad
                    $x_nr = 0;
                    $y_nr += 1;
                    $this->Line($this->margin, $this->margin+(($y_nr+1)*$this->rut_height), $this->GetPageWidth()-$this->margin, $this->margin+(($y_nr+1)*$this->rut_height)); # Horisontell linje under
                } elseif ($x_nr < 3 ) {
                    # Dra vertikal linje i slutet av rutan
                    $this->Line($this->margin+(($x_nr)*$this->rut_width), $this->margin+(($y_nr)*$this->rut_height), $this->margin+(($x_nr)*$this->rut_width), $this->margin+(($y_nr+1)*$this->rut_height));
                    
                }
                if ($y_nr > 6 && $total_printed < $amount_to_print) {
                    $this->AddPage();
                    $x_nr = 0;
                    $y_nr = 0;
                    $this->Line($this->margin, $this->margin+($this->rut_height), $this->GetPageWidth()-$this->margin, $this->margin+($this->rut_height)); # Första Horisontell linje under
                }
            }
        }
    }
    
    
    
    /**
     * @param ingredient
     * @param font
     * @param rut_width
     * @param seller_name
     */
    function print_ingredient_patch(Alchemy_Ingredient $ingredient, $font, $x_nr, $y_nr, $larp, $seller_name) {
        global $root;

        $squareX = $this->margin+($x_nr * $this->rut_width);
        $squareY = $y_nr * $this->rut_height;
        
        $this->Image($root . '/images/bytesruna.jpg', $this->rut_width-10 - 3 + $squareX, 3*($this->rut_height/4) + $squareY+5, 5);
        
        $size = 44;
        $txt = $ingredient->Name;
        
        # En bit kod för att säkerställa att inget hamnar utanför kanten på rutan
        $this->SetFont($font,'',$size);
        $slen = $this->GetStringWidth($txt,0);
        while ($slen > ($this->rut_width-7)) {
            $size -= 1;
            $this->SetFont($font,'',$size);
            $slen = $this->GetStringWidth($txt,0);
        }
        # Fix för font som alltid blir för stor
        if ($font == 'simplyglamorous' || $font == 'cherish') $this->SetFont($font,'',$size-2);
        
        $this->SetXY($squareX, ($this->rut_height/2)-2 + $squareY);
        $this->Cell($this->rut_width,10,encode_utf_to_iso(ucfirst($txt)),0,1,'C');
        
        //Skriv ut essenser/"katalysator"
        if ($ingredient->isCatalyst()) $txt = "Katalysator";
        else $txt = $ingredient->getEssenceNames();
     
        $this->SetFont($font,'',10);
        $this->SetXY($squareX, ($this->rut_height/2)+8 + $squareY);
        $this->Cell($this->rut_width,10,encode_utf_to_iso(ucfirst($txt)),0,1,'C');
         
        //Skriv ut nivå 
        $txt = "Nivå ".$ingredient->Level;         $this->SetFont($font,'',10);
        $this->SetXY($squareX, ($this->rut_height/2)+13 + $squareY);
        $this->Cell($this->rut_width,10,encode_utf_to_iso(ucfirst($txt)),0,1,'C');
        
        //Skriv ut effekt
        if (!empty(trim($ingredient->Effect))) {
            $txt = "Effekt: ".$ingredient->Effect;
            $this->SetFont($font,'',10);
            $this->SetXY($squareX, ($this->rut_height/2)+18 + $squareY);
            $this->Cell($this->rut_width,10,encode_utf_to_iso(ucfirst($txt)),0,1,'C');
        }

        $size=8;
        $this->SetFont($font,'',$size);
        
        $txt = $seller_name;
        $slen = $this->GetStringWidth($txt,0);
        while ($slen > $this->rut_width-3) {
            $size -= 1;
            $this->SetFont($font,'',$size);
            $slen = $this->GetStringWidth($txt,0);
        }
        $this->SetXY( 3+$squareX, 8 + $squareY);
        $this->Cell($this->rut_width,10,encode_utf_to_iso($txt),0,1,'L');
            
        $size=5;
        $this->SetFont($font,'',$size);
        
        $txt = $larp->Name;
        $slen = $this->GetStringWidth($txt,0);
        while ($slen > $this->rut_width-3) {
            $size -= 1;
            $this->SetFont($font,'',$size);
            $slen = $this->GetStringWidth($txt,0);
        }
        $this->SetXY($squareX + $this->rut_width - 3 - $slen, ($this->rut_height+3) + $squareY);
        $this->Cell($this->rut_width,10,encode_utf_to_iso($txt),0,1,'L');
    }

    
}
