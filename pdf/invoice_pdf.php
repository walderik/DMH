<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/lib/fpdf185/fpdf.php';
require_once $root . '/lib/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';

class Invoice_PDF extends FPDF {
    
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
    
    
    
    
    function SetText(Invoice $invoice) {
        $larp = $invoice->getLarp();
        $campaign = $larp->getCampaign();
        
        $left = 11;
        $page_height = $this->GetPageHeight();
        $y = 0;
        $left2 = $left + 30;
        $left_header = $left + 50;
        
        $txt_font = 'Helvetica';
        $this->SetFont($txt_font,'',40);
        
        $y += 13;
        $this->SetXY($left_header, $y);
        $this->Cell(0,10,encode_utf_to_iso("Faktura"),0,1);
        
        $this->SetFont($txt_font,'',12);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite

        $y += 13;
        $this->SetXY($left_header, $y);
        $this->Cell(0,10,encode_utf_to_iso("Fakturanummer ".$invoice->Number),0,1);
        
        
 
        $y += 30;
        $this->SetXY($left_header, $y);
        $this->Cell(80,10,encode_utf_to_iso('Mottagare'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $y += 7;
        $this->SetXY($left_header, $y);
        if (!empty($invoice->RecipientAddress)) {
            $this->MultiCell(0,8,encode_utf_to_iso($invoice->RecipientAddress),1,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        } else {
            $this->MultiCell(0,8,encode_utf_to_iso($invoice->Recipient),1,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        }
        $y = $this->GetY();
        
        
        
        
        $y += 20;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Fakturadatum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($invoice->getSentDate()),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Förfallodatum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($invoice->DueDate),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Betalas till'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($campaign->Bankaccount),0,1);
        $y += 7;
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso("Märk betalningen ".$invoice->PaymentReference),0,1);
        
        if ($invoice->hasContactPerson()) {
            $contactPerson = $invoice->getContactPerson();
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,encode_utf_to_iso('Er referens'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y);
            $this->Cell(80,10,encode_utf_to_iso($contactPerson->Name),0,1);
        }
        
        
        $y += 14;
        $this->SetXY($left, $y);
        //$this->Cell(80,10,encode_utf_to_iso($invoice->Matter),0,1);
        $this->MultiCell(0,8,encode_utf_to_iso($invoice->Matter."\n\nSumma ".$invoice->Amount()." kr"),1,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        $y = $this->GetY();    
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Fakturan skapad för '.$larp->Name),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
    }
    
    function ny_faktura(Invoice $invoice)
    {
        $this->AddPage();
        
        $this->SetText($invoice);
    }

    # Dra en linje tvärs över arket på höjd $y
    private function bar() {
        global $y;
        $this->Line(static::$x_min, $y, static::$x_max, $y);
    }
    

}
