<?php
# Läs mer på http://www.fpdf.org/

# För avgöra om intriger skall skrivas ut:
# $larp->isIntriguesReleased()

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';


class MagicMagicianRitualSheet_PDF extends PDF_MemImage {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 291;
    public static $y_min = 5;
    public static $y_max = 205;
    
    public static $middle;
    public static $col1_x_min;
    public static $col1_x_max;
    
    public static $col2_x_min;
    public static $col2_x_max;
    
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 10;
    public static $text_max_length = 50;
    
    public $magician;
    public $magic_school;
    public $role;
    public $person;
    public $larp;
    public $current_left;
    public $current_right;
    public $cell_y_space;        # Standardhöjden på en cell 
    public $current_cell_height; # Nuvarande höjden på den här radens celler
    public $cell_width;
    
    function Header() {
        global $root, $y, $middle, $lovest_y;

        $this->SetXY($middle-30, 3);
        $this->SetFont('Helvetica','',static::$text_fontsize/0.9);
        $this->SetFillColor(255,255,255);
        
        $txt = "ID-kort för ritualer";
        $this->MultiCell(60, 4, utf8_decode($txt), 0, 'C', true);
        
        $y = $this->GetY();
        
        
        $this->SetXY($middle-30, static::$y_max-3);
        $this->SetFont('Helvetica','',static::$text_fontsize/1.1);
        $this->SetFillColor(255,255,255);
        
        $txt = $this->larp->Name;
        $this->MultiCell(60, 4, utf8_decode($txt), 0, 'C', true);
        
        
        
        $rowImageHeight = 0;
        $lovest_y = $y;

    }
    
