<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/lib/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class Receipt_PDF extends FPDF {
    
    public static $x_min = 5;
    public static $x_max = 291;
    
    function Header()
    {
        global $root, $current_larp;
        $omlogo = $root . 'images/'.$this->remove_swedish_chars($current_larp->getCampaign()->Abbreviation).'_logo_vit.jpg';
        $this->Image($omlogo, 10, 10, -200);
    }
    
    function Footer()
    {
        global $y;
        // Go to 1.5 cm from bottom
        //$this->SetY(-50);
        $this->setXY(11, -30);
        $y = $this->GetY();
        $this->bar();
        
        // Select Arial italic 8
        $this->SetFont('Helvetica', '', 10);
        
        $this->setXY(11, -25);
        $this->MultiCell(60,4,encode_utf_to_iso("Berghems Vänner\nc/o Martin Gabrielsson\nTrädgårdsgatan 17\n567 93 Hok"),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        $this->setXY(80, -25);
        $this->MultiCell(40,4,encode_utf_to_iso("Organisationsnummer\n802488-4846"),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        $this->setXY(140, -25);
        $this->MultiCell(60,4,encode_utf_to_iso("Webbplats\nmain.berghemsvanner.se\nEpost\ninfo@berghemsvanner.se"),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        // Print centered page number
        //$this->Cell(0, 10, encode_utf_to_iso('Genererad av Omnes Mundi för Berghems Vänner'), 0, 0, 'C');
    }
    
    function remove_swedish_chars($text) {
        $tmp = str_replace("Ö","O",$text);
        $tmp = str_replace("ö","o",$tmp);
        $tmp = str_replace("Ä","A",$tmp);
        $tmp = str_replace("ä","a",$tmp);
        $tmp = str_replace("Å","A",$tmp);
        $tmp = str_replace("å","a",$tmp);
        return $tmp;
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
        $this->Cell(0,10,encode_utf_to_iso($headline),0,1,'C');
        
        $this->SetFont($txt_font,'',20);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
        
        
        
        $y += 45;
        //$this->SetXY($left, $y);
        //$this->Cell(80,10,encode_utf_to_iso('Rubrik'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($matter),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $y += 3;
        
        $this->SetFont($txt_font,'',12);
                
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Betalare'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($who),0,1);
        
        if (!empty($specification)) {
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,encode_utf_to_iso('Specifikation'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y+1);
            $this->MultiCell(0,8,encode_utf_to_iso($specification),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
            $y = $this->GetY();
            
        }
        
       
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Summa'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($amount." kr"),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Datum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($date),0,1);
        
         $y += 28;
         $this->SetXY($left, $y);
         $this->Cell(80,10,encode_utf_to_iso('Kvittot skapat för '.$larp->Name),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
    }
    
    function nytt_kvitto(Bookkeeping $bookkeeping)
    {
        $this->AddPage();
        
        $this->SetText("Kvitto", $bookkeeping->Headline, $bookkeeping->Who, $bookkeeping->Text, abs($bookkeeping->Amount), $bookkeeping->AccountingDate, $bookkeeping->getLarp());
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
    
    # Dra en linje tvärs över arket på höjd $y
    private function bar() {
        global $y;
        $this->Line(static::$x_min, $y, static::$x_max, $y);
    }
    
    
}

