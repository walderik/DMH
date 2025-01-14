<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class RESOURCE_PDF extends PDF_MemImage {
    
    
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
//         $this->Image($root . '/images/telegram.png',null,null,200);
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
    
    function SetText(Titledeed $titledeed, $type, LARP $larp, $owner) {
        global $root;
        $is_dmh = false;
        $dmh_image = $root . '/images/resurs_bakgrund_dmh.jpeg';
        if ($larp->getCampaign()->is_dmh() && !$type == RESOURCE_PDF::Calligraphy)$is_dmh = true;
        
        if ($type == RESOURCE_PDF::Calligraphy) {
            $font = $this->calligraphyfonts[array_rand($this->calligraphyfonts, 1)];
        } else {
            $font = $this->handfonts[array_rand($this->handfonts, 1)];
        }
        $size = 44;
        
        $resources = $titledeed->Produces();
        
        $check_total = 0;
        foreach ($resources as $resource_titledeed) {
            if ($resource_titledeed->Quantity > 0) $check_total += $resource_titledeed->Quantity;
        }
        if ($check_total <= 0) return;
        
        
        if ($titledeed->isGeneric()) $this->title = $titledeed->Name . " - ".$owner;
        else $this->title = $titledeed->Name;
        $this->AddPage();

        $height = $this->GetPageHeight();
        $width  = $this->GetPageWidth();
        
        $rut_width  = ($width  - 2*$this->margin) / 3;
        if ($is_dmh) {
            
            list($image_width, $image_height) =  getimagesize($dmh_image);
            $rut_height = round(($image_height / $image_width) * $rut_width);
            
            $max_y_nr = 5;
        }
        else {
            $rut_height = ($height - 2*$this->margin) / 7;
            $max_y_nr = 6;
        }

        
        $max_image_width = 25;
        $max_image_height = 18;
        
        # Header på sidan
        $this->SetXY($this->margin, 0);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,10,encode_utf_to_iso($this->title),0,1,'L');
        
        
        
        # Rita rutor
        $x_nr = 0;
        $y_nr = 0;
        if (!$is_dmh) $this->Line($this->margin, $this->margin+($rut_height), $width-$this->margin, $this->margin+($rut_height)); # Första Horisontell linje under
        
        foreach ($resources as $resource_titledeed) {
            
            if ($resource_titledeed->Quantity <= 0) continue;
            
            $resource = $resource_titledeed->getResource();
            if ($resource->hasImage()) $image = Image::loadById($resource->ImageId);
            else $image=null;
            
            for ($i = 0; $i < $resource_titledeed->Quantity; $i++){
                $squareX = $this->margin+($x_nr * $rut_width);
                $squareY = $y_nr * $rut_height;
                
                if ($is_dmh) $this->Image($dmh_image, $squareX, $squareY + $rut_height/4, $rut_width);
                
//                 # Rubriken
//                 $this->SetFont('specialelite','',12);
//                 $this->SetXY( 3+$this->margin+($x_nr * $rut_width), $this->margin+($y_nr * $rut_height));
//                 $txt = ucfirst("Kvitto för");
//                 $this->Cell($rut_width,10,encode_utf_to_iso($txt),0,1,'L');
                
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
                $txt = "$resource->UnitSingular";
//                 $txt = "Bal ull eller ".$font;
                
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
                
                $this->SetXY($squareX, ($rut_height/2)+2 + $squareY);
                $this->Cell($rut_width,10,encode_utf_to_iso(ucfirst($txt)),0,1,'C');
                
                # Undertext
//                 if ($titledeed->Tradeable && !$titledeed->IsTradingPost) {
//                     $this->SetFont('specialelite','',11);
//                     $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height-4) + ($y_nr * $rut_height));
//                     $this->Cell($rut_width,10,encode_utf_to_iso(ucfirst("Finns i marknadslagret")),0,1,'L');
//                     $this->SetFont('specialelite','',10);
//                     $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height+1) + ($y_nr * $rut_height));
//                     $this->Cell($rut_width,10,encode_utf_to_iso('för '.ucfirst($titledeed->Name)),0,1,'L');
//                 } else {
//                     $this->SetFont('specialelite','',12);
//                     $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height-3) + ($y_nr * $rut_height));
//                     $this->Cell($rut_width,10,encode_utf_to_iso(ucfirst("Finns i marknadslagret")),0,1,'L');
//                 }

                if ($type == RESOURCE_PDF::Handwriting) {

                    if ($titledeed->Tradeable && !$titledeed->IsTradingPost) {
                        $this->SetFont('specialelite','',11);
                        $this->SetXY( 3+$squareX, ($rut_height-4) + $squareY-1);
                        $this->Cell($rut_width,10,encode_utf_to_iso(ucfirst("Från")),0,1,'L');
                        
                        $size=10;
                        $this->SetFont('specialelite','',$size);
                        if ($titledeed->isGeneric()) $txt = $owner;
                        else $txt = $titledeed->Name;
                        
                        $slen = $this->GetStringWidth($txt,0);
                        while ($slen > $rut_width-7) {
                            $size -= 1;
                            $this->SetFont('specialelite','',$size);
                            $slen = $this->GetStringWidth($txt,0);
                        }
                        $this->SetXY( 3+$squareX, ($rut_height+1) + $squareY-2);
                        $this->Cell($rut_width,10,encode_utf_to_iso(ucfirst($txt)),0,1,'L');
                    }
                } elseif ($type == RESOURCE_PDF::Calligraphy) {
                    $this->SetFont($font,'',11);
                    $this->SetXY( 3+$squareX, ($rut_height-4) + $squareY);
                    $this->Cell($rut_width,10,encode_utf_to_iso(ucfirst("Från")),0,1,'L');
                    
                    $size=10;
                    $this->SetFont($font,'',$size);
                    if ($titledeed->isGeneric()) $txt = $owner;
                    else $txt = $titledeed->Name;
                    $slen = $this->GetStringWidth($txt,0);
                    while ($slen > $rut_width-3) {
                        $size -= 1;
                        $this->SetFont($font,'',$size);
                        $slen = $this->GetStringWidth($txt,0);
                    }
                    $this->SetXY( 3+$squareX, ($rut_height+1) + $squareY);
                    $this->Cell($rut_width,10,encode_utf_to_iso(ucfirst($txt)),0,1,'L');
                    
                }
                
                $size=5;
                if ($type == RESOURCE_PDF::Handwriting) $this->SetFont('specialelite','',$size);
                else $this->SetFont($font,'',$size);
                
                $txt = $larp->Name;
                $slen = $this->GetStringWidth($txt,0);
                while ($slen > $rut_width-3) {
                    $size -= 1;
                    $this->SetFont($font,'',$size);
                    $slen = $this->GetStringWidth($txt,0);
                }
                if ($is_dmh) $this->SetXY($squareX + $rut_width - 3 - $slen, ($rut_height+4) + $squareY-2);
                else $this->SetXY($squareX + $rut_width - 1 - $slen, ($rut_height+4) + $squareY);
                
                $this->Cell($rut_width,10,encode_utf_to_iso($txt),0,1,'L');
                
                
                $x_nr += 1;
                
                if ($x_nr >= 3) {
                    # Ny rad
                    $x_nr = 0;
                    $y_nr += 1;
                    if(!$is_dmh) $this->Line($this->margin, $this->margin+(($y_nr+1)*$rut_height), $width-$this->margin, $this->margin+(($y_nr+1)*$rut_height)); # Horisontell linje under
                } elseif ($x_nr < 3 ) {
                    # Dra vertikal linje i slutet av rutan
                    if(!$is_dmh) $this->Line($this->margin+(($x_nr)*$rut_width), $this->margin+(($y_nr)*$rut_height), $this->margin+(($x_nr)*$rut_width), $this->margin+(($y_nr+1)*$rut_height));
                    
                }
                if ($y_nr > $max_y_nr) {
                    $this->AddPage();
                    $x_nr = 0;
                    $y_nr = 0;
                    if(!$is_dmh) $this->Line($this->margin, $this->margin+($rut_height), $width-$this->margin, $this->margin+($rut_height)); # Första Horisontell linje under
                }
            }
        }
            
    }
    
	
    function all_resources(Array $titledeeds, $type, LARP $larp)
	{

	    $this->SetAutoPageBreak(true , 1.5);
	    $this->AddFont('Smokum','');
	    $this->AddFont('specialelite','');
	    if ($type == RESOURCE_PDF::Handwriting) foreach ($this->handfonts as $font) $this->AddFont($font,'');
	    elseif ($type == RESOURCE_PDF::Calligraphy) foreach ($this->calligraphyfonts as $font) $this->AddFont($font,'');
	    //         $this->AddPage('L','A5',270);
	    foreach ($titledeeds as $titledeed) {
	        $owners = array();
	        foreach ($titledeed->getGroupOwners() as $owner_group) $owners[] = $owner_group->Name;
	        foreach ($titledeed->getRoleOwners() as $owner_role)  $owners[] = $owner_role->Name;
	        
	        if ($titledeed->isGeneric() && !empty($owners) && (sizeof($owners)> 1)) {
	            foreach ($owners as $owner) $this->SetText($titledeed, $type, $larp, $owner);
	        }
    	    else $this->SetText($titledeed, $type, $larp, null);
	    }
	}
}
