<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class our_fonts_pdf extends FPDF {
    
    public static function print_test() {
        $pdf = new our_fonts_pdf();
        $pdf->SetTitle('Font_test');
        $pdf->SetAuthor('Omnes Mundi');
        $pdf->SetCreator('Omnes Mundi');
        $pdf->SetSubject('Alla valbara fonter');
        $pdf->nytt_test();
        
        $pdf->Output();
    }
    
    function Header()
    {
        global $root;  
    }
    
    
    function nytt_test()
    {   
        $this->AddPage();
        foreach(OurFonts::fontsToLoad() as $font) {
            $this->AddFont($font,'');
        }
        foreach(OurFonts::fontArray() as $font) {
            $this->SetFont('Helvetica','',12);
            $this->Cell(40,10,encode_utf_to_iso("$font"),0,0);
            $this->SetFont($font,'',15);
            $this->Cell(80,10,encode_utf_to_iso("$font - Små bäckasiner häckar i vassen."),0,1);
            
        }
	}
}

