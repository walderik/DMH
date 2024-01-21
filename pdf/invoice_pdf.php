<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class Invoice_PDF extends FPDF {
    
    
    function Header()
    {
        global $root, $current_larp;
         $omlogo = $root . '/images/'.$current_larp->getCampaign()->Abbreviation.'_logo.jpg';
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
        $this->Cell(0,10,utf8_decode("Faktura"),0,1);
        
        $this->SetFont($txt_font,'',12);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite

        $y += 13;
        $this->SetXY($left_header, $y);
        $this->Cell(0,10,utf8_decode("Fakturanummer ".$invoice->Number),0,1);
        
        
 
        $y += 30;
        $this->SetXY($left_header, $y);
        $this->Cell(80,10,utf8_decode('Mottagare'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $y += 7;
        $this->SetXY($left_header, $y);
        $this->MultiCell(0,8,utf8_decode($invoice->Name),1,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        $y = $this->GetY();
        
        
        
        
        $y += 20;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Fakturadatum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($invoice->getSentDate()),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Förfallodatum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($invoice->DueDate),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Betalas till'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode($campaign->Bankaccount),0,1);
        $y += 7;
        $this->SetXY($left2, $y);
        $this->Cell(80,10,utf8_decode("Märk betalningen ".$invoice->PaymentReference),0,1);
        
        if ($invoice->hasContactPerson()) {
            $contactPerson = $invoice->getContactPerson();
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,utf8_decode('Er referens'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y);
            $this->Cell(80,10,utf8_decode($contactPerson->Name),0,1);
        }
        
        
        $y += 14;
        $this->SetXY($left, $y);
        //$this->Cell(80,10,utf8_decode($invoice->Description),0,1);
        $this->MultiCell(0,8,utf8_decode($invoice->Description."\n\nSumma ".$invoice->Amount()." kr"),1,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        $y = $this->GetY();    
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,utf8_decode('Fakturan skapad för '.$larp->Name),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
    }
    
    function ny_faktura(Invoice $invoice)
    {
        $this->AddPage();
        
        $this->SetText($invoice);
    }


}
