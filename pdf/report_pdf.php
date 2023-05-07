<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';

require_once $root . '/includes/all_includes.php';


class Report_PDF extends FPDF {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    
    public $larp;
    public $name;
    public $rows;
    public $lefts = [];
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
        
        $mini_header_with = 46;
        $this->SetXY($mitten-($mini_header_with/2), 3);
        $this->SetFont('Helvetica','',static::$text_fontsize/1.1);
        $this->SetFillColor(255,255,255);
        $txt = $this->larp->Name;
        $this->MultiCell($mini_header_with, 4, utf8_decode($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    # Skriv ut Rapportnamnet högst upp.
    function title($text) {
        global $y;

        $font_size = (800 / strlen(utf8_decode($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($this->lefts[0], $y-2);
        $this->Cell(0, static::$cell_y*6, utf8_decode($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*6) + (static::$Margin);
        
        $this->bar();
    }
    
//     # Namnen på karaktär och spelare
//     function name($left) {
//         global $y, $cell_width, $mitten;
     
//         $this->set_header($left, 'Rapport');

//         if (!empty($this->name)) $this->set_text($left, $this->name);

//     }

    
    function beskrivning() {
        global $y;
        $text = $this->role->Description; #.' '.strlen($role->Description);
//         if (($y > (static::$y_max/2)-static::$Margin) || (strlen($text)>2600)) {
//             $this->set_full_page('Beskrivning', $text);
//         } else {
            $this->set_rest_of_page('Beskrivning', $text);
//         }
        return true;
    }
    
    function new_report(LARP $larp, String $name, Array $rows) {
        global $x, $y, $mitten;
        
        $this->larp = $larp;
        $this->name = $name;
        $this->rows = $rows;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->cell_width = (static::$x_max - static::$x_min) / sizeof($this->rows) - (2*static::$Margin);
        
        $current_left = static::$x_min ;
        
        for ($i = 0; $i < sizeof($this->rows); $i++){
            $this->lefts[$i] = $current_left + $this->cell_width + static::$Margin;
            $current_left = $this->lefts[$i];
        }
        
        
//         $left = static::$x_min + static::$Margin;
//         $x = $left;
        
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $this->lefts[0];
        
        $this->AddPage();
        
        $this->title($this->name);
//         $this->name($left);
        
        $y += $this->cell_y_space;
        $this->current_cell_height = $this->cell_y_space;
//         $this->bar();

	}
	
	# Dynamiska småfält
	
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function yrke($left) {
// 	    $this->set_header($left, 'Yrke');
	    $this->set_text($left, $this->role->Profession);
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
	
// 	# Gemensamt sätt beräkna var rubriken i ett fält ska ligga
// 	private function set_header_start($venster) {
// 	    global $y;
// 	    $this->SetXY($venster, $y);
// 	    $this->SetFont('Helvetica','',static::$header_fontsize);
// 	}
	
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
