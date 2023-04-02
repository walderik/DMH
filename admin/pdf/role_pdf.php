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
    public static $text_max_length = 52;
    
    
    
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
    function title($left)
    {
        global $y, $current_larp;

        $font_size = (850 / strlen(utf8_decode($current_larp->Name)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($current_larp->Name),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        $this->bar();
    }
    
    
    
    # Namnen på roll och spelare
    function names($left, $left2) 
    {
        global $y, $cell_width, $mitten, $current_larp, $role;
        
        $person = $role->getPerson();        
        $this->set_header($left, 'Spelare');
        $age = (empty($person)) ? '?' : $person->getAgeAtLarp($current_larp);
        $namn = "$person->Name ($age)";
        if (!empty($person)) $this->set_text($left, $namn);
        
        $this->mittlinje();
        
        $this->SetXY($left2, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
        $type = ($role->isMain($current_larp)) ? 'Huvudkaraktär' : 'Sidokaraktär';
        
        if ($role->IsDead) {
            $type = 'Avliden';
            $this->Line(static::$x_min, $y+static::$Margin*1.5, $mitten, $y+static::$Margin*1.5);
        }
         
        $this->Cell($cell_width, static::$cell_y, utf8_decode($type),0,0,'L');
        
        $this->SetXY($left2, $y + static::$Margin);
        $this->SetFont('Helvetica','B',24); # Extra stora bokstäver på karaktärens namn
       
        $this->Cell($cell_width, static::$cell_y, utf8_decode($role->Name),0,0,'L');
        
    }
    
    function yrke($left) 
    {
        global $role;
        $this->set_header($left,'Yrke');
        $this->set_text($left, $role->Profession);
    }
    
    function epost($left)
    {
        global $role;
        $this->set_header($left,'Epost');
        $person = $role->getPerson();
        if (empty($person)) return;
        $this->set_text($left, $person->Email);
    }
    
    function erfarenhet($left)
    {  
        global $role;
        if (!Experience::is_in_use()) return;
        $this->set_header($left,'Erfarenhet');
        $person = $role->getPerson();
        if (empty($person)) return;
        $this->set_text($left, $person->getExperience()->Name);
    }
    
    function rikedom($left)
    {
        global $role;
        if (!Wealth::is_in_use()) return;
        $this->set_header($left,'Rikedom');
        $this->set_text($left, Wealth::loadById($role->WealthId)->Name);
    }
    
    function lajvar_typ($left)
    {
        global $role;
        if (!LarperType::is_in_use()) return;
        
        $this->set_header($left,'Lajvartyp');
        $person = $role->getPerson();
        if (empty($person)) return;
        $this->set_text($left, $person->getLarperType()->Name);
    }
    
    function group($left)
    {
        global $role;
        $this->set_header($left, 'Grupp');
        $group = $role->getGroup();
        if (empty($group)) return;
        $this->set_text($left, $group->Name);
    }
    
    function beskrivning()
    {
        global $role;
        $this->set_full_page('Beskrivning', $role->Description);
    }
    
    function new_character_cheet(Role $role)
    {
        global $x, $y, $left, $left2, $cell_width, $cell_y_space, $mitten, $current_larp, $role;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);
        $cell_y_space = static::$cell_y + (2*static::$Margin);
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->AddPage();
        
        $this->title($left);
        $this->names($left, $left2);
        
        $y += $cell_y_space;
        $this->bar();
        
        $this->epost($left);
        $this->mittlinje();
        $this->yrke($left2);
        
        $y += $cell_y_space;
        $this->bar();
        
        $this->erfarenhet($left);
        $this->mittlinje();
        $this->rikedom($left2);
        
        $y += $cell_y_space;
        $this->bar();
        
        $this->lajvar_typ($left);
        $this->mittlinje();
        $this->group($left2);
        
        $y += $cell_y_space;
        $this->bar();
        
        $this->AddPage();
        $this->beskrivning();
        
	}
	
	
	
	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    global $y;
	    $this->Line(static::$x_min, $y, static::$x_max, $y);
	}
	
	private function mittlinje() {
	    global $y, $cell_y_space, $mitten;
	    $down = $y + $cell_y_space;
	    $this->Line($mitten, $y, $mitten, $down);
	}
	
	# Gemensamt sätt beräkna var rubriken i ett fält ska ligga
	private function set_header_start($venster) {
	    global $y;
	    $this->SetXY($venster, $y);
	    $this->SetFont('Helvetica','',static::$header_fontsize);
	}
	
	# Gemensamt sätt beräkna var texten i ett fält ska ligga
	private function set_text_start($venster) {
	    global $y;
	    $this->SetXY($venster, $y + static::$Margin + 1);
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	}
	
	# Gemensam funktion för all logik för att skriva ut ett rubriken
	private function set_header($venster, $text) {
	    global $cell_width;
	    $this->set_header_start($venster);
	    $this->Cell($cell_width, static::$cell_y, utf8_decode($text),0,0,'L');
	}
	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_text($venster, $text) {
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
	
	private function set_full_page($header, $text) {
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