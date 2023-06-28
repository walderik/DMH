<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';

require_once $root . '/includes/all_includes.php';


class Group_PDF extends FPDF {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $group;
    public $person;
    public $larp_group;

    public $larp;
    public $all;
    public $current_left;
    public $cell_y_space;        # Standardhöjden på en cell 
    public $current_cell_height; # Nuvarande höjden på den här radens celler
    public $cell_width;
    
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
            $txt = $this->group->Name;
        } else {
            $txt = 'Grupp';
        }
        $this->MultiCell(30, 4, utf8_decode($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title($left, $text) {
        global $y;

        $font_size = (850 / strlen(utf8_decode($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        $this->bar();
    }
    
    # Namnen på karaktär och spelare
    function names($left, $left2) {
        global $y, $mitten;
        
        $this->SetXY($left2, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
        
        if ($this->group->IsDead) {
            $this->cross_over();
        }
        
        $font_size = (strlen($this->group->Name)>20) ? 14 : 24;
        $this->SetXY($left, $y + static::$Margin);
        $this->SetFont('Helvetica','B', $font_size); # Extra stora bokstäver på Gruppens namn
        
        $this->Cell($this->cell_width, static::$cell_y, utf8_decode($this->group->Name),0,0,'L');
        
        $this->mittlinje();
        
        $persn = $this->person;        
        $this->set_header($left2, 'Ansvarig');
        if (!empty($persn)) {
            $this->set_text($left2, utf8_decode($persn->Name));
        }
        
    }

    
    function beskrivning() {
        global $y;
        $text = $this->group->Description;
        $this->set_rest_of_page('Beskrivning', $text);
        return true;
    }
    
    function new_group_sheet(Group $group_in, LARP $larp_in, bool $all_in=false) {
        global $x, $y, $left, $left2, $mitten;
        
        $this->group = $group_in;
        $this->person = $this->group->getPerson();

        $this->larp = $larp_in;
        
        $this->larp_group = LARP_Group::loadByIds($this->group->Id, $this->larp->Id);
        
        $this->all = $all_in;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->current_cell_height = $this->cell_y_space;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $this->cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);     
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $left;
        
        $this->AddPage();
        
        $this->title($left, $this->larp->Name);
        $this->names($left, $left2);
        
        $y += $this->cell_y_space;
        
        $this->bar();
        $this->draw_row('medlemmar');
        
        $this->draw_field('friends');
        $this->draw_field('enemies');
        
        $this->draw_field('rikedom');
        $this->draw_field('bor');
        
        $this->draw_field('want_intrigue');
        $this->draw_field('intrigtyper');
        
        if ($this->all) $this->draw_field('intrigue_ideas');
        if ($this->all) $this->draw_field('remaining_intrigues');
        
        $this->draw_field('other_info');
        
        if ($this->current_left == $left2) $this->draw_field('empty');
        
        if ($this->all) $this->draw_row('organizerNotes');
        
        $this->beskrivning();
        
    return;    

     
        # Det nedan löser vi med tiden. Just nu et jag inte hur det är tänkt fungera

        $previous_larps = $this->group->getPreviousLarps();
        if (isset($previous_larps) && count($previous_larps) > 0) {
            
            foreach ($previous_larps as $prevoius_larp) {
                $previous_larp_group = LARP_Role::loadByIds($this->group->Id, $prevoius_larp->Id);
                $this->AddPage();
                $this->title($left, "Historik $prevoius_larp->Name");

                $this->names($left, $left2);
                
                $text = (isset($prevoius_larp_role->Intrigue) && $prevoius_larp_role->Intrigue != "") ? $prevoius_larp_role->Intrigue : "Inget att rapportera";
                $this->set_rest_of_page("Intrig", $text);
                $y = $this->GetX();
                
               
                $text = (isset($prevoius_larp_role->WhatHappened) && $prevoius_larp_role->WhatHappened != "") ? $prevoius_larp_role->WhatHappened : "Inget att rapportera";
                $this->set_rest_of_page("Vad hände för ".$this->group->Name."?", $text);
                $y = $this->GetX();
                $this->bar();
                $text = (isset($prevoius_larp_role->WhatHappendToOthers) && $prevoius_larp_role->WhatHappendToOthers != "") ? $prevoius_larp_role->WhatHappendToOthers : "Inget att rapportera";
                $this->set_rest_of_page("Vad hände för andra?", $text);
            }
        }
	}
	
	function all_group_sheets(LARP $larp_in ) {
	    $this->larp = $larp_in;

	    $groups = Group::getAllRegistered($this->larp);
	    foreach($groups as $group) {
	        $this->new_group_sheet($group, $larp_in, true);
	    }
	}

	# Dynamiska rader
	protected function medlemmar() {
	    global $left;
	    $this->set_header($left, 'Medlemmar');
	    
	    $namnen = array();
	    $roles = Role::getAllMainRolesInGroup($this->group, $this->larp);
	    if (empty($roles)) return true;
	    foreach ($roles as $role) {
	        $namnen[] = $role->Name;
	    }
	    $txt = join(", ", $namnen);
	    
	    $this->set_row($txt);
	    return true;
	}
	
	
	protected function OrganizerNotes() {
	    global $left;
	    $this->set_header($left, 'Anteckning');
	    $this->set_row($this->group->OrganizerNotes);
	    return true;
	}
	
	
	# Dynamiska småfält
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function friends($left) {
	    $this->set_header($left, 'Vänner');
	    $this->set_text($left, $this->group->Friends);
	    return true;
	}
	
	protected function enemies($left) {
	    $this->set_header($left, 'Fiender');
	    $this->set_text($left, $this->group->Enemies);
	    return true;
	}

	
	
	protected function rikedom($left) {
	    if (!Wealth::isInUse($this->larp)) return false;
	    
	    $this->set_header($left, 'Rikedom');
	    $text = ($this->group->is_trading($this->larp)) ? " (Handel)" : " (Ingen handel)";
	    $this->set_text($left, $this->group->getWealth()->Name . $text);
	    return true;
	}
	
	protected function want_intrigue($left) { 
	    $this->set_header($left, 'Will gärna ha intrig');
	    
	    $text = $this->larp_group->WantIntrigue ? 'Ja' : 'Nej';

	    $this->set_text($left, $text);
	    return true;
	}
	
	protected function intrigtyper($left) {
	    if (!IntrigueType::isInUse($this->larp)) return false;

	    $this->set_header($left, 'Intrigtyper');

	    $text = commaStringFromArrayObject($this->larp_group->getIntrigueTypes());
	    $this->set_text($left, $text);
	    return true;
	}
	
	protected function intrigue_ideas($left) {
	    $this->set_header($left, 'Intrigéer');
	    
	    $this->set_text($left, $this->group->IntrigueIdeas);
	    return true;
	}
	
	protected function remaining_intrigues($left) {
	    $this->set_header($left, 'Kvarvarande intriger');
	    
	    $this->set_text($left, $this->larp_group->RemainingIntrigues);
	    return true;
	}

	protected function other_info($left) {
	    $this->set_header($left, 'Annat');    
	    $this->set_text($left, $this->group->OtherInformation);
	    return true;
	}

	
	protected function bor($left) {
	    $this->set_header($left, 'Bor');
	    $this->set_text($left, $this->group->getPlaceOfResidence()->Name);
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
 	            $this->current_cell_height = $new_height;
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
	
	# Rita en rad
	private function draw_row($func) {
	    global $y, $left;
	    $to_execute = '$draw_ok = $this->'.$func.'();';
	    eval($to_execute);
	    if ($draw_ok) {

	        # Hantering om resultatet av cellen är för stort för att få plats.
	        $current_y = $this->GetY();
	        if ($current_y > $y + $this->current_cell_height) {
	            $new_height = $current_y-$y;
	            $this->current_cell_height = $new_height;
	        }
	        
// 	        # Räkna upp en cell i bredd
// 	        if ($this->current_left == $left) {
// 	            $this->current_left = $left2;
// 	        } else {
	            # Vi har just ritat den högra rutan
// 	            $this->mittlinje();
// 	            $this->current_left = $left;
	            $y += $this->current_cell_height;
	            $this->bar();
	            $this->current_cell_height = $this->cell_y_space;
// 	        }
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
	    $this->set_header_start($venster);
	    $this->Cell($this->cell_width, static::$cell_y, utf8_decode($text),0,0,'L');
	}
	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_text($venster, $text) {
	    global $y;
	    
	    if (empty($text)) return;
	    
	    $text = trim(utf8_decode($text));
	    # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
	    if (strlen($text)>static::$text_max_length){
	        $this->SetXY($venster, $y + static::$Margin-1);
	        $this->SetFont('Arial','',static::$text_fontsize/1.5);
	        
	        if (strlen($text)>210) {
	            $this->SetFont('Arial','',static::$header_fontsize);
	            $this->MultiCell($this->cell_width+5, static::$cell_y-2.1, $text, 0, 'L'); # Väldigt liten och tät text
	        } else {
	            $this->MultiCell($this->cell_width+5, static::$cell_y-1.5, $text, 0, 'L');
	        }

	        return;
	    }
	    # Normal utskrift
	    $this->set_text_start($venster);
	    $this->Cell($this->cell_width, static::$cell_y, $text, 0, 0, 'L');
	    
	    return;
	}
	
	# Gemensam funktion för all logik för att skriva ut en hel rad
	private function set_row($text) {
	    global $left, $y;
	    
	    if (empty($text)) return;
	    
	    $text = trim(utf8_decode($text));
	    # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
	    if (strlen($text) > (static::$text_max_length*2)){
	        $this->SetXY($left, $y + static::$Margin);
	        $this->SetFont('Arial','',static::$text_fontsize/1.5);
	        
// 	        if (strlen($text)>210) {
// 	            $this->SetFont('Arial','',static::$header_fontsize);
// 	            $this->MultiCell((2*$this->cell_width)+5, static::$cell_y-2.1, $text, 0, 'L'); # Väldigt liten och tät text
// 	        } else {
	            $this->MultiCell((2*$this->cell_width)+5, static::$cell_y-1, $text, 0, 'L');
// 	        }
	        
	        return;
	    }
	    # Normal utskrift
	    $this->set_text_start($left);
	    $this->Cell((2*$this->cell_width), static::$cell_y, $text, 0, 0, 'L');
	    
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
	        return;
	    }
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
	}
	
	
}