    # Skriv ut lajvnamnet högst upp.
    function title($left, ?String $text = null) {
        global $y;

        if (empty($text)) $text =  $this->larp->Name;
        
        $font_size = (850 / strlen(utf8_decode($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        //$this->bar();
    }
    
    function print_mage_info() {
        global $y, $left, $left2, $middle;
        
        $space = 3;
        
        # Uppräkning av ett antal fält som kan finnas eller inte
        $this->draw_field('magician');
        $this->draw_field('school');
        $this->draw_field('off_name');
        $this->draw_field('level');
        $this->draw_field('housing');
        $this->draw_field('master');
        $this->draw_field('apprentices');
        
        # Fixa till om vi skapat ett udda antal fält
        if ($this->current_left == $left2) $this->draw_field('empty');
        
        
        //Annat viktigt
        
        $y = $this->GetY() + 3*$space;
        $this->SetXY($left, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Annat viktigt"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        
        $y = $this->GetY();
        $this->Line($this->GetStringWidth($text)+$this->current_left+2, $y, $middle-10, $y);

        $y += +7;
        $this->Line($left, $y, $middle-10, $y);
        
        $y += +7;
        $this->Line($left, $y, $middle-10, $y);
        
        $this->SetY($y);
        
        //Godkänd workshop
        $y = $this->GetY() + $space;
        $this->SetXY($this->current_left, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Godkänd workshop"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        

        $this->SetXY(45, $y);
        $this->Cell(5, 5, "  ", 1, 'L');
        
        $this->SetXY(50, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Ja"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');

        $this->SetXY(65, $y);
        $this->Cell(5, 5, "  ", 1, 'L');
        
        $this->SetXY(70, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Nej"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        
        //$y += +7;
        //$this->Line(static::$x_min, $y, $middle, $y);
        
    }
    
    
    function print_ritual_info() {
        global $y;
        $this->SetXY($this->current_left, $this->GetY());
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $text = trim(utf8_decode("Ritualer utförda"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        
        $this->SetXY($this->current_left, $this->GetY());
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Mycket kort beskrivning:"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        
        $y = $this->GetY();
        $this->Line($this->GetStringWidth($text)+$this->current_left+2, $y, $this->current_right-10, $y);
        
        $y += +8;
        $this->Line($this->current_left, $y, $this->current_right-10, $y);
        
        $y += +8;
        $this->Line($this->current_left, $y, $this->current_right-10, $y);
        
        $this->SetY($y);

    
        //Godkänd ritual
        $y = $this->GetY() + 3;
        $this->SetXY($this->current_left, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Godkänd"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        
        
        $this->SetXY($this->current_left+45, $y);
        $this->Cell(5, 5, "  ", 1, 'L');
        
        $this->SetXY($this->current_left+50, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Ja"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        
        $this->SetXY($this->current_left+65, $y);
        $this->Cell(5, 5, "  ", 1, 'L');
        
        $this->SetXY($this->current_left+70, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode("Nej; varför inte:"));
        $this->Cell(0, static::$cell_y-1, $text, 0, 'L');
        
        $y = $this->GetY();
        $this->Line(70 + $this->GetStringWidth($text)+$this->current_left+2, $y, $this->current_right-10, $y);
        
        $y += +8;
        $this->Line($this->current_left, $y, $this->current_right-10, $y);
        
        $y += +8;
        $this->Line($this->current_left, $y, $this->current_right-10, $y);
        
        $y += +8;
        $this->Line($this->current_left, $y, $this->current_right-10, $y);
        
        $y += +8;
        $this->Line($this->current_left, $y, $this->current_right-10, $y);
        
        $this->SetY($y);
        
    
    }
    
    
    
    function single_magician_sheet(Magic_Magician $magician_in, LARP $larp_in) {
        global $x, $y, $left, $left2, $middle, $lovest_y;
        $space = 3;
        
    
        $this->magician = $magician_in;
        $this->magic_school = $this->magician->getMagicSchool();
        $this->role = $this->magician->getRole();
        $this->person = $this->role->getPerson();
        
        $this->larp = $larp_in;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->current_cell_height = $this->cell_y_space;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $middle = static::$x_min + (static::$x_max - static::$x_min) / 2;
        $this->cell_width = ($middle - static::$x_min) / 2 - (2*static::$Margin);     
        $left2 = $this->cell_width + 2*static::$Margin;
        
        $this->current_left = $left;
        
        $this->SetMargins(0, 0);
        $this->SetAutoPageBreak(false);
        
        $this->AddPage('L');
        
        //$this->Line($middle, static::$y_min, $middle, static::$y_max);
        
        
        $this->print_mage_info();
        
        $middle_y = $lovest_y + (static::$y_max - $lovest_y) / 2;
        
        $y = $middle_y;
        $this->SetY($y);
        $this->current_left = $left;
        $this->current_right = $middle;
        $this->print_ritual_info();
 
        $y = $lovest_y;
        $this->SetY($y);
        $this->current_left = $middle+static::$Margin;
        $this->current_right = static::$x_max;
        $this->print_ritual_info();
        
        $y = $middle_y;
        $this->SetY($y);
        $this->current_left = $middle+static::$Margin;
        $this->current_right = static::$x_max;
        $this->print_ritual_info();
        
        $this->AddPage('L');

        $y = $lovest_y;
        $this->SetY($y);
        $this->current_left = $left;
        $this->current_right = $middle;
        $this->print_ritual_info();
        
        $y = $middle_y;
        $this->SetY($y);
        $this->current_left = $left;
        $this->current_right = $middle;
        $this->print_ritual_info();
        
        $y = $lovest_y;
        $this->SetY($y);
        $this->current_left = $middle+static::$Margin;
        $this->current_right = static::$x_max;
        $this->print_ritual_info();
        
        $y = $middle_y;
        $this->SetY($y);
        $this->current_left = $middle+static::$Margin;
        $this->current_right = static::$x_max;
        $this->print_ritual_info();
        
 	}
	
	function all_magician_sheets(LARP $larp_in) {
	    $this->larp = $larp_in;

	    $magicians = Magic_Magician::allByComingToLarp($larp_in);
	    foreach($magicians as $magician) {
	        $this->single_magician_sheet($magician, $larp_in);
	        return;
	    }
	}

	
	
	# Dynamiska småfält
	
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function magician($left) {
	    $this->set_header($left, 'Magiker');
	    $this->set_text($left, $this->role->Name);
	    return true;
	}
	
	protected function school($left) {
	    $this->set_header($left, 'Magiskola');
	    $schoolId = $this->magician->MagicSchoolId;
	    if (empty($this->magic_school)) return;
	    
	    $this->set_text($left, $this->magic_school->Name);
	    return true;
	}
	
	protected function level($left) {
	    $this->set_header($left, 'Nivå');
	    $this->set_text($left, $this->magician->Level);
	    return true;
	}
	
	protected function master($left) {
	    $this->set_header($left, 'Mästare');
	    $masterId = $this->magician->MasterMagicianId;
	    if (empty($masterId)) return true;
	    $master = Magic_Magician::loadById($masterId);
	    $this->set_text($left, $master->getRole()->Name);
	    return true;
	}
	
	protected function apprentices($left) {
	    $apprentices = $this->magician->getApprentices();
	    $this->set_header($left, 'Lärlingar');
	    if (empty($apprentices)) {
	        $txt = "Har inga lärlingar";
	    } else {
	    
    	    $apprentice_names = array();
    	    foreach($apprentices as $apprentice) {
    	        $apprenticeSchool = $apprentice->getMagicSchool();
    	        $str = $apprentice->getRole()->Name." (";
    	        if (isset($apprenticeSchool)) $str.=$apprentice->getMagicSchool()->Name.", ";
    	        $str.="nivå $apprentice->Level)";
    	        $apprentice_names[] = $str;
    	    }
    	    $txt = implode(", ", $apprentice_names);
	    }
	    $this->set_text($left, $txt);
	    return true;
	}
	
	protected function off_name($left) {
	    $this->set_header($left, 'Off-namn');
	    $this->set_text($left, $this->person->Name);
	    return true;
	}
	
	protected function housing($left) {
	    $this->set_header($left, 'Boende');
	    $housing = $this->person->getHouseAtLarp($this->larp);
	    if (isset($housing)) $this->set_text($left, $housing->Name);
	    return true;
	}
	
	
	# Rita en ruta
	# Håll reda på om nästa ruta är till höger eller vänster
	private function draw_field($func) {
	    global $y, $left, $left2;
	    $to_execute = '$draw_ok = $this->'.$func.'($this->current_left);';
	    eval($to_execute);
	    if ($draw_ok) {
	        # Hantering om resultatet av cellen är för stort för att få plats.
	        $current_y = $this->GetY();
	        if ($current_y > $y + $this->current_cell_height) {
 	            $new_height = $current_y-$y;
 	            $this->current_cell_height = $new_height+2;
	        }
	        
	        # Räkna upp en cell i bredd
	        if ($this->current_left == $left) {
	            $this->current_left = $left2;
	        } else { 
	            # Vi har just ritat den högra rutan
	            $this->mittlinje();
	            $this->current_left = $left;
	            $y += $this->current_cell_height;
	            //$this->bar();
	            $this->current_cell_height = $this->cell_y_space;
	        }
	    }
	}
	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    global $y;
	    $this->Line(static::$x_min, $y, static::$x_max, $y);
	}
	
	# Dra en linje tvärs över arket på höjd $y
	private function guide_line() {
	    global $y;
	    $this->Line($this->current_left, $y, static::$middle, $y);
	}
	
	
	
	private function mittlinje() {
	    global $y, $mitten;
	    $down = $y + $this->current_cell_height;
	    $this->Line($mitten, $y, $mitten, $down);
	}
	
	# En rad med överstrykning Används för att visa att något inte gäller längre, som för döda.
	private function cross_over() {
	    global $y, $mitten;
	    $this->Line($this->current_left, $y+static::$Margin*1.5, ($this->current_left+$mitten-(3*static::$Margin)), $y+static::$Margin*1.5);
	}
	
	# Gemensam funktion för all logik för att skriva ut ett rubriken
	private function set_header($venster, $text) {
	    global $y;
	    $this->SetXY($venster, $y);
	    $this->SetFont('Helvetica','',static::$header_fontsize);
	    $this->Cell($this->cell_width, static::$cell_y, utf8_decode($text),0,0,'L');
	}
	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_text($venster, $text) {
	    global $y;
	    
	    if (empty($text)) return;
	    
	    $text = trim(utf8_decode($text));
	    
	    $this->SetXY($venster, $y + static::$Margin + 1);
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    
	    # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
	    
	    
	    if (strlen($text) > static::$text_max_length){
	        $this->SetXY($venster, $y + static::$Margin);
	        $this->SetFont('Arial','',static::$text_fontsize/1.25);
	        $this->MultiCell($this->cell_width-5, static::$cell_y-1, $text, 0, 'L');

	        return;
	    }
	    
	    $this->Cell($this->cell_width-5, static::$cell_y, $text, 0, 0, 'L');
	    
	    return;
	}
	
	private function set_full_page($header, $text) {
	    global  $y, $left;
	    
	    $this->AddPage();
	    
	    $text = utf8_decode($text);
	    $this->set_header($left, $header);
	    $this->SetXY($left, $y + static::$Margin+1);
	    
	    if (strlen($text)>3500){
	        $this->SetFont('Helvetica','',static::$header_fontsize);
	        $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y-2.5, $text, 0,'L'); # Mindre radavstånd
	        return;
	    }
	    if (strlen($text)>2900){
	        $this->SetFont('Helvetica','',static::$text_fontsize-1);
	        $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y-1.5, $text, 0,'L'); # Mindre radavstånd
	        return;
	    }
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
	}
	
	private function set_rest_of_page($header, $text) {
	    global  $y, $left;
	    
	    $text = utf8_decode($text);
	    $this->set_header($left, $header);
	    $this->SetXY($left, $y + static::$Margin+1);
	    
	    if (strlen($text)>1800){
	        $this->SetFont('Helvetica','',static::$text_fontsize/1.5); # Hantering för riktigt långa texter
	        $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y-1.3, $text, 0,'L');
	        $y = $this->GetY();
	        return;
	    }
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
	    $y = $this->GetY();
	}
	
	
}
