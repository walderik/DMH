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
        $pdf->SetAuthor('Omnes Mundos');
        $pdf->SetCreator('Omnes Mundos');
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
            $this->SetFont('Helvetica','',14);
            $this->Cell(50,10,utf8_decode("$font"),0,0);
            $this->SetFont($font,'',14);
            $this->Cell(80,10,utf8_decode("$font - Små bäckasiner häckar i vassen."),0,1);
            
        }
	}
}

