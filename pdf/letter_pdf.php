<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class Letter_PDF extends FPDF {
    
    
    function SetText(string $whenwhere, string $greeting, string $endingPhrase, string $signature, string $message, string $font) {
        $this->SetFont($font,'',14);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
		# För mer fonter använder du http://www.fpdf.org/makefont/
		$left = 21;

		$this->SetXY(140, 20);
		# http://www.fpdf.org/en/doc/cell.htm
		# https://stackoverflow.com/questions/3514076/special-characters-in-fpdf-with-php
		$this->Cell(80,10,utf8_decode($whenwhere),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad	
        
		$this->SetXY($left, 60);
		$this->Cell(80,10,utf8_decode($greeting),0,1);
		$this->SetXY($left, 72);
		$this->MultiCell(0,8,utf8_decode($message),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
		$this->SetXY(70, 140);
		$this->Cell(80,10,utf8_decode($endingPhrase),0,1);
		$this->SetXY(70, 150);
		$this->Cell(80,10,utf8_decode($signature),0,1);
    }
    
    function nytt_brev(Letter $letter)
    {
        $this->AddFont($letter->Font,'');
        $this->AddPage();

        $this->SetText($letter->WhenWhere, $letter->Greeting, $letter->EndingPhrase, $letter->Signature, $letter->Message, $letter->Font);
	}
}

