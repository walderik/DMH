<?php
# Läs mer på http://www.fpdf.org/
# Testa orientation med $this->CurOrientation ger 'P' eller 'L'

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';

require_once $root . '/includes/all_includes.php';


class Report_PDF extends FPDF {
    
    public static $debug=false;
    
    public static $Margin = 1;
    
    public static $x_min = 5;
    public static $y_min = 5;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 9;
    
    public $text_fontsize;
    public $x_max;
    public $y_max;
    public $larp;
    public $name;
    public $rows;
    public $num_cols;
    public $left_positions = [];
    public $default_cell_width;
    public $cell_widths = [];
    public $current_col = 0;
    public $current_cell_height;
    public $text_max_length;
    
    function Header() {
        global $root, $y;
        
        # Rita två rutor runt sidan
        $this->SetLineWidth(0.6);
        $this->Line(static::$x_min, static::$y_min, $this->x_max, static::$y_min);
        $this->Line(static::$x_min, static::$y_min, static::$x_min, $this->y_max);
        $this->Line(static::$x_min, $this->y_max, $this->x_max, $this->y_max);
        $this->Line($this->x_max, static::$y_min, $this->x_max, $this->y_max);
        
        $space = 1.2;
        $this->Line(static::$x_min-$space, static::$y_min-$space, $this->x_max+$space, static::$y_min-$space);
        $this->Line(static::$x_min-$space, static::$y_min-$space, static::$x_min-$space, $this->y_max+$space);
        $this->Line(static::$x_min-$space, $this->y_max+$space, $this->x_max+$space, $this->y_max+$space);
        $this->Line($this->x_max+$space, static::$y_min-$space, $this->x_max+$space, $this->y_max+$space);
        
        # Fixa rubriken med lajvets namn på toppen av sidan
        $mini_header_with = 46;
        $mitten = static::$x_min + ($this->x_max - static::$x_min) / 2 ;
        
        $this->SetXY($mitten-($mini_header_with/2), 3);
        $this->SetFont('Helvetica','', static::$header_fontsize);
        $this->SetFillColor(255,255,255);
        $txt = $this->larp->Name;
        $this->MultiCell($mini_header_with, 4, utf8_decode($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    # Det som står längst ner på varje sida
    function Footer()
    {
       
        // Select Arial italic 8
        $this->SetFont('Arial','I', static::$header_fontsize);
        
        # Om det är ett dokument med känsliga uppgifter
        if ($this->is_sensitive) {
            // Go to 1.5 cm from bottom
            $this->SetY(-15); 
            $this->Cell(0, 10, utf8_decode('Det här dokumentet innehåller känsliga personuppgifter. Förstör det efter lajvet.'), 0, 0, 'L');
        }

        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Print centered page number
        $this->Cell(0, 10, 'Sidan '.$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    
    # Skriv ut Rapportnamnet högst upp.
    # Används på första sidan
    function title($text) {
        global $y;

        # Bra ställe lägga ut debuginfo i början på rapporten
//         $text = $this->GetPageWidth() . " - $text"; # 210 för A4 Portrait
//         $text = strlen(utf8_decode($text)) . " - $text"; # Antalet tecken

        $font_size = 90; # Så det inte blir för stort i X-led
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $field_with = $this->x_max - static::$x_min - (2*static::$Margin); # Bredden på rutan som kan fyllas med rubriken
        while ($this->GetStringWidth($text) > $field_with*0.85) { # Se till så inte rubrik-rutan får för lång text.
            $font_size -=1;
            $this->SetFont('Helvetica','B', $font_size);
        }

        $this->SetXY($this->left_positions[0], $y-1);
        $this->Cell(0, static::$cell_y*6, utf8_decode($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*6) + (static::$Margin);
        
        $this->bar();
    }

    
    function new_report(LARP $larp, String $name, Array $rows, bool $is_sensitive = false) {
        global $x, $y;
        if (static::$debug)
            echo "<br><br>New Report - $name<br>";
        
        $this->AliasNbPages();
        
        $this->is_sensitive = $is_sensitive;
        
        $this->text_fontsize = 10;
        
        $this->x_max = $this->GetPageWidth()  - static::$x_min;
        $this->y_max = $this->GetPageHeight() - static::$y_min;
        
        $this->left_positions = [];
        $this->cell_widths = [];
        
        $this->larp     = $larp;
        $this->name     = $name;
        $this->rows     = $rows;
        $this->num_cols = sizeof($this->rows[0]);
        $this->text_max_length = 94 / $this->num_cols; # Max antal tecken per kolumn (Empiriskt testat för vad som ser bra ut)
        
        $this->cell_height = static::$cell_y + (2*static::$Margin);
        $this->default_cell_width = ($this->x_max - static::$x_min) / $this->num_cols - (2*static::$Margin);
        
        $current_left = static::$x_min ;
        
        $this->left_positions[0] = $current_left; # Vänstraste vänstermarginalen

        $this->SetFont('Helvetica', '', $this->text_fontsize);
        
        if (static::$debug) {
            echo "<br>ROWS in report:<br>\n";
            print_r($this->rows);
        }
        
        # Markera extra breda kolumner
        # Beräkna också maxvidden på en rad text.
        $max_length = [];
        $max_col_text_width = [];
        foreach($this->rows as $row){
            $column = 0;
            foreach($row as $cell_text){
                if (!isset($max_length[$column]))         $max_length[$column] = 0;
                if (!isset($max_col_text_width[$column])) $max_col_text_width[$column] = 0;
                if (strlen($cell_text) > $max_length[$column]) $max_length[$column] = strlen($cell_text);
                $text_rows_in_cell = explode("\n", $cell_text);
                foreach($text_rows_in_cell as $text_row_in_cell) {
                    $text_width = $this->GetStringWidth($text_row_in_cell);
                    if ($text_width > $max_col_text_width[$column]) {
                        $max_col_text_width[$column] = $text_width;
                    }
                }
                $column++;
            }
        }
        
        if (static::$debug) {
            echo "<br><br>MAX TEXT WIDTHS in report<br>\n";
            print_r($max_col_text_width);
        }
        
        $with_part = [];
        $total_with_all_max_widths = 0;
        $avg_part = 0;
        for ($column = 0; $column < $this->num_cols; $column++){
            $total_with_all_max_widths += $max_col_text_width[$column];
        }
        
        if (static::$debug)
            echo "<br><br>Total max with : $total_with_all_max_widths <br>\n";
        
        for ($column = 0; $column < $this->num_cols; $column++){
            if ($max_length[$column] > $this->text_max_length*3) {
                $with_part[$column] = 2;
            } elseif ($max_length[$column] > $this->text_max_length*2) {
                $with_part[$column] = 1.6;
            } elseif ($max_length[$column] > $this->text_max_length) {
                $with_part[$column] = 1.3;
            } elseif ($max_length[$column] < $this->text_max_length) {
                $with_part[$column] = 0.05*$max_length[$column];
            } else {
                $with_part[$column] = 1;
            }
            
            $avg_part += $with_part[$column];
        }
        $avg_part = $avg_part / $this->num_cols;
        
//         echo "<br>\n";
//         print_r($with_part);
//         echo "with_part --<br>\n";
//         echo "AVG: $avg_part <br>\n";
        
        # Sätt alla kolumnbredder
        for ($column = 0; $column < $this->num_cols; $column++){
            $this->cell_widths[$column] = round($this->default_cell_width * ($with_part[$column] / $avg_part ));
//             $this->cell_widths[$col] = $this->default_cell_width;
        }

//         print_r($this->cell_widths);
//         echo "Cell widths --<br>\n";
        
        # Beräkna vänster-marginaler
        for ($column = 1; $column < $this->num_cols; $column++){
            $this->left_positions[$column] = $current_left + $this->cell_widths[$column-1] + static::$Margin*2;
            $current_left = $this->left_positions[$column];
        }
        
//         print_r($this->lefts);
//         echo "Lefts 2 --<br>\n";
        
        $this->AddPage($this->CurOrientation);
        
        $this->title($this->name);
        
        $this->current_cell_height = $this->cell_height;
        
        $rubrik = true;
        foreach($this->rows as $row){
            foreach($row as $cell_text) {
                $this->set_cell($cell_text, $rubrik);
            }
            $rubrik = false;
        }
	}
	
	# Dynamiska småfält
	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    global $y;
	    $this->Line(static::$x_min, $y, $this->x_max, $y);
	}
	
	private function mittlinje($col) {
	    global $y;
	    $x_pos = $this->left_positions[$col]; # $this->current_col];
	    $down = $y + $this->current_cell_height;
	    $this->Line($x_pos, $y, $x_pos, $down);
	}
	
// 	private function cross_over() {
// 	    global $y, $mitten;
// 	    $this->Line($this->current_left, $y+static::$Margin*1.5, ($this->current_left+$mitten-(3*static::$Margin)), $y+static::$Margin*1.5);
// 	}

	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_cell($text, $bold) {
	    global $y;
	    if (empty($text)) $text = ' ';
	    
	    $text = trim(utf8_decode($text));
// 	    $text = "$text - $this->current_col";
	    
	    # Max som får plats är per kolumnbredd = 94 / antalet kolumner:
	    #  2 - 47 tecken
	    #  3 - 31 tecken
	    #  4 - 23 tecken
	    
	    $bold_char = $bold ? 'B' : '';
	    $scaling = 1.2;
	   
	    # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
//  	    if (strlen($text) > $this->text_max_length){
//  	        $this->SetFont('Arial', $bold_char, $this->text_fontsize/$scaling);
//  	    } else {
 	        $this->SetFont('Helvetica', $bold_char, $this->text_fontsize);
//         }
    
	    $x_location = $this->left_positions[$this->current_col]+static::$Margin;
	    
	    # Normal utskrift
	    $this->SetXY($x_location, $y + static::$Margin + 1);
	    
	    # Skriv ut texten i cellen
	    $this->MultiCell($this->cell_widths[$this->current_col], static::$cell_y-1.5, $text, 0, 'L');

	    # Hantering om resultatet av cellen är för stort för att få plats.
        $current_y = $this->GetY();
        if ($current_y > $y + $this->current_cell_height) {
            $new_height = $current_y - $y;
            $this->current_cell_height = $new_height;
            # Efterjustera mittlinjen om det behövs
            
        }
            
        # Räkna upp en cell i bredd
        $this->current_col += 1;
        
        if ($this->num_cols == $this->current_col) { 
            # Sista cellen i en rad
            
            # Dra alla mellanstrecken
            for ($col = 0; $col < $this->num_cols; $col++) {
                $this->mittlinje($col);
            }
            
            $this->current_col = 0;
            $y += $this->current_cell_height;
            $this->bar();
            if ($y > 270) { 
                $this->AddPage($this->CurOrientation); # Ny sidan om vi är längst ner
                $y += 5;
            }
            
            $this->current_cell_height = $this->cell_height;
        }
	    
	    return;
	}
	
}
