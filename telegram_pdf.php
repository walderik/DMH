<?php
# Läs mer på http://www.fpdf.org/

require('includes/fpdf185/fpdf.php');
# $this->MultiCell(0,5,$txt);
include 'telegram.php';

class TELEGRAM_PDF extends FPDF {
    
    function Header()
    {
        $this->Image('telegram.png',null,null,200);
    }
    
    function SetText($sender, $receiver, $message) {
		$this->SetFont('SpecialElite','',14);    # OK är Times, Arial, Helvetica
		# För mer fonter använder du http://www.fpdf.org/makefont/
		$left = 21;
		$this->SetXY($left, 68);
		# http://www.fpdf.org/en/doc/cell.htm
		# https://stackoverflow.com/questions/3514076/special-characters-in-fpdf-with-php
        $this->Cell(80,10,utf8_decode($sender),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad	
		$this->SetXY($left, 88);
		$this->Cell(80,10,utf8_decode($receiver),0,1);
		$this->SetXY($left, 112);
		$this->MultiCell(0,8,utf8_decode($message),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
    }
    
    function nytt_telegram($telegram)
    {
        $sender = $telegram->sender.', '.$telegram->sendercity;
        $eceiver = $telegram->receiver.', '.$telegram->receivercity;
		$this->AddPage();
		$this->SetText($sender, $receiver, $telegram->message);
	}
}

class TELEGRAM_PDFS
{
    public function __construct($arrayOfTelegrams)
    {
        $pdf = new TELEGRAM_PDF();
        $pdf->SetTitle('Telegram');
        $pdf->SetAuthor('Dod mans hand');
        $pdf->SetCreator('Mats Rappe');
        $pdf->AddFont('SpecialElite','');
        foreach ($arrayOfTelegrams as $telegram)  {
            $pdf->nytt_telegram($telegram);
        }
        $pdf->Output();
    }
}

?>