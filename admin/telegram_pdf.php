<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';

class TELEGRAM_PDF extends FPDF {
    
    function Header()
    {
        $this->Image('../images/telegram.png',null,null,200);
    }
    
    function SetText(string $sender, string $receiver, string $message, ?string $when) {
		$this->SetFont('SpecialElite','',14);    # OK är Times, Arial, Helvetica
		# För mer fonter använder du http://www.fpdf.org/makefont/
		$left = 21;
		if (!is_null($when)) {
		    $this->SetXY(150,60);
		    $this->Cell(80,10,$when,0,1);
		}
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
        $sender = $telegram->Sender.', '.$telegram->SenderCity;
        $reciever = $telegram->Reciever.', '.$telegram->RecieverCity;
        $this->AddPage();
        $deliverytime = $telegram->Deliverytime;
        if (is_string($deliverytime)) {
            $time = strtotime($deliverytime);
            $deliverytime = date('M d Y, g:i a',$time);
        }
        $this->SetText($sender, $reciever, $telegram->Message, $deliverytime);
	}
}


$arrayOfTelegrams = Telegram::all();
$pdf = new TELEGRAM_PDF();
$pdf->SetTitle('Telegram');
$pdf->SetAuthor('Dod mans hand');
$pdf->SetCreator('Mats Rappe');
$pdf->AddFont('SpecialElite','');
foreach ($arrayOfTelegrams as $telegram)  {
    $pdf->nytt_telegram($telegram);            
}
$doc = $pdf->Output('S');

$attachments = ['Telegrammen' => $doc];
DmhMailer::send('Mats.rappe@yahoo.se', 'Admin', "Det här är alla telegrammen", "Alla Telegrammen som PDF", $attachments);

$pdf->Output();
?>