<?php

// Include the main TCPDF library (search for installation path).


global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/tcpdf/tcpdf.php';

// extend TCPF with custom functions
class Report_TCP_PDF extends Tcpdf {
    public static $debug = false;
    
    
    private $isSensitive;
     
    // Page footer
    public function Footer() {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Sidan '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        
        # Om det är ett dokument med känsliga uppgifter
        if ($this->isSensitive) {
            // Go to 1.5 cm from bottom
            $this->SetY(-15);
            $this->Cell(0, 10, 'Det här dokumentet innehåller känsliga personuppgifter. Förstör det efter lajvet.', 0, 0, 'L');
        }
    }
    
    
    public function init($author, $title, $larpname, $isSensitive) {
        // set document information
        $this->SetCreator('Omnes Mundi');
        $this->SetAuthor($author);
        $this->SetTitle($title);
        $this->SetSubject($title);
        $this->SetKeywords($title);
        
        $this->isSensitive = $isSensitive;
        if ($isSensitive) {
            $this->SetProtection(array('modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'));
        }
        
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
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        
        // ---------------------------------------------------------
        
        // set font
        $this->SetFont('helvetica', '', 12);
        
    }
    
    
    // Colored table
    public function Table($headline, $header,$data) {

        // set font
        $this->SetFont('helvetica', 'B', 30);
        
        $this->Write(0, $headline, '', 0, 'C', true, 0, false, false, 0);
        
        $this->SetFont('helvetica', '', 10);
        
        
        $html = '<table style="width: 95%;" cellpadding="3" cellspacing="0">';
        $html = $html . '<thead><tr bgcolor="#CCCCCC">';
        
        foreach($header as $headertext) $html .= '<td  style="font-weight: bold; align: center;">'.$headertext.'</td>';
        $html .= '</tr></thead>';
        foreach($data as $row){
            $html .= '<tr nobr="true">';
            foreach ($row as $item) $html .= '<td style="border: 1px solid #000000;">'.$item.'</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        
        // output the HTML content
        $this->writeHTML($html, true, false, false, false, '');
        
    }
    
    
}

