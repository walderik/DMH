<?php
# Läs mer på http://www.fpdf.org/

# För avgöra om intriger skall skrivas ut:
# $larp->isIntriguesReleased()

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';


class MagicMagicianSheet_PDF extends PDF_MemImage {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $magician;
    public $role;
    public $person;
    public $larp;
    public $current_left;
    public $cell_y_space;        # Standardhöjden på en cell 
    public $current_cell_height; # Nuvarande höjden på den här radens celler
    public $cell_width;
    
    function Header() {
        global $root, $y, $mitten, $rowImageHeight, $lovest_y;
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

        
        $txt = 'Magiker';
       
        

        $this->MultiCell(30, 4, utf8_decode($txt), 0, 'C', true);
        
        $this->SetXY($mitten-15,  static::$y_min + 2*static::$Margin);
        $y = $this->GetY();
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
    
    function single_magician_sheet(Magic_Magician $magician_in, LARP $larp_in) {
        global $x, $y, $left, $left2, $mitten;
        $space = 3;
        
        $this->magician = $magician_in;
        $this->role = $this->magician->getRole();
        $this->person = $this->role->getPerson();
        $this->larp = $larp_in;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->current_cell_height = $this->cell_y_space;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $this->cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);     
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $left;
        
        $this->AddPage();
        
        $this->title($left, $this->role->Name);
         
        //$y += $this->cell_y_space;
        
        $this->bar();
        
        # Uppräkning av ett antal fält som kan finnas eller inte
        $this->draw_field('school');
        $this->draw_field('level');
        $this->draw_field('master');
        $this->draw_field('apprentices');
        
        
        # Fixa till om vi skapat ett udda antal fält
        if ($this->current_left == $left2) $this->draw_field('empty');
        $y += 3;
        
        # Att fixa när vi väl har tidigare lajv
        $spells = $this->magician->getSpells();
        if (!empty($spells)) {
            
            foreach ($spells as $spell) {
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','B',static::$text_fontsize);
                $this->Cell($this->cell_width, static::$cell_y, utf8_decode($spell->Name),0,0,'L');
                
                $y = $this->GetY() + $space*3;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','',static::$text_fontsize);
                $text = trim(utf8_decode("Nivå ".$spell->Level));
                $this->Cell($this->cell_width, static::$cell_y,$text,0,0,'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
                
                $y = $this->GetY() + $space*3;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','',static::$text_fontsize);
                $text = trim(utf8_decode($spell->Description));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
 
                if (isset($spell->Special)) {
                    $y = $this->GetY() + $space*3;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont('Helvetica','',static::$text_fontsize);
                    $text = trim(utf8_decode($spell->Special));
                    $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                    $y = $this->GetY() + $space;
                    $this->SetXY($left, $y);
                }
                
                $this->bar();
                $y += 3;
                    
            }
        }
                
 	}
	
	function all_magician_sheets(LARP $larp_in) {
	    $this->larp = $larp_in;

	    $magicians = Magic_Magician::allByComingToLarp($larp_in);
	    foreach($magicians as $magician) {
	        $this->single_magician_sheet($magician, $larp_in);
	    }
	}

	
	
	# Dynamiska småfält
	
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function school($left) {
	    $this->set_header($left, 'Magiskola');
	    $schoolId = $this->magician->MagicSchoolId;
	    if (empty($schoolId)) return;
	    $school = Magic_School::loadById($schoolId);

	    $this->set_text($left, $school->Name);
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
	    if (empty($apprentices)) return;
	    
	    $apprentice_names = array();
	    foreach($apprentices as $apprentice) {
	        $apprenticeSchool = $apprentice->getMagicSchool();
	        $str = $apprentice->getRole()->Name." (";
	        if (isset($apprenticeSchool)) $str.=$apprentice->getMagicSchool()->Name.", ";
	        $str.="nivå $apprentice->Level)";
	        $apprentice_names[] = $str;
	    }
	    $txt = implode(", ", $apprentice_names);

	    $this->set_header($left, 'Lärlingar');
	    $this->set_text($left, $txt);
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
	        $this->MultiCell($this->cell_width+5, static::$cell_y-1, $text, 0, 'L');

	        return;
	    }
	    
	    $this->Cell($this->cell_width, static::$cell_y, $text, 0, 0, 'L');
	    
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
