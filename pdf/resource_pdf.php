<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class RESOURCE_PDF extends FPDF {
    
    
    public $margin = 10;
    public $title;
    public $handfonts = ['cherish','dancingscript','daniel','dawningofanewday','ekologiehand','homemadeapple','mynerve',
                         'reeniebeanie','simplyglamorous','splash','sueellenfrancisco','zeyada'];
    
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
    
    function SetText(Titledeed $titledeed, Campaign $campaign) {
        
        $resources = $titledeed->Produces();
        
        $check_total = 0;
        foreach ($resources as $resource_titledeed) {
            if ($resource_titledeed->Quantity > 0) $check_total += $resource_titledeed->Quantity;
        }
        if ($check_total <= 0) return;
        
        $this->title = $titledeed->Name;
        $this->AddPage();

        $height = $this->GetPageHeight();
        $width  = $this->GetPageWidth();
        
        $rut_width  = ($width  - 2*$this->margin) / 3;
        $rut_height = ($height - 2*$this->margin) / 7;
        
        # Header på sidan
        $this->SetXY($this->margin, 0);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,10,utf8_decode($titledeed->Name),0,1,'L');
        
        
        # Rita rutor
        $x_nr = 0;
        $y_nr = 0;
        $this->Line($this->margin, $this->margin+($rut_height), $width-$this->margin, $this->margin+($rut_height)); # Första Horisontell linje under
        
        foreach ($resources as $resource_titledeed) {
            
            if ($resource_titledeed->Quantity <= 0) continue;
            
            $resource = $resource_titledeed->getResource();
            
            for ($i = 0; $i < $resource_titledeed->Quantity; $i++){
                
                
//                 # Rubriken
//                 $this->SetFont('specialelite','',12);
//                 $this->SetXY( 3+$this->margin+($x_nr * $rut_width), $this->margin+($y_nr * $rut_height));
//                 $txt = ucfirst("Kvitto för");
//                 $this->Cell($rut_width,10,utf8_decode($txt),0,1,'L');
                
                # Resursnamnet

                $font = $this->handfonts[array_rand($this->handfonts, 1)];

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
                
                $this->SetXY($this->margin+($x_nr * $rut_width), ($rut_height/2)-2 + ($y_nr * $rut_height));
                $this->Cell($rut_width,10,utf8_decode(ucfirst($txt)),0,1,'C');
                
                # Undertext
//                 if ($titledeed->Tradeable && !$titledeed->IsTradingPost) {
//                     $this->SetFont('specialelite','',11);
//                     $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height-4) + ($y_nr * $rut_height));
//                     $this->Cell($rut_width,10,utf8_decode(ucfirst("Finns i marknadslagret")),0,1,'L');
//                     $this->SetFont('specialelite','',10);
//                     $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height+1) + ($y_nr * $rut_height));
//                     $this->Cell($rut_width,10,utf8_decode('för '.ucfirst($titledeed->Name)),0,1,'L');
//                 } else {
//                     $this->SetFont('specialelite','',12);
//                     $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height-3) + ($y_nr * $rut_height));
//                     $this->Cell($rut_width,10,utf8_decode(ucfirst("Finns i marknadslagret")),0,1,'L');
//                 }

                if ($titledeed->Tradeable && !$titledeed->IsTradingPost) {
                    if (strlen($titledeed->Name) > 28) {
                        $first_chars = substr($titledeed->Name, 0, 19);
                        $rest_max_30_chars = substr($titledeed->Name, 19, 30);
                        $this->SetFont('specialelite','',11);
                        $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height-4) + ($y_nr * $rut_height));
                        $this->Cell($rut_width,10,utf8_decode(ucfirst("Från $first_chars-")),0,1,'L');
                        $this->SetFont('specialelite','',10);
                        $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height+1) + ($y_nr * $rut_height));
                        $this->Cell($rut_width,10,utf8_decode($rest_max_30_chars),0,1,'L');
                        
                    } else {
                        $this->SetFont('specialelite','',11);
                        $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height-4) + ($y_nr * $rut_height));
                        $this->Cell($rut_width,10,utf8_decode(ucfirst("Från")),0,1,'L');
                        $this->SetFont('specialelite','',10);
                        $this->SetXY( 3+$this->margin+($x_nr * $rut_width), ($rut_height+1) + ($y_nr * $rut_height));
                        $this->Cell($rut_width,10,utf8_decode(ucfirst($titledeed->Name)),0,1,'L');
                    }
                }
                
                
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
    
	
    function all_resources(Array $titledeeds, LARP $larp)
	{
	    $campaign = $larp->getCampaign();
	    $this->SetAutoPageBreak(true , 1.5);
	    $this->AddFont('Smokum','');
	    $this->AddFont('specialelite','');
	    foreach ($this->handfonts as $font) $this->AddFont($font,'');
	    //         $this->AddPage('L','A5',270);
	    foreach ($titledeeds as $titledeed) {
    	    $this->SetText($titledeed, $campaign);
	    }
	}
}
