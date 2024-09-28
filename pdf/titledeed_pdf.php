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
    
    function SetTextDMH(Titledeed $titledeed, LARP $larp, bool $odd) {
        global $root;
        
        $left = 11;
        $page_height = $this->GetPageHeight();
        $y = $odd ? 0 : ($page_height/2);
        $left2 = $left + 30;
        
        $this->Image($root . '/images/agarbevis.jpeg', 0, $y, $this->GetPageWidth()-5);
        
        
        
        
        $txt_font = ($titledeed->Tradeable) ? 'SpecialElite' : 'Helvetica';
        
        if ($titledeed->Tradeable) {
            $this->SetFont('Smokum','',80);
            $y += 7;
        } else {
            $this->SetFont('Helvetica','',50);
        }
        
        $y += 13;
        $this->SetXY($left, $y);
        //$txt = $titledeed->Tradeable ? 'ÄGARBEVIS' : 'Ägarinformation';
        //$this->Cell(0,10,encode_utf_to_iso($txt),0,1,'C');
        if (!$titledeed->Tradeable) {
            $this->SetXY($left, $y);
            $y += 10;
            $this->SetXY($left, $y);
            $this->SetFont('Helvetica','',10); 
            $this->Cell(0,10,encode_utf_to_iso('(Kan inte säljas)'),0,1,'C');
            $y -= 10;
        }
        
        //$this->SetFont($txt_font,'',20);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
		# För mer fonter använder du http://www.fpdf.org/makefont/
        //if (strlen($titledeed->Name) > 40) $this->SetFont($txt_font,'',18);
        
        $this->SetFont($txt_font,'',12);
        
        $y += 25;
        $this->SetXY($left, $y);
		$this->Cell(80,10,encode_utf_to_iso('NAMN'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
		$this->SetXY($left2, $y);
		$this->Cell(80,10,strtoupper(encode_utf_to_iso($titledeed->Name)),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
		$y += 3;
        
		$this->SetFont($txt_font,'',12); 
		
        $texts = array();
        $txt = '';
		if (!empty($titledeed->Type)) $txt .= "$titledeed->Type ";
		if (!empty($titledeed->Size)) $txt .= "($titledeed->Size)";
		if (!empty($txt)) $texts[] = $txt;
		if (!empty($titledeed->Location)) $texts[] = "$titledeed->Location ";
		if (!empty($texts)) {
		    $y += 3;
		    $this->SetXY($left2, $y);
		    $this->Cell(80,10,encode_utf_to_iso(implode(", ",$texts)),0,1);
		}
		    
        if (!empty($titledeed->PublicNotes)) {
            $y += 7;
//             $this->SetXY($left, $y);
//             $this->Cell(80,10,encode_utf_to_iso('Beskrivning'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y);
            $this->Cell(80,10,encode_utf_to_iso($titledeed->PublicNotes),0,1);
            
        }
        
        $y += 14;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Ägare'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $owners = array();
        foreach ($titledeed->getGroupOwners() as $owner_group) $owners[] = $owner_group->Name;
        foreach ( $titledeed->getRoleOwners() as $owner_role)  $owners[] = $owner_role->Name;
        if (!empty($owners)) {
            $txt = join(', ', $owners);
        } else {
            $txt = 'Ingen';
        }
        $this->SetXY($left2, $y+1);
        $this->MultiCell(0,8,encode_utf_to_iso($txt),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        $y += 14;
 
        
        if (!empty($titledeed->Dividend)) {
            $this->SetXY($left, $y);
            $this->Cell(80,7,encode_utf_to_iso('Utdelning'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y);
            $this->MultiCell(0, 7, encode_utf_to_iso($titledeed->Dividend), 0, 'L');
            $y = $this->GetY();
        }
        
        

        $this->SetXY($left, $y);
        $this->Cell(80,7,encode_utf_to_iso('Tillgångar'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);

        $this->MultiCell(0, 7, encode_utf_to_iso($titledeed->ProducesString()), 0, 'L');
        $y = $this->GetY();
        
        //$y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,7,encode_utf_to_iso('Behöver'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->MultiCell(0, 7, encode_utf_to_iso($titledeed->RequiresString()), 0, 'L');
        $y = $this->GetY();
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,7,encode_utf_to_iso('Förbättring'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->MultiCell(0, 7, encode_utf_to_iso($titledeed->RequiresForUpgradeString()), 0, 'L');
        $y = $this->GetY();
        
        
        $size=5;
        $this->SetFont($txt_font,'',$size);
        
        $y_bottom = $odd ? ($page_height/2) : $page_height;

        $txt = $larp->Name;
        $slen = $this->GetStringWidth($txt,0);
        $this->SetXY(($this->GetPageWidth()-$slen)/2, $y_bottom-10);
        $this->Cell(($this->GetPageWidth()- $slen)/2,10,encode_utf_to_iso($txt),0,1,'L');

        
    }
    
    function SetTextDOH(Titledeed $titledeed, Campaign $campaign, bool $odd) {
        
        $page_height = $this->GetPageHeight();
        $y = $odd ? 0 : ($page_height/2);
        
        $page_width = $this->GetPageWidth();
        
        $left_1_1 = 10;
        $left_1_2 = $left_1_1 + 11;
        
        $left_2_1 = $page_width/2 + 11;
        $left_2_2 = $left_2_1 + 10;
        
        if ($odd) $this->Line(0, ($page_height/2), $this->GetPageWidth(), ($page_height/2));
        
        $font1 = 'KaiserzeitGotisch';
        $font2 = 'ComicRunes';

        
        
        $y += 13;
        $this->SetXY($left_1_1, $y);
        $size = 40;
        $this->SetFont($font1,'',$size);
        
        $txt = $titledeed->Name;
        $slen = $this->GetStringWidth($txt,0);
        while ($slen > ($page_width/2-15)) {
            $size -= 1;
            $this->SetFont($font1,'',$size);
            $slen = $this->GetStringWidth($txt,0);
        }
        
        $this->Cell(0,10,encode_utf_to_iso($txt),0,1);
        

        $this->SetXY($left_2_1, $y);
        $size = 40;
        $this->SetFont($font2,'',$size);
        
        $txt = $titledeed->Name;
        $slen = $this->GetStringWidth($txt,0);
        while ($slen > ($page_width/2-15)) {
            $size -= 1;
            $this->SetFont($font2,'',$size);
            $slen = $this->GetStringWidth($txt,0);
        }
        $this->Cell(0,10,encode_utf_to_iso($txt),0,1);
        
        
        $y += 14;
        $this->SetFont($font1,'',20);
        $this->SetXY($left_1_1, $y);
        $this->Cell(80,7,encode_utf_to_iso('Behöver'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad

        $this->SetXY($left_1_2, $y+7);
        $this->MultiCell($page_width/2-20, 7, encode_utf_to_iso($titledeed->RequiresString()), 0, 'L');
        $y1 = $this->GetY();

        $this->SetFont($font2,'',20);
        $this->SetXY($left_2_1, $y);
        $this->Cell(80,7,encode_utf_to_iso('Behöver'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left_2_2, $y+7);
        $this->MultiCell($page_width/2-20, 7, encode_utf_to_iso($titledeed->RequiresString()), 0, 'L');
        $y2 = $this->GetY();
        
        $y = max($y1, $y2);
        
        $y += 7;
        $this->SetFont($font1,'',20);
        $this->SetXY($left_1_1, $y);
        $this->Cell(80,7,encode_utf_to_iso('Utveckling'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left_1_2, $y+7);
        $this->MultiCell($page_width/2-20, 7, encode_utf_to_iso($titledeed->RequiresForUpgradeString()), 0, 'L');
        $y1 = $this->GetY();

        $this->SetFont($font2,'',20);
        $this->SetXY($left_2_1, $y);
        $this->Cell(80,7,encode_utf_to_iso('Utveckling'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left_2_2, $y+7);
        $this->MultiCell($page_width/2-20, 7, encode_utf_to_iso($titledeed->RequiresForUpgradeString()), 0, 'L');
        $y2 = $this->GetY();
        
        $y = max($y1, $y2);
    }
    
    
    
    function new_titledeed(Titledeed $titledeed, LARP $larp)
    {
        $campaigne = $larp->getCampaign();
        $this->AddFont('Smokum','');
        $this->AddFont('SpecialElite');
//         $this->AddPage('L','A5',270);
        $this->AddPage('L','A5',0);
//         $this->AddPage();
        $this->SetText($titledeed, $campaigne, true);
	}
	
	function all_titledeedsDMH(Array $titledeeds, LARP $larp)
	{
	    $this->AddFont('Smokum','');
	    $this->AddFont('SpecialElite');
	    $this->SetMargins(0, 0);
	    $this->SetAutoPageBreak(false);
	    
	    //         $this->AddPage('L','A5',270);
	    $odd = true;
	    foreach ($titledeeds as $titledeed) {
	        if ($odd) {
	            $this->AddPage();
	            $odd = false;
	        } else {
	            $odd = true;
	        }
	        $this->SetTextDMH($titledeed, $larp, !$odd);
	    }
	}

	function all_titledeedsDOH(Array $titledeeds, LARP $larp)
	{
	    $campaign = $larp->getCampaign();
	    $this->AddFont('KaiserzeitGotisch');
	    $this->AddFont('ComicRunes');
	    //         $this->AddPage('L','A5',270);
	    $odd = true;
	    foreach ($titledeeds as $titledeed) {
	        if ($odd) {
	            $this->AddPage();
	            $odd = false;
	        } else {
	            $odd = true;
	        }
	        $this->SetTextDOH($titledeed, $campaign, !$odd);
	    }
	}
	
}

// Money = 0;
// MoneyForUpgrade = 0;
// OrganizerNotes;
// SpecialUpgradeRequirements;