<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class RESOURCE_PDF extends FPDF {
    
    
    public $margin = 10;
    public $title;
    
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
        
        $left = 11;
        
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
        $this->SetFont('Arial','B',14);
        $x_nr = 0;
        $y_nr = 0;
        $this->Line($this->margin, $this->margin+($rut_height), $width-$this->margin, $this->margin+($rut_height)); # Första Horisontell linje under
        
        foreach ($resources as $resource_titledeed) {
            
            if ($resource_titledeed->Quantity <= 0) continue;
            
            $resource = $resource_titledeed->getResource();
            
            for ($i = 0; $i < $resource_titledeed->Quantity; $i++){
                
                $this->SetXY(11 + ($x_nr * $rut_width), 15 + ($y_nr * $rut_height));
                $this->Cell(0,10,utf8_decode("$resource->Name"),0,1,'L');
                
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
	    $this->AddFont('Smokum','');
	    //         $this->AddPage('L','A5',270);
	    foreach ($titledeeds as $titledeed) {
    	    $this->SetText($titledeed, $campaign);
	    }
	}
}
