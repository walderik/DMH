<?php
# Läs mer på http://www.fpdf.org/
# Testa orientation med $this->CurOrientation ger 'P' eller 'L'

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';

require_once $root . '/includes/all_includes.php';


class Report_PDF extends FPDF {
    
    public static $debug = false; 
    
    public static $Margin = 1;
    public static $frame_space = 1.3;
    
    public static $x_min = 5;
    public static $y_min = 5;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 9;
    
    public static $max_font_size = 20; # Så stor font som rapporten kan få om allt får plats jättebra
    public static $min_font_size = 9;  # Mindre än så kan man inte läsa
    
    public $text_fontsize;
    public $x_max;
    public $y_max;
    public $larp;
    public $name;
    public $rows;
    public $num_cols;
    public $left_positions = [];

    public $cell_widths = [];
    public $current_col = 0;
    public $current_cell_height;
    public $current_y;
    public $text_max_length;
    
    function Header() {
        global $root;
        
        # Rita två rutor runt sidan
        $this->SetLineWidth(0.6);
        # Inre ruta
        $this->Line(static::$x_min, static::$y_min, $this->x_max, static::$y_min);
        $this->Line(static::$x_min, static::$y_min, static::$x_min, $this->y_max);
        $this->Line(static::$x_min, $this->y_max, $this->x_max, $this->y_max);
        $this->Line($this->x_max, static::$y_min, $this->x_max, $this->y_max);
        
        # Yttre ruta
        $space = static::$frame_space; #1.2;
        
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
        
        $this->current_y = static::$y_min + static::$Margin;
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
        # Bra ställe lägga ut debuginfo i början på rapporten
//         $text = $this->GetPageWidth() . " - $text"; # 210 för A4 Portrait
//         $text = strlen(utf8_decode($text)) . " - $text"; # Antalet tecken

        $font_size = 85; # Så det inte blir för stort i X-led
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $field_with = $this->x_max - static::$x_min - (2*static::$Margin); # Bredden på rutan som kan fyllas med rubriken
        while ($this->GetStringWidth($text) > $field_with*0.85) { # Se till så inte rubrik-rutan får för lång text.
            $font_size -=1;
            $this->SetFont('Helvetica','B', $font_size);
        }

        $this->SetXY($this->left_positions[0], $this->current_y-1);
        $this->Cell(0, static::$cell_y*6, utf8_decode($text),0,0,'C');
        
        $this->current_y = static::$y_min + (static::$cell_y*6) + (static::$Margin);
        
        $this->bar();
    }

    
    function new_report(LARP $larp, String $name, Array $rows, bool $is_sensitive = false) {
        global $x;
        if (static::$debug) {
            echo "<hr>";
            echo "<br><br><b>New Report - $name</b><br>";
        }
        
        $this->AliasNbPages();
        
        $this->is_sensitive = $is_sensitive;
        
        $this->x_max = $this->GetPageWidth()  - static::$x_min;
        $this->y_max = $this->GetPageHeight() - static::$y_min;
        
        $this->left_positions = [];
        $this->cell_widths = [];
        
        $this->larp     = $larp;
        $this->name     = $name;
        $this->rows     = $rows;
        $this->num_cols = sizeof($this->rows[0]);
        $this->num_rows = sizeof($this->rows);
        $this->text_max_length = 94 / $this->num_cols; # Max antal tecken per kolumn (Empiriskt testat för vad som ser bra ut)
        
        $this->cell_height = static::$cell_y + (2*static::$Margin);
        
        $current_left = static::$x_min ;
        
        $this->left_positions[0] = $current_left; # Vänstraste vänstermarginalen

        # Hur mycket text får det max plats på en rad. Två marginaler per text
        $total_width_for_text = $this->x_max - static::$x_min - (2*$this->num_cols*static::$Margin);
        
        $total_with_all_max_widths = 10000;
        
        $this->text_fontsize = static::$max_font_size + 1;
        
        # Beräkna fornt size och kolumnbredder
        while ($total_with_all_max_widths > $total_width_for_text && $this->text_fontsize > static::$min_font_size) {
            $this->text_fontsize -= 1;
            if (static::$debug) {
                echo "<br><b>Tryout Fontsize: $this->text_fontsize</b><br>";
            }
            
            if (static::$debug) {
                echo "<br>ROWS in report:<br>\n";
                print_r($this->rows);
            }
            
            # Markera extra breda kolumner
            # Beräkna också maxvidden på en rad text.
            $max_length = [];
            $max_col_text_width = [];
            $row_nr = 0;
            $this->SetFont('Helvetica', 'B', $this->text_fontsize);
            foreach($this->rows as $row){
                $column_nr = 0;
                foreach($row as $cell_text){
                    if (!isset($max_length[$column_nr]))         $max_length[$column_nr] = 0;
                    if (!isset($max_col_text_width[$column_nr])) $max_col_text_width[$column_nr] = 0;
                    if (strlen($cell_text) > $max_length[$column_nr]) $max_length[$column_nr] = strlen($cell_text);
                    $text_rows_in_cell = explode("\n", $cell_text);
                    foreach($text_rows_in_cell as $text_row_in_cell) {
                        $text_width = $this->GetStringWidth($text_row_in_cell)+2;
//                         if (($text_width > $max_col_text_width[$column]) && ($text_width < ($total_width_for_text / 2 ))) {
                        if ($text_width > $max_col_text_width[$column_nr]) { 
                            # Om vi har en ny längstatext i en kolumn och kolumnen inte tar upp mer än halva bredden på rapporten
                            if (static::$debug) {
                              echo "New Max for col $column_nr : Nytt max $text_width : Old Max $max_col_text_width[$column_nr] : $text_row_in_cell<br>";
                            }
                            $max_col_text_width[$column_nr] = $text_width;
                        }
                    }
                    $column_nr++;
                }
                $this->SetFont('Helvetica', '', $this->text_fontsize);
            }
            
            if (static::$debug) {
                echo "<br><br>MAX TEXT WIDTHS in report<br>\n";
                print_r($max_col_text_width);
            }
            
            $total_with_all_max_widths = 0;
            for ($column_nr = 0; $column_nr < $this->num_cols; $column_nr++){
                $total_with_all_max_widths += $max_col_text_width[$column_nr];
            }
            
            if (static::$debug) {
                echo "<br><br>  Total max with : $total_with_all_max_widths <br>\n";
                echo "    Total text area is : $total_width_for_text <br>\n";
            }
            
        }
        
        if (static::$debug) 
            echo "<b>Klar sätta font_size $this->text_fontsize</b><br>";
        
        $widest_column = 0;
        for ($column_nr = 0; $column_nr < $this->num_cols; $column_nr++){
            if ($max_col_text_width[$column_nr] > $max_col_text_width[$widest_column]) $widest_column = $column_nr; # Hitta bredaste kolumnen
        }
        # Hur stor andel utgör varje kolumnms text av all text
        $with_part = [];
        for ($column_nr = 0; $column_nr < $this->num_cols; $column_nr++){
            $with_part[$column_nr] = $max_col_text_width[$column_nr] / $total_with_all_max_widths; # Hur stor andel utgör den av all text
            if ($this->num_cols > 3 && $with_part[$column_nr] > 0.4) $with_part[$column_nr] = 0.4;
        }
        
        # Minska bredaste kolumnen om andra inte får plats
        $avg_part = 0;
        $column_nr = [];
        $changes = [];
        for ($column_nr = 0; $column_nr < $this->num_cols; $column_nr++){
            $this_cell_widths = round($total_width_for_text * $with_part[$column_nr]);
            $how_much_dont_fit[] = $max_col_text_width[$column_nr] - $this_cell_widths;
            
            $fits_in_column = $how_much_dont_fit[$column_nr] < 0; # ($this_cell_widths > $max_col_text_width[$column_nr]);
            $text_fit_in_column_before[] = $fits_in_column ? 'Ja' : 'NEJ';
            
            if ($column_nr != $widest_column && !$fits_in_column) {
                # Gör eventuellt andra kolumner bredare på bredaste kolumnens bekostnad
                # Hantera om små kolumner inte får plats med sitt data  
                $change = ($with_part[$widest_column] * 0.1);
                if (($with_part[$column_nr] + $change) < ($with_part[$widest_column] - $change)) {
                    $with_part[$widest_column] -= $change;
                    $with_part[$column_nr] += $change;
                    $changes[] = $change;
                }
            } else {
                $changes[] = 0;
            }
            $avg_part += $with_part[$column_nr]; 
        }
        
        # Justera om vi inte får 1 i totalt medevärde av bredderna
        for ($column_nr = 0; $column_nr < $this->num_cols; $column_nr++) $with_part[$column_nr] = $with_part[$column_nr]/$avg_part;
        
        # Sätt alla kolumnbredder
        for ($column_nr = 0; $column_nr < $this->num_cols; $column_nr++){
            $this->cell_widths[$column_nr] = round($total_width_for_text * $with_part[$column_nr]);
        }
        for ($column_nr = 0; $column_nr < $this->num_cols; $column_nr++){
            $text_fit_in_column_after[] = ($this->cell_widths[$column_nr] > $max_col_text_width[$column_nr]) ? 'Ja' : 'NEJ';
        }
        
        if (static::$debug) {
            echo "   Antalet kolumner är : $this->num_cols <br>\n";
            echo "      Widest column is : $widest_column <br>\n";
            echo "<br>with_part:\n";
            print_r($with_part);
            echo "with_part --<br>\n";
            echo "    Genomsnitlig width : $avg_part <br>\n";
        }
        
        if (static::$debug) {
            echo "<br>max_col_text_width :\n";
            print_r($max_col_text_width);
            echo "max_col_text_width --\n";
            
            echo "<br>with_part:\n";
            print_r($with_part);
            echo "with_part --\n";
            
            echo "<br>cell_widths :\n";
            print_r($this->cell_widths);
            echo "Cell widths --<br>\n";
        }
        
//         $this->rows[] = ["TOT: $total_width_for_text", "widest $widest_column"];
//         $this->rows[] = ["Max col text width",'','','',''];
//         $this->rows[] = $max_col_text_width;
//         $this->rows[] = ["with_part",'','','',''];
//         $this->rows[] = $with_part;
//         $this->rows[] = ["Cell text width",'','','',''];
//         $this->rows[] = $this->cell_widths;
//         $this->rows[] = ["Får rum före",'','','',''];
//         $this->rows[] = $text_fit_in_column_before;
//         $this->rows[] = ["Hur mycket får inte plats",'','','',''];
//         $this->rows[] = $how_much_dont_fit;
//         $this->rows[] = $changes;
//         $this->rows[] = ["Får rum efter",'','','',''];
//         $this->rows[] = $text_fit_in_column_after;
        
        
        # Beräkna vänster-marginaler
        for ($column_nr = 1; $column_nr < $this->num_cols; $column_nr++){
            $this->left_positions[$column_nr] = $current_left + $this->cell_widths[$column_nr-1] + static::$Margin*2;
            $current_left = $this->left_positions[$column_nr];
        }
//         print_r($this->lefts);
//         echo "Lefts 2 --<br>\n";
        
        $this->AddPage($this->CurOrientation);
        
        $this->title($this->name);
        
        $this->current_cell_height = $this->cell_height;
        
        # Skriv ut cellerna
        $max_lines_per_row = [];
        $rubrik = true;
        $row_nr = 0;
        $first_row = $this->rows[0];
        foreach($this->rows as $row){
            $max_lines_per_row[] = 0;
            foreach($row as $cell_text) {
//                 $cell_text = "$row_nr - $max_lines_per_row[$row_nr] - $cell_text";
                $number_lines = $this->NbLines($this->cell_widths[$this->current_col], $cell_text);
                if ($number_lines > $max_lines_per_row[$row_nr]) $max_lines_per_row[$row_nr] = $number_lines;
//                 $cell_text = "Row $row_nr : $number_lines : MAX $max_lines_per_row[$row_nr] : $cell_text";
                $this->set_cell($cell_text, $rubrik);
                # Räkna upp en cell i bredd
                $this->current_col += 1;
                
                if ($this->num_cols == $this->current_col) {
                    # Sista cellen i en rad
                    
                    # Dra alla mellanstrecken
                    for ($col = 0; $col < $this->num_cols; $col++) $this->mittlinje($col);
                    
                    $this->current_col = 0;
                    $this->current_y += $this->current_cell_height;
                    $this->bar();
                    
                    $predicted_cell_height = ($max_lines_per_row[$row_nr]*static::$cell_y) + (static::$Margin*2);
                    if ($this->current_y > ($this->PageBreakTrigger - $predicted_cell_height) && (($row_nr+1) < $this->num_rows)) {
                        $this->AddPage($this->CurOrientation); # Ny sidan om vi är längst ner
                        $this->current_y += 5;
                        $this->bar();
                        # Skriv ut rubrikerna igen
                        $this->current_cell_height = $this->cell_height;
                        foreach($first_row as $header_text) {
                            $this->set_cell($header_text, true);
                            # Räkna upp en cell i bredd
                            $this->current_col += 1;
                        }
                        for ($col = 0; $col < $this->num_cols; $col++) $this->mittlinje($col);
                        $this->current_col = 0;
                        $this->current_y += $this->current_cell_height;
                        $this->bar();
                    }
                    
                    $this->current_cell_height = $this->cell_height;
                }
            }
            $rubrik = false;
            $row_nr += 1;
        }
	}
	
