<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class TITLEDEED_PDF extends FPDF {
    
    
    function Header()
    {
        global $root;
//         $this->Image($root . '/images/telegram.png',null,null,200);
    }
    
    function SetText(string $name, string $receiver) {
        
        $left = 11;
        
        $this->SetFont('DancingScript','',45);
        $this->SetXY($left, 15);
        $this->Cell(0,10,utf8_decode('Ägarbevis'),0,1,'C');
        
        $this->SetFont('SpecialElite','',20);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
		# För mer fonter använder du http://www.fpdf.org/makefont/

		$this->SetXY($left, 35);
		$this->Cell(80,10,utf8_decode('Namn'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
		$this->SetXY($left + 30, 35);
		$this->Cell(80,10,utf8_decode($name),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad	
        
		$this->SetFont('SpecialElite','',12); 
		
		$this->SetXY($left, 88);
		$this->Cell(80,10,utf8_decode($receiver),0,1);
// 		$this->SetXY($left, 112);
// 		$this->MultiCell(0,8,utf8_decode($message),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
    }
    
    function new_titledeed(Titledeed $titledeed)
    {
        $this->AddFont('DancingScript','');
        $name = $titledeed->Name;
        $location = $titledeed->Location;
//         $this->AddPage('L','A5',270);
        $this->AddPage('L','A5',0);
//         $this->AddPage();
        $this->SetText($name, $location);
	}
}

// public $Id;
// public $Name;
// public $Location;
// public $Tradeable = 1;
// public $IsTradingPost = 0;
// public $CampaignId;
// public $Money = 0;
// public $MoneyForUpgrade = 0;
// public $OrganizerNotes;
// public $PublicNotes;
// public $SpecialUpgradeRequirements;