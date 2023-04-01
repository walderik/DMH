<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class ROLE_PDF extends FPDF {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    
    
    
    function Header()
    {
        global $root;
//         $this->Image($root . '/images/telegram.png',null,null,200);
        $this->SetLineWidth(1);
//         $this->Line(float x1, float y1, float x2, float y2)

        $this->Line(static::$x_min, static::$y_min, static::$x_max, static::$y_min);
        $this->Line(static::$x_min, static::$y_min, static::$x_min, static::$y_max);
        $this->Line(static::$x_min, static::$y_max, static::$x_max, static::$y_max);
        $this->Line(static::$x_max, static::$y_min, static::$x_max, static::$y_max);
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title()
    {
        global $x, $y, $left, $current_larp;

        $font_size = (strlen(utf8_decode($current_larp->Name)) * (26.5 / 9));
        $this->SetFont('Helvetica','', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($current_larp->Name),0,0,'L');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        $this->bar();
    }
    
    # Dra en linje tvärs över arket på höjd $y
    function bar()
    {
        global $y;
        $this->Line(static::$x_min, $y, static::$x_max, $y);
    }
    
    function name(Role $role) 
    {
        global $x, $y, $left, $left2, $cell_width, $cell_y_space, $mitten, $current_larp;
        
        $down = $y + $cell_y_space;
        $this->Line($mitten, $y, $mitten, $down);
        
        $this->SetXY($left, $y + static::$Margin);
        $this->SetFont('Helvetica','',24);
        $this->Cell($cell_width, static::$cell_y, utf8_decode($role->Name),0,0,'L');
        
        $person = $role->getPerson();
        
        $this->SetXY($left2, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
        $this->Cell($cell_width, static::$cell_y, utf8_decode('Spelare'),0,0,'L');
        
        $this->SetXY($left2, $y + static::$Margin);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $this->Cell($cell_width, static::$cell_y, utf8_decode($person->Name),0,0,'L');
        
        
        $y = $down;
        
        $this->bar();
    }
    
    function new_character_cheet(Role $role)
    {
        global $x, $y, $left, $left2, $cell_width, $cell_y_space, $mitten, $current_larp;
        
        $y = static::$y_min + static::$Margin;
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $cell_width = (static::$x_max - static::$x_min) / 2 ;
        $cell_y_space = static::$cell_y + (2*static::$Margin);
        $mitten = $cell_width + static::$x_min;
        $left2 = $mitten + static::$Margin;
        
        $this->AddPage();
        
        $this->title();
        $this->name($role);

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