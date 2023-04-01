<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class ROLE_PDF extends FPDF {
    
    public static $MarginLeft = 21;
    
    function Header()
    {
        global $root;
//         $this->Image($root . '/images/telegram.png',null,null,200);
        $this->SetLineWidth(2);
//         $this->Line(float x1, float y1, float x2, float y2)

        $x_max = 210;
        $y_max = 296;
        
        $this->Line(0, 0, $x_max, 0);
        $this->Line(0, 0, 0, $y_max);
        $this->Line(0, $y_max, $x_max, $y_max);
        $this->Line($x_max, 0, $x_max, $y_max);
    }
    
    function SetText(Role $role) {
		$this->SetFont('Helvetica','',14);    # OK är Times, Arial, Helvetica
		# För mer fonter använder du http://www.fpdf.org/makefont/
		$left = static::$MarginLeft;
		
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
    
    function new_character_cheet(Role $role)
    {
        $this->SetFont('Helvetica','',24);    # OK är Times, Arial, Helvetica
        $this->AddPage();
        
        $this->SetXY(static::$MarginLeft, 68);
        $this->Cell(80,10,utf8_decode($role->Name),0,1);
        
//         $this->SetText($sender, $reciever, $telegram->Message, $deliverytime);
	}
}

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "GET" || empty($_GET['id'])) {
    header('Location: ../../admin/index.php');
    exit;
}

$roleId = $_GET['id'];
$role = Role::loadById($roleId);
if (empty($role)) {
    header('Location: index.php'); //Rollen finns inte
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); //Rollen är inte anmäld
    exit;
}


$pdf = new ROLE_PDF();
$pdf->SetTitle(utf8_decode('Karaktärsblad'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($role->Name));
$pdf->new_character_cheet($role);

// $attachments = ['Telegrammen' => $doc];
//BerghemMailer::send('Mats.rappe@yahoo.se', 'Admin', "Det här är alla telegrammen", "Alla Telegrammen som PDF", $attachments);

$pdf->Output();