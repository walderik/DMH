<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class Receipt_PDF extends FPDF {
    
    
    function Header()
    {
        global $root;
    }
    
    function SetText(string $headline, Bookkeeping $bookkeeping) {
        $larp = LARP::loadById($bookkeeping->LarpId);
        $left = 11;
        $page_height = $this->GetPageHeight();
        $y = 0;
        $left2 = $left + 30;
                
        $txt_font = 'Helvetica';
        $this->SetFont($txt_font,'',50);
        
        $y += 13;
        $this->SetXY($left, $y);
        $this->Cell(0,10,utf8_decode($headline),0,1,'C');
        
        $this->SetFont($txt_font,'',20);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
        
        
        
        $y += 25;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Rubrik'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($bookkeeping->Headline),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $y += 3;
        
        $this->SetFont($txt_font,'',12);
                
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Betalare'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($bookkeeping->Who),0,1);
        
        if (!empty($bookkeeping->Text)) {
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,utf8_decode('Specifikation'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y);
            $this->Cell(80,10,utf8_decode($bookkeeping->Text),0,1);
            
        }
        
       
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Summa'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($bookkeeping->Amount),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Datum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($bookkeeping->Date),0,1);
        
        $y += 14;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Attesteras'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad

        $y += 28;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('För '.$larp->Name),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
    }
    
    function nytt_kvitto(Bookkeeping $bookkeeping)
    {
        $this->AddPage('L','A5',0);
        
        $this->SetText("Kvitto", $bookkeeping);
    }
}

