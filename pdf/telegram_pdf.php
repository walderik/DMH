<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class TELEGRAM_PDF extends FPDF {
    
    
    function Header()
    {
        global $root;
        $this->Image($root . '/images/telegram.png',null,null,200);
    }
    
    function SetText(string $sender, string $receiver, string $message, ?string $when) {
        $this->SetFont('SpecialElite','',14);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
		# För mer fonter använder du http://www.fpdf.org/makefont/
		$left = 21;
		if (!is_null($when)) {
		    $this->SetXY(150,60);
		    $this->Cell(80,10,$when,0,1);
		}
		$this->SetXY($left, 68);
		# http://www.fpdf.org/en/doc/cell.htm
		# https://stackoverflow.com/questions/3514076/special-characters-in-fpdf-with-php
        $this->Cell(80,10,encode_utf_to_iso($sender),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad	
        
		$this->SetXY($left, 88);
		$this->Cell(80,10,encode_utf_to_iso($receiver),0,1);
		$this->SetXY($left, 112);
		$this->MultiCell(0,8,encode_utf_to_iso($message),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
    }
    
    function nytt_telegram(Telegram $telegram)
    {
        $sender = $telegram->Sender.', '.$telegram->SenderCity;
        $reciever = $telegram->Reciever.', '.$telegram->RecieverCity;
//         $this->AddPage('L','A5');
        $this->AddPage();
        $deliverytime = $telegram->Deliverytime;
        if (is_string($deliverytime)) {
            $time = strtotime($deliverytime);
            $deliverytime = date('M d Y, g:i a',$time);
        }
        $this->SetText($sender, $reciever, $telegram->Message, $deliverytime);
	}
}

