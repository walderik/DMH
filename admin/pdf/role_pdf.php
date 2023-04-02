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
    public static $text_max_length = 45;
    
    
    
    function Header()
    {
        global $root, $y;
        $this->SetLineWidth(1);
        $this->Line(static::$x_min, static::$y_min, static::$x_max, static::$y_min);
        $this->Line(static::$x_min, static::$y_min, static::$x_min, static::$y_max);
        $this->Line(static::$x_min, static::$y_max, static::$x_max, static::$y_max);
        $this->Line(static::$x_max, static::$y_min, static::$x_max, static::$y_max);
        
        $y = static::$y_min + static::$Margin;
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title()
    {
        global $x, $y, $left, $current_larp;

        $font_size = (850 / strlen(utf8_decode($current_larp->Name)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($current_larp->Name),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        $this->bar();
    }
    
    # Dra en linje tvärs över arket på höjd $y
    function bar()
    {
        global $y;
        $this->Line(static::$x_min, $y, static::$x_max, $y);
    }
    
    function mittlinje()
    {
        global $y, $cell_y_space, $mitten;
        $down = $y + $cell_y_space;
        $this->Line($mitten, $y, $mitten, $down);
    }
    
    # Gemensamt sätt beräkna var rubriken i ett fält ska ligga
    function set_header_start($venster)
    {
        global $y;
        $this->SetXY($venster, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
    }
    
    # Gemensamt sätt beräkna var texten i ett fält ska ligga
    function set_text_start($venster)
    {
        global $y;
        $this->SetXY($venster, $y + static::$Margin + 1);
        $this->SetFont('Helvetica','',static::$text_fontsize);
    }
    
    # Gemensam funktion för all logik för att skriva ut ett rubriken
    function set_header($venster, $text)
    {
        global $cell_width;
        $this->set_header_start($venster);
        $this->Cell($cell_width, static::$cell_y, utf8_decode($text),0,0,'L');
    }
    
    # Gemensam funktion för all logik för att skriva ut ett fält
    function set_text($venster, $text)
    {
        global $y, $cell_width;
        
        if (empty($text)) return;
        
        $text = utf8_decode($text);
        # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
        if (strlen($text)>static::$text_max_length){
            $this->SetXY($venster, $y + static::$Margin-1);
            $this->SetFont('Helvetica','',static::$text_fontsize/1.5);
            $this->MultiCell($cell_width, static::$cell_y-1.5, $text, 0,'L');
            return;
        }
        # Normal utskrift
        $this->set_text_start($venster);
        $this->Cell($cell_width, static::$cell_y, $text,0,0,'L');
    }
    
    function set_full_page($header, $text)
    {
        global  $y, $left, $cell_width;
        
        $text = utf8_decode($text);
        $this->set_header($left, $header);
        $this->SetXY($left, $y + static::$Margin+1);
        
        if (strlen($text)>4000){
            $this->SetFont('Helvetica','',static::$text_fontsize/2); # Hantering för riktigt långa texter
            $this->MultiCell(($cell_width*2)+(2*static::$Margin), static::$cell_y-1.5, $text, 0,'L');
            return;
        }
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $this->MultiCell(($cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
    }
    
    # Namnen på roll och spelare
    function names(Role $role) 
    {
        global $x, $y, $left, $left2, $cell_width, $cell_y_space, $mitten, $current_larp;
        
        $this->SetXY($left, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
        $type = ($role->isMain($current_larp)) ? 'Huvudkaraktär' : 'Sidokaraktär';
        
        if ($role->IsDead) {
            $type = 'Avliden';
            $this->Line(static::$x_min, $y+static::$Margin*1.5, $mitten, $y+static::$Margin*1.5);
        }
         
        $this->Cell($cell_width, static::$cell_y, utf8_decode($type),0,0,'L');
        
        $this->SetXY($left, $y + static::$Margin);
        $this->SetFont('Helvetica','B',24); # Extra stora bokstäver på karaktärens namn
        $this->Cell($cell_width, static::$cell_y, utf8_decode($role->Name),0,0,'L');
        
        $this->mittlinje();
        
        $person = $role->getPerson();
        
        $this->set_header($left2, 'Spelare');  
        $this->set_text($left2, $person->Name);
        
        $this->bar();
    }
    
    function yrke(Role $role) 
    {
        global $x, $y, $left, $left2, $cell_width, $cell_y_space, $mitten, $current_larp;
        $this->set_header($left,'Yrke');
        $this->set_text($left, $role->Profession);
    }
    
    function group(Role $role)
    {
        global $x, $y, $left, $left2, $cell_width, $cell_y_space, $mitten, $current_larp;
        $this->set_header($left2, 'Grupp');
        $group = $role->getGroup();
        if (empty($group)) return;
        $this->set_text($left2, $group->Name);
    }
    
    function beskrivning(Role $role)
    {
        $this->set_full_page('Beskrivning', $role->Description);
    }
    
    function new_character_cheet(Role $role)
    {
        global $x, $y, $left, $left2, $cell_width, $cell_y_space, $mitten, $current_larp;
        
//         $y = static::$y_min + static::$Margin;
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);
        $cell_y_space = static::$cell_y + (2*static::$Margin);
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->AddPage();
        
        $this->title();
        $this->names($role);
        
        $y += $cell_y_space;
        $this->bar();
        
        $this->yrke($role);
        $this->mittlinje();
        $this->group($role);
        
        $y += $cell_y_space;
        $this->bar();
        
        $this->AddPage();
        $this->beskrivning($role);
        
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