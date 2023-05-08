<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';

require_once $root . '/includes/all_includes.php';


class Report_PDF extends FPDF {
    
    public static $Margin = 1;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 10;
    public static $text_max_length = 50;
    
    
    public $larp;
    public $name;
    public $rows;
    public $num_cols;
    public $lefts = [];
    public $cell_width;
    public $current_col = 0;
    public $current_cell_height;
    
    function Header() {
        global $root, $y;
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
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        
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

        $font_size = (600 / strlen(utf8_decode($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($this->lefts[0], $y-2);
        $this->Cell(0, static::$cell_y*6, utf8_decode($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*6) + (static::$Margin);
        
        $this->bar();
    }

    
    function new_report(LARP $larp, String $name, Array $rows) {
        global $x, $y;
        
        $this->larp     = $larp;
        $this->name     = $name;
        $this->rows     = $rows;
        $this->num_cols = sizeof($this->rows[0]);
        
        $this->cell_height = static::$cell_y + (2*static::$Margin);
        $this->cell_width = (static::$x_max - static::$x_min) / $this->num_cols - (2*static::$Margin);
        
        $current_left = static::$x_min ;
        $this->lefts[0] = $current_left;
        
        for ($i = 1; $i < $this->num_cols; $i++){
            $this->lefts[$i] = $current_left + $this->cell_width + static::$Margin*2;
            $current_left = $this->lefts[$i];
        }
        
        $this->AddPage();
        
        $this->title($this->name);
        
        $this->current_cell_height = $this->cell_height;
        
        $rubrik = true;
        foreach($this->rows as $row){
            foreach($row as $cell) {
                $this->set_cell($cell, $rubrik);
            }
            $rubrik = false;
        }
        
//         $y += $this->current_cell_height;
//         $this->bar();

	}
	
	# Dynamiska småfält
	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    global $y;
	    $this->Line(static::$x_min, $y, static::$x_max, $y);
	}
	
	private function mittlinje() {
	    global $y;
	    $x_pos = $this->lefts[$this->current_col];
	    $down = $y + $this->current_cell_height;
	    $this->Line($x_pos, $y, $x_pos, $down);
	}
	
	private function cross_over() {
	    global $y, $mitten;
	    $this->Line($this->current_left, $y+static::$Margin*1.5, ($this->current_left+$mitten-(3*static::$Margin)), $y+static::$Margin*1.5);
	}

	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_cell($text, $bold) {
	    global $y;
	    if (empty($text)) $text = ' ';
	    
	    $text = trim(utf8_decode($text));
	    
	    # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
	    # Temporärt bortkommenterat så vi tar den logiken senare
// 	    if (strlen($text) > static::$text_max_length){
// 	        $this->SetXY($x_location, $y + static::$Margin-1);
// 	        $this->SetFont('Arial','',static::$text_fontsize/1.5);
	        
// 	        if (strlen($text)>210) {
// 	            $this->SetFont('Arial','',static::$header_fontsize);
// 	            $this->MultiCell($this->cell_width+5, static::$cell_y-2.1, $text, 0, 'L'); # Väldigt liten och tät text
// 	        } else {
// 	            $this->MultiCell($this->cell_width+5, static::$cell_y-1.5, $text, 0, 'L');
// 	        }

// 	        return;
// 	    }
        
	    $x_location = $this->lefts[$this->current_col]+static::$Margin;
	    
// 	    echo "Current col $this->current_col<br>";
// 	    echo "Lefts : <br>";
// 	    print_r($this->lefts);
	    
	    # Normal utskrift
	    $this->SetXY($x_location, $y + static::$Margin + 1);
	    
	    # Sätt fonten. Första raden är rubrik och blir fetare
	    if ($bold) {
	       $this->SetFont('Helvetica','B',static::$text_fontsize+1);
	    } else {
	       $this->SetFont('Helvetica','',static::$text_fontsize);
	    }
	    
	    # Skriv ut texten i cellen
	    $this->Cell($this->cell_width, static::$cell_y, $text, 0, 0, 'L');
	    
	    # Hantering om resultatet av cellen är för stort för att få plats.
        $current_y = $this->GetY();
        if ($current_y > $y + $this->current_cell_height) {
            $new_height = $current_y - $y;
            $this->current_cell_height = $new_height;
        }
        
        # Räkna upp en cell i bredd
        $this->current_col += 1;
        if ($this->num_cols == $this->current_col) { 
            # Sista cellen i en rad
            $this->current_col = 0;
            $y += $this->current_cell_height;
            $this->bar();
            if ($y > 270) { 
                $this->AddPage(); # Ny sidan om vi är längst ner
                $y += 5;
            }
            $this->current_cell_height = $this->cell_height;
        } else {
            $this->mittlinje();
        }

	    
	    
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
