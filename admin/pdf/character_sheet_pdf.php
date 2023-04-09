<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class CharacterSheet_PDF extends FPDF {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $role;
    public $person;
    public $isMyslajvare;
    public $larp;
    public $all;
    public $current_left;
    public $current_cell_height;
    public $cell_y_space;
    
    function Header() {
        global $root, $y, $mitten;
        $this->SetLineWidth(0.6);
        $this->Line(static::$x_min, static::$y_min, static::$x_max, static::$y_min);
        $this->Line(static::$x_min, static::$y_min, static::$x_min, static::$y_max);
        $this->Line(static::$x_min, static::$y_max, static::$x_max, static::$y_max);
        $this->Line(static::$x_max, static::$y_min, static::$x_max, static::$y_max);
        
        $space = 1.2;
        $this->Line(static::$x_min-$space, static::$y_min-$space, static::$x_max+$space, static::$y_min-$space);
        $this->Line(static::$x_min-$space, static::$y_min-$space, static::$x_min-$space, static::$y_max+$space);
        $this->Line(static::$x_min-$space, static::$y_max+$space, static::$x_max+$space, static::$y_max+$space);
        $this->Line(static::$x_max+$space, static::$y_min-$space, static::$x_max+$space, static::$y_max+$space);
        
        
        $this->SetXY($mitten-15, 3);
        $this->SetFont('Helvetica','',static::$text_fontsize/1.1);
        $this->SetFillColor(255,255,255);
        if ($this->all) {
            $txt = $this->role->Name;
        } else {
            $txt = 'Karaktärsblad';
        }
        $this->MultiCell(30, 4, utf8_decode($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title($left) {
        global $y;

        $font_size = (850 / strlen(utf8_decode($this->larp->Name)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($this->larp->Name),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        $this->bar();
    }
    
    # Namnen på roll och spelare
    function names($left, $left2) {
        global $y, $cell_width, $mitten;
        
        $persn = $this->person;        
        $this->set_header($left, 'Spelare');
        $age = (empty($this->person)) ? '?' : $persn->getAgeAtLarp($this->larp);
        $namn = "$persn->Name ($age)";
        if (!empty($persn)) $this->set_text($left, $namn);
        
        $this->mittlinje();
        
        $this->SetXY($left2, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
        $type = ($this->role->isMain($this->larp)) ? 'Huvudkaraktär' : 'Sidokaraktär';
        
        if ($this->role->IsDead) {
            $type = 'Avliden';
            $this->cross_over();
        }
         
        $this->Cell($cell_width, static::$cell_y, utf8_decode($type),0,0,'L');
        
        $this->SetXY($left2, $y + static::$Margin);
        $this->SetFont('Helvetica','B',24); # Extra stora bokstäver på karaktärens namn
       
        $this->Cell($cell_width, static::$cell_y, utf8_decode($this->role->Name),0,0,'L');
    }

    
    function beskrivning() {
        global $y;
        $text = $this->role->Description; #.' '.strlen($role->Description);
        if (($y > (static::$y_max/2)-static::$Margin) || (strlen($text)>2600)) {
            $this->set_full_page('Beskrivning', $text);
        } else {
            $this->set_rest_of_page('Beskrivning', $text);
        }
        return true;
    }
    
    function new_character_sheet(Role $role_in, LARP $larp_in, bool $all_in=false) {
        global $x, $y, $left, $left2, $cell_width, $mitten;
        
        $this->role = $role_in;
        $this->person = $this->role->getPerson();
        $this->isMyslajvare = $this->person->isMysLajvare();
        $this->larp = $larp_in;
        $this->all = $all_in;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $left;
        
        $this->AddPage();
        
        $this->title($left);
        $this->names($left, $left2);
        
        $y += $this->cell_y_space;
        $this->current_cell_height = $this->cell_y_space;
        $this->bar();
        
        # Uppräkning av ett antal fält som kan finnas eller inte
        $this->draw_field('epost');
        $this->draw_field('group');
        $this->draw_field('erfarenhet');
        $this->draw_field('yrke');
        $this->draw_field('lajvar_typ');
        $this->draw_field('rikedom');
        if ($this->all) $this->draw_field('intrigtyper');
        $this->draw_field('birth_place');
        if ($this->all) $this->draw_field('intrigsuggestions');
        $this->draw_field('bor');
        if ($this->all) $this->draw_field('notOkIntrigues');
        $this->draw_field('reason_for_being_in_here');
        $this->draw_field('religion');
        if ($this->all) $this->draw_field('darkSecret');
        
        # Fixa till om vi skapat ett udda antal fält
        if ($this->current_left == $left2) $this->draw_field('empty');
        
//         $this->beskrivning();
	}
	
	function all_character_sheets(LARP $larp_in ) {
	    $this->larp = $larp_in;
	    $roles = $this->larp->getAllRoles();
	    foreach($roles as $role) {
 	        $this->new_character_sheet($role, $larp_in, true);
	    }
	}
	
	# Dynamiska småfält
	
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function yrke($left) {
	    $this->set_header($left, 'Yrke');
	    $this->set_text($left, $this->role->Profession);
	    return true;
	}
	
	protected function epost($left) {
	    $this->set_header($left, 'Epost');
	    if (empty($this->person)) return true;
	    $this->set_text($left, $this->person->Email);
	    return true;
	}
	
	protected function erfarenhet($left) {
	    if (!Experience::isInUse($this->larp)) return false;
	    
	    $this->set_header($left, 'Erfarenhet');
	    if (empty($this->person)) return true;
	    $this->set_text($left, $this->person->getExperience()->Name);
	    return true;
	}
	
	protected function rikedom($left) {
	    if (!Wealth::isInUse($this->larp)) return false;
	    
	    if ($this->isMyslajvare) $this->cross_over();
	    
	    $this->set_header($left, 'Rikedom');
	    $text = ($this->role->is_trading($this->larp)) ? " (Handel)" : " (Ingen handel)";
	    $this->set_text($left, $this->role->getWealth()->Name . $text);
	    return true;
	}
	
	protected function intrigtyper($left) {
	    if (!IntrigueType::isInUse($this->larp)) return false;
	    
	    $this->set_header($left, 'Intrigtyper');
	    if ($this->isMyslajvare) return true;
	    $larp_role = LARP_Role::loadByIds($this->role->Id, $this->larp->Id);
	    $text = commaStringFromArrayObject($larp_role->getIntrigueTypes());
	    $this->set_text($left, $text);
	    return true;
	}
	
	protected function intrigsuggestions($left) {  
	    $this->set_header($left, 'Intrigförslag');
	    if ($this->isMyslajvare) return true;
	    $this->set_text($left, $this->role->IntrigueSuggestions);
	    return true;
	}
	
	protected function notOkIntrigues($left) {
	    $this->set_header($left, 'Inget spel på');
	    if ($this->isMyslajvare) return true;
	    $this->set_text($left, $this->role->NotAcceptableIntrigues);
	    return true;
	}
	
	protected function darkSecret($left) {
	    $this->set_header($left, 'Mörk hemlighet');
	    if ($this->isMyslajvare) return true;
	    $this->set_text($left, $this->role->DarkSecret);
	    return true;
	}
	
	protected function lajvar_typ($left) {
	    if (!LarperType::isInUse($this->larp)) return false;
	    
	    $this->set_header($left, 'Lajvartyp');
	    if (empty($this->person)) return true;
	    $mertext = (empty(trim($this->person->TypeOfLarperComment))) ? '' : " (".trim($this->person->TypeOfLarperComment).")";
	    $text = $this->person->getLarperType()->Name.$mertext;
	    $this->set_text($left, $text );
	    return true;
	}
	
	protected function group($left) {
	    $this->set_header($left, 'Grupp');
	    $group = $this->role->getGroup();
	    if (empty($group)) return true;
	    $this->set_text($left, $group->Name);
	    return true;
	}
	
	protected function birth_place($left) {
	    $this->set_header($left, 'Född');
	    $this->set_text($left, $this->role->Birthplace);
	    return true;
	}
	
	protected function bor($left) {
	    $this->set_header($left, 'Bor');
	    $this->set_text($left, $this->role->getPlaceOfResidence()->Name);
	    return true;
	}
	
	protected function religion($left) {
	    $this->set_header($left, 'Religion');
	    $this->set_text($left, $this->role->Religion);
	    return true;
	}
	
	protected function reason_for_being_in_here($left) {
	    $this->set_header($left, 'Orsak för att vistas här');
	    $this->set_text($left, $this->role->ReasonForBeingInSlowRiver);
	    return true;
	}
	
	
	# Rita en ruta
	# Håll reda på om nästa ruta är till höger eller vänster
	private function draw_field($func) {
	    global $y, $left, $left2;
	    $to_execute = '$draw_ok = $this->'.$func.'($this->current_left);';
	    eval($to_execute);
	    if ($draw_ok) {
	        $current_y = $this->GetY();
	        # Hantering om resultatet av cellen är för stort för att få plats.
	        if ($current_y > $y + $this->current_cell_height) {
 	            $new_height = $current_y-$y;
 	            $this->current_cell_height = $new_height;
	        }
	        if ($this->current_left == $left) {
	            $this->current_left = $left2;
	        } else { 
	            # Vi har just ritat den högra rutan
	            $this->mittlinje();
	            $this->current_left = $left;
	            $y += $this->current_cell_height;
	            $this->bar();
	            $this->current_cell_height = $this->cell_y_space;
	        }
	    }
	}
	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    global $y;
	    $this->Line(static::$x_min, $y, static::$x_max, $y);
	}
	
	private function mittlinje() {
	    global $y, $mitten;
	    $down = $y + $this->current_cell_height;
	    $this->Line($mitten, $y, $mitten, $down);
	}
	
	private function cross_over() {
	    global $y, $mitten;
	    $this->Line($this->current_left, $y+static::$Margin*1.5, ($this->current_left+$mitten-(3*static::$Margin)), $y+static::$Margin*1.5);
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
	    
	    $text = trim(utf8_decode($text));
	    # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
	    if (strlen($text)>static::$text_max_length){
	        $this->SetXY($venster, $y + static::$Margin-1);
	        $this->SetFont('Arial','',static::$text_fontsize/1.5);
	        
	        if (strlen($text)>210) {
	            $this->SetFont('Arial','',static::$header_fontsize);
	            $this->MultiCell($cell_width+5, static::$cell_y-2.1, $text, 0, 'L'); # Väldigt liten och tät text
	        } else {
	            $this->MultiCell($cell_width+5, static::$cell_y-1.5, $text, 0, 'L');
	        }

	        return;
	    }
	    # Normal utskrift
	    $this->set_text_start($venster);
	    $this->Cell($cell_width, static::$cell_y, $text, 0, 0, 'L');
	    
	    return;
	}
	
	private function set_full_page($header, $text) {
	    global  $y, $left, $cell_width;
	    
	    $this->AddPage();
	    
	    $text = utf8_decode($text);
	    $this->set_header($left, $header);
	    $this->SetXY($left, $y + static::$Margin+1);
	    
	    if (strlen($text)>3500){
	        $this->SetFont('Helvetica','',static::$header_fontsize);
	        $this->MultiCell(($cell_width*2)+(2*static::$Margin), static::$cell_y-2.5, $text, 0,'L'); # Mindre radavstånd
	        return;
	    }
	    if (strlen($text)>2900){
	        $this->SetFont('Helvetica','',static::$text_fontsize-1);
	        $this->MultiCell(($cell_width*2)+(2*static::$Margin), static::$cell_y-1.5, $text, 0,'L'); # Mindre radavstånd
	        return;
	    }
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    $this->MultiCell(($cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
	}
	
	private function set_rest_of_page($header, $text) {
	    global  $y, $left, $cell_width;
	    
	    $text = utf8_decode($text);
	    $this->set_header($left, $header);
	    $this->SetXY($left, $y + static::$Margin+1);
	    
	    if (strlen($text)>2000){
	        $this->SetFont('Helvetica','',static::$text_fontsize/2); # Hantering för riktigt långa texter
	        $this->MultiCell(($cell_width*2)+(2*static::$Margin), static::$cell_y-1.5, $text, 0,'L');
	        return;
	    }
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    $this->MultiCell(($cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
	}
	
	
}


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

if (isset($_GET['id'])) {
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
}

if (!$current_user->IsAdmin) {
    if (empty($role)) {
        header('Location: index.php'); //Rollen finns inte
        exit;
    }
    
    # Kolla behörigheten
    $person = $role->getPerson();
    if ($person->UserId != $current_user->Id) {
        header('Location: ../../participant/index.php');
        exit;
    }

    if (!$role->isRegistered($current_larp)) {
        header('Location: index.php'); //Rollen är inte anmäld
        exit;
    }
}


$pdf = new CharacterSheet_PDF();
$title = (empty($role)) ? 'Alla Karaktärer' : ('Karaktärsblad '.$role->Name) ;
$pdf->SetTitle(utf8_decode($title));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$subject = (empty($role)) ? 'ALLA' : $role->Name;
$pdf->SetSubject(utf8_decode($subject));

if (empty($role)) {
    $pdf->all_character_sheets($current_larp);
} else {
    $pdf->new_character_sheet($role, $current_larp);
}

$pdf->Output();