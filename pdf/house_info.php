<?php

// Include the main TCPDF library (search for installation path).


global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/lib/tcpdf/tcpdf.php';

// extend TCPF with custom functions
class HouseInfo extends Tcpdf {
    public $text_fontsize;
    
    // Page footer
    public function Footer() {
    }
    
    
    public function init($author, $housname, $larpname, $isSensitive) {
        // set document information
        $this->SetCreator('Omnes Mundi');
        $title = "Information om $housname från husförvaltarna";
        $this->SetAuthor($author);
        $this->SetTitle($title);
        $this->SetSubject($title);
        $this->SetKeywords($title);
        
         // set default header data
        $this->SetHeaderData("", 0, $title, $larpname);
        
        // set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $this->SetAutoPageBreak(TRUE);
        
        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        
        // ---------------------------------------------------------
        
        // set font
        $this->SetFont('helvetica', '', 12);
        
    }
    
    // Colored table
    public function printInfo($housname, String $data) {
        // set font
        $headline = "Information om $housname från husförvaltarna";
        
        $this->SetFont('helvetica', 'B', 20);
        
        $this->Write(0, $headline, '', 0, 'C', true, 0, false, false, 0);
        
        $this->SetFont('helvetica', '', 10);
        
        $this->MultiCell($this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT, 5, $data, 0, 'L', 0, 0, '', '', true);
    }
    
}