	# Dynamiska småfält
	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    $this->Line(static::$x_min, $this->current_y, $this->x_max, $this->current_y);
	}
	
	private function mittlinje($col) {
	    $x_pos = $this->left_positions[$col]; # $this->current_col];
	    $down = $this->current_y + $this->current_cell_height;
	    $this->Line($x_pos, $this->current_y, $x_pos, $down);
	}

	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_cell($text, $bold) {
	    if (empty($text)) $text = ' ';
	    
	    $text = trim(utf8_decode($text));
//  	    $text = "$text - $this->PageBreakTrigger";
   
	    $bold_char = $bold ? 'B' : '';
	   
	    # Specialbehandling för väldigt långa strängar så dom blir lite mindre och får lite, lite bättre plats
	    $to_long = false;
	    $text_rows_in_cell = explode("\n", $text);
	    foreach($text_rows_in_cell as $text_row_in_cell) {
	        if ($this->GetStringWidth($text_row_in_cell) > $this->cell_widths[$this->current_col]) $to_long = true;
	    }
	    if ($to_long){
 	        $this->SetFont('Arial', $bold_char, ($this->text_fontsize-1));
 	    } else {
 	        $this->SetFont('Helvetica', $bold_char, $this->text_fontsize);
        }
    
	    $x_location = $this->left_positions[$this->current_col]+static::$Margin;
	    
	    # Normal utskrift
	    $this->SetXY($x_location, $this->current_y + static::$Margin + 1);
	    
	    # Skriv ut texten i cellen
	    # MultiCell(float w, float h, string txt [, mixed border [, string align [, boolean fill]]])
	    $res = $this->MultiCell($this->cell_widths[$this->current_col], static::$cell_y, $text, 0, 'L', false);

	    # Hantering om resultatet av cellen är för stort för att få plats på en rad
	    # Beräkna nya cellhöjden för den här raden
        $current_y = $this->GetY();
        if ($current_y > $this->current_y + $this->current_cell_height) {
            $new_height = $current_y - $this->current_y;
            $this->current_cell_height = $new_height;
            # Efterjustera mittlinjen om det behövs            
        }
	    return;
	}
	
	function NbLines($w, $txt)
	{
	    // Compute the number of lines a MultiCell of width w will take
	    if(!isset($this->CurrentFont)) $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if($w==0) $w = $this->w-$this->rMargin-$this->x;
        
        
        $wmax = ($w-2*$this->cMargin) * 1000/$this->FontSize;
        $s = str_replace("\r",'',(string)$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
            $sep = -1;
            $i = 0;
            $j = 0;
            $l = 0;
            $nl = 1;
            while($i<$nb)
            {
                $c = $s[$i];
                if($c=="\n")
                {
                    $i++;
                    $sep = -1;
                    $j = $i;
                    $l = 0;
                    $nl++;
                    continue;
                }
                if($c==' ')
                    $sep = $i;
                    $l += $cw[$c];
                    if($l>$wmax)
                    {
                        if($sep==-1)
                        {
                            if($i==$j)
                                $i++;
                        }
                        else
                            $i = $sep+1;
                            $sep = -1;
                            $j = $i;
                            $l = 0;
                            $nl++;
                    }
                    else
                        $i++;
            }
            return $nl;
	}
	
}
