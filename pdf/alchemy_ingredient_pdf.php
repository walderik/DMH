<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
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

    
    function Header()
    {
        global $root;
        # Ramen runt sidan
        $height = $this->GetPageHeight();
        $width  = $this->GetPageWidth();
        
        $this->Line($this->margin, $this->margin, $width-$this->margin, $this->margin); # Topp
        $this->Line($this->margin, $height-$this->margin, $width-$this->margin, $height-$this->margin); # Bottom
        $this->Line($this->margin, $this->margin, $this->margin, $height-$this->margin); # Vänster
        $this->Line($width-$this->margin, $this->margin, $width-$this->margin, $height-$this->margin); # Höger
        
        # Header på sidan
        $this->SetXY($this->margin, 0);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,10,utf8_decode($this->title),0,1,'L');
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
    
    function SetText(Alchemy_Supplier $supplier, $type, Larp $larp) {
        if ($type == ALCHEMY_INGREDIENT_PDF::Calligraphy) {
            $font = $this->calligraphyfonts[array_rand($this->calligraphyfonts, 1)];
        } else {
            $font = $this->handfonts[array_rand($this->handfonts, 1)];
        }
        $size = 44;
        
        $supplier_ingredients = $supplier->getIngredientAmounts($larp);
        
        $check_total = 0;
        foreach ($supplier_ingredients as $supplier_ingredient) {
            if ($supplier_ingredient->Amount > 0) $check_total += $supplier_ingredient->Amount;
        }
        if ($check_total <= 0) return;
        
        $name = $supplier->getRole()->Name;
        $this->title = $name;
        $this->AddPage();

        $height = $this->GetPageHeight();
        $width  = $this->GetPageWidth();
        
        $rut_width  = ($width  - 2*$this->margin) / 3;
        $rut_height = ($height - 2*$this->margin) / 7;
        
        $max_image_width = 25;
        $max_image_height = 18;
        
        # Header på sidan
        $this->SetXY($this->margin, 0);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,10,utf8_decode($name),0,1,'L');
        
        
        # Rita rutor
        $x_nr = 0;
        $y_nr = 0;
        $this->Line($this->margin, $this->margin+($rut_height), $width-$this->margin, $this->margin+($rut_height)); # Första Horisontell linje under
        
        foreach ($supplier_ingredients as $supplier_ingredient) {
            
            if ($supplier_ingredient->Amount <= 0) continue;
            
            $ingredient = $supplier_ingredient->getIngredient();
            //if ($resource->hasImage()) $image = Image::loadById($resource->ImageId);
            //else $image=null;
            $image = null;
            
            for ($i = 0; $i < $supplier_ingredient->Amount; $i++){
                $squareX = $this->margin+($x_nr * $rut_width);
                $squareY = $y_nr * $rut_height;
                
//                 # Rubriken
//                 $this->SetFont('specialelite','',12);
//                 $this->SetXY( 3+$this->margin+($x_nr * $rut_width), $this->margin+($y_nr * $rut_height));
//                 $txt = ucfirst("Kvitto för");
//                 $this->Cell($rut_width,10,utf8_decode($txt),0,1,'L');
                
                # Resursnamnet

                if (isset($image)) {
                    $v = 'img'.md5($image->file_data);
                    $GLOBALS[$v] = $image->file_data;
                    list($imageWidth, $imageHeight) =  getimagesize('var://'.$v);
                    
                    if ($imageWidth > $imageHeight) {
                        $this->MemImage($image->file_data, $rut_width-$max_image_width - 3 + $squareX, 3*($rut_height/4) + $squareY, $max_image_width);
                    } else {
                        $realWidth = round(($imageWidth / $imageHeight) * $max_image_height);
                        $this->MemImage($image->file_data, $rut_width - $realWidth - 3 + $squareX, 3*($rut_height/4) + $squareY, 0, $max_image_height);
                    }
                }
                
                
                
                
                $size = 44;
                $txt = $ingredient->Name;

                
                # En bit kod för att säkerställa att inget hamnar utanför kanten på rutan
                $this->SetFont($font,'',$size);
                $slen = $this->GetStringWidth($txt,0);
                while ($slen > ($rut_width-7)) {
                    $size -= 1;
                    $this->SetFont($font,'',$size);
                    $slen = $this->GetStringWidth($txt,0);
                }
                # Fix för font som alltid blir för stor
                if ($font == 'simplyglamorous' || $font == 'cherish') $this->SetFont($font,'',$size-2);
                
                $this->SetXY($squareX, ($rut_height/2)-2 + $squareY);
                $this->Cell($rut_width,10,utf8_decode(ucfirst($txt)),0,1,'C');
                
                //Skriv ut essenser
                $essenceNames = $ingredient->getEssenceNames();
                 $this->SetFont($font,'',10);
                 $this->SetXY($squareX, ($rut_height/2)+8 + $squareY);
                 $this->Cell($rut_width,10,utf8_decode(ucfirst($essenceNames)),0,1,'C');


                $this->SetFont($font,'',11);
                $this->SetXY( 3+$squareX, ($rut_height-4) + $squareY);
                $this->Cell($rut_width,10,utf8_decode(ucfirst("Från")),0,1,'L');
                
                $size=10;
                $this->SetFont($font,'',$size);
                
                $txt = $name;
                $slen = $this->GetStringWidth($txt,0);
                while ($slen > $rut_width-3) {
                    $size -= 1;
                    $this->SetFont($font,'',$size);
                    $slen = $this->GetStringWidth($txt,0);
                }
                $this->SetXY( 3+$squareX, ($rut_height+1) + $squareY);
                $this->Cell($rut_width,10,utf8_decode($txt),0,1,'L');
                    
                $size=5;
                $this->SetFont($font,'',$size);
                
                $txt = $larp->Name;
                $slen = $this->GetStringWidth($txt,0);
                while ($slen > $rut_width-3) {
                    $size -= 1;
                    $this->SetFont($font,'',$size);
                    $slen = $this->GetStringWidth($txt,0);
                }
                $this->SetXY($squareX + $rut_width - 3 - $slen, ($rut_height+3) + $squareY);
                $this->Cell($rut_width,10,utf8_decode($txt),0,1,'L');
                
                
                
                $x_nr += 1;
                
                if ($x_nr >= 3) {
                    # Ny rad
                    $x_nr = 0;
                    $y_nr += 1;
                    $this->Line($this->margin, $this->margin+(($y_nr+1)*$rut_height), $width-$this->margin, $this->margin+(($y_nr+1)*$rut_height)); # Horisontell linje under
                } elseif ($x_nr < 3 ) {
                    # Dra vertikal linje i slutet av rutan
                    $this->Line($this->margin+(($x_nr)*$rut_width), $this->margin+(($y_nr)*$rut_height), $this->margin+(($x_nr)*$rut_width), $this->margin+(($y_nr+1)*$rut_height));
                    
                }
                if ($y_nr > 6) {
                    $this->AddPage();
                    $x_nr = 0;
                    $y_nr = 0;
                    $this->Line($this->margin, $this->margin+($rut_height), $width-$this->margin, $this->margin+($rut_height)); # Första Horisontell linje under
                }
            }
        }
            
    }
    
	
    function all_resources(Array $alchemy_suppliers, $type, LARP $larp)
	{

	    $this->SetAutoPageBreak(true , 1.5);
	    $this->AddFont('Smokum','');
	    $this->AddFont('specialelite','');
	    if ($type == ALCHEMY_INGREDIENT_PDF::Handwriting) foreach ($this->handfonts as $font) $this->AddFont($font,'');
	    elseif ($type == ALCHEMY_INGREDIENT_PDF::Calligraphy) foreach ($this->calligraphyfonts as $font) $this->AddFont($font,'');
	    //         $this->AddPage('L','A5',270);
	    foreach ($alchemy_suppliers as $alchemy_supplier) {
	        $this->SetText($alchemy_supplier, $type, $larp);
	    }
	}
}
