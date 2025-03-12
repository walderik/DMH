<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class Letter_PDF extends FPDF {
    
    
    function SetText(string $whenwhere, string $greeting, string $endingPhrase, string $signature, string $message, string $font) {
        $this->SetFont($font,'',14);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
		# För mer fonter använder du http://www.fpdf.org/makefont/
		$left = 21;

		if ($this->GetStringWidth(encode_utf_to_iso($whenwhere)) > 60) {
		    $this->SetXY($this->GetPageWidth() - 20 - $this->GetStringWidth(encode_utf_to_iso($whenwhere)), 20);
		} else $this->SetXY(140, 20);
		# http://www.fpdf.org/en/doc/cell.htm
		# https://stackoverflow.com/questions/3514076/special-characters-in-fpdf-with-php
		$this->Cell(80,10,encode_utf_to_iso($whenwhere),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad	
        
		$this->SetXY($left, 60);
		$this->Cell(80,10,encode_utf_to_iso($greeting),0,1);
		$this->SetXY($left, 72);
		$this->MultiCell(0,8,encode_utf_to_iso($message),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är

		
		if ($this->GetStringWidth(encode_utf_to_iso($endingPhrase)) > 60) {
		    $ending_x = $this->GetPageWidth() - 20 - $this->GetStringWidth(encode_utf_to_iso($endingPhrase));
		} else $ending_x = 140;
		
		
		$this->SetXY($ending_x, $this->GetY()+30);
		$this->Cell(80,10,encode_utf_to_iso($endingPhrase),0,1);
		$this->SetX($ending_x);
		$this->Cell(80,10,encode_utf_to_iso($signature),0,1);
    }
    
    function nytt_brev(Letter $letter)
    {
        $this->AddFont($letter->Font,'');
        $this->AddPage();
        $this->SetMargins(10, 10, 20);

        $this->SetText($letter->WhenWhere, $letter->Greeting, $letter->EndingPhrase, $letter->Signature, $letter->Message, $letter->Font);
	}
}

