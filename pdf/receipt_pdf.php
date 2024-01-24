<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class Receipt_PDF extends FPDF {
    
    
    function Header()
    {
        global $root, $current_larp;
        $omlogo = $root . '/images/'.$current_larp->getCampaign()->Abbreviation.'_logo_vit.jpg';
        $this->Image($omlogo, 10, 10, -200);
    }
    
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
        $this->Cell(0, 10, utf8_decode('Genererad av Omnes Mundi för Berghems Vänner'), 0, 0, 'C');
    }
    
    function SetText(string $headline, $matter, $who, $specification, $amount, $date, $larp) {
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
        
        
        
        $y += 45;
        //$this->SetXY($left, $y);
        //$this->Cell(80,10,utf8_decode('Rubrik'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($matter),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $y += 3;
        
        $this->SetFont($txt_font,'',12);
                
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Betalare'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($who),0,1);
        
        if (!empty($specification)) {
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,utf8_decode('Specifikation'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y+1);
            $this->MultiCell(0,8,utf8_decode($specification),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
            $y = $this->GetY();
            
        }
        
       
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Summa'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($amount." kr"),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Datum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($date),0,1);
        
         $y += 28;
         $this->SetXY($left, $y);
         $this->Cell(80,10,utf8_decode('Kvittot skapat för '.$larp->Name),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
    }
    
    function nytt_kvitto(Bookkeeping $bookkeeping)
    {
        $this->AddPage();
        
        $this->SetText("Kvitto", $bookkeeping->Headline, $bookkeeping->Who, $bookkeeping->Text, $bookkeeping->Amount, $bookkeeping->Date, $bookkeeping->getLarp());
    }


    function nytt_kvitto_avgift(Registration $registration)
    {
        $this->AddPage();
        
        $person = $registration->getPerson();
        $larp = $registration->getLARP();
        $this->SetText("Kvitto", "Avgift för ".$larp->Name, $person->Name, "Avgift för deltagande på ".$larp->Name, $registration->AmountPayed, $registration->Payed, $larp);
    }

    function receipt_invoice(Invoice $invoice)
    {
        $this->AddPage();
        
        $larp = $invoice->getLARP();
        $this->SetText("Kvitto", "Faktura $invoice->Number", $invoice->Recipient, $invoice->Matter, $invoice->AmountPayed, $invoice->PayedDate, $larp);
    }
    
}

