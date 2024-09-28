<?php

// Include the main TCPDF library (search for installation path).


global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/tcpdf/tcpdf.php';

// extend TCPF with custom functions
class Report_TCP_PDF extends Tcpdf {
    public static $debug = false;
    
    
    private $isSensitive;
    
    public static $max_font_size = 15; # Så stor font som rapporten kan få om allt får plats jättebra
    public static $min_font_size = 9;  # Mindre än så kan man inte läsa
    
    public $text_fontsize;
     
    // Page footer
    public function Footer() {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Sidan '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        
        # Om det är ett dokument med känsliga uppgifter
        if ($this->isSensitive) {
            // Go to 1.5 cm from bottom
            $this->SetY(-15);
            $this->Cell(0, 10, 'Det här dokumentet innehåller känsliga personuppgifter. Förstör det efter lajvet.', 0, 0, 'L');
        }
    }
    
    
    public function init($author, $title, $larpname, $isSensitive) {
        // set document information
        $this->SetCreator('Omnes Mundi');
        $this->SetAuthor($author);
        $this->SetTitle($title);
        $this->SetSubject($title);
        $this->SetKeywords($title);
        
        $this->isSensitive = $isSensitive;
        if ($isSensitive) {
            $this->SetProtection(array('modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'));
        }
        
        // set default header data
        $this->SetHeaderData("", 0, $title, $larpname);
        
        // set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $this->SetAutoPageBreak(FALSE);
        
        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        
        // ---------------------------------------------------------
        
        // set font
        $this->SetFont('helvetica', '', 12);
        
    }
    
    
    // Colored table
    public function Table($headline, $header,$data, ?array $colWidths = NULL, ?bool $doHTML=false) {
        // set font
        $this->SetFont('helvetica', 'B', 20);
        
        $this->Write(0, $headline, '', 0, 'C', true, 0, false, false, 0);
        
        $this->SetFont('helvetica', '', 10);
        
        if ($doHTML) $this->HTMLTable($headline, $header, $data, $colWidths);
        else $this->ColoredTable($headline, $header, $data);
    }
    
    // Colored table
    public function ColoredTable($headline, $header,$data) {
        $w = $this->calculateWidths(array_merge(array($header), $data));
        
        // Colors, line width and bold font
        $this->SetFillColor(100, 100, 100);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 128, 128);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B',$this->text_fontsize);
        // Header
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $this->MultiRow($data, $w, 6);
        /*
        foreach($data as $row) {
            $this->MultiRow($row, $w, 6);
            //for($i = 0; $i < $num_headers; ++$i) {
            //    $this->Cell($w[$i], 6, $row[$i], 'LR', 0, 'L', $fill);
            //}
            $this->Ln();
            $fill=!$fill;
        }
        */
    }
    
    
    private function MultiRow($data, $widths, $size) {
        $dimensions = $this->getPageDimensions();
        $hasBorder = false; //flag for fringe case
        $fill = 0;
        $numCols = count($data[0]);
        
        foreach($data as $row) {
            $rowcount = 0;
            
            $rowcount = 0;
            for($i = 0; $i < $numCols; ++$i) $rowcount = max($rowcount, $this->getNumLines($row[$i], $widths[$i]));
            //work out the number of lines required
            
            $startY = $this->GetY();
            
            if (($startY + $rowcount * 6) + $dimensions['bm']+5 > ($dimensions['hk'])) {
                //this row will cause a page break, draw the bottom border on previous row and give this a top border
                //we could force a page break and rewrite grid headings here
                if ($hasBorder) {
                    $hasBorder = false;
                } else {
                    $this->Cell(array_sum($widths), 0, '', 'T');  //draw bottom border on previous row
                    $this->AddPage();
                    $this->Ln();
                }
                $borders = 'LTR';
            } elseif ((ceil($startY) + $rowcount * 6) + $dimensions['bm'] == floor($dimensions['hk'])) {
                //fringe case where this cell will just reach the page break
                //draw the cell with a bottom border as we cannot draw it otherwise
                $borders = 'LRB';
                $hasBorder = true; //stops the attempt to draw the bottom border on the next row
            } else {
                //normal cell
                $borders = 'LR';
            }
            
            //now draw it
            for($i = 0; $i < $numCols; ++$i) {
                $this->MultiCell($widths[$i],$rowcount * $size,$row[$i],$borders,'L',$fill,0);
            }
            //$this->MultiCell(80,$rowcount * 6,$row['cell2data'],$borders,'L',0,0);
            //$this->MultiCell(80,$rowcount * 6,$row['cell3data'],$borders,'L',0,0);
            
            $this->Ln();
            $fill=!$fill;
        }
        
        $this->Cell(array_sum($widths), 0, '', 'T');  //last bottom border
    }
    
    
    
    public function HTMLTable($headline, $header,$data, ?array $colWidths = NULL ) {
        
        $setWidth = false;
        $numberOfCols = sizeof($header);
        if (isset($colWidths) && is_array($colWidths) && sizeof($colWidths)==$numberOfCols) $setWidth = true;
        
        $html = '<table style="width: 95%;" cellpadding="3" cellspacing="0">';
        $html = $html . '<thead><tr bgcolor="#CCCCCC">';
        
        foreach($header as $key=>$headertext) {
            $html .= '<td ';
            if ($setWidth && $key<$numberOfCols) $html .= 'width="'.$colWidths[$key].'" ';
            $html .= 'style="font-weight: bold; align: center;">'.$headertext.'</td>';
        }
        $html .= '</tr></thead>';
        foreach($data as $row){
            $html .= '<tr nobr="true">';
            foreach ($row as $key=>$item) {
                $html .= '<td ';
                if ($setWidth && $key<$numberOfCols) $html .= 'width="'.$colWidths[$key].'" ';
                $html .= 'style="border: 1px solid #000000;">'.$item.'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        
        //print_r($html);
        // output the HTML content
        $this->writeHTML($html, true, false, false, false, '');
        
    }
    
    private function calculateWidths($allRows) {
        $current_left = PDF_MARGIN_LEFT;
        
        $x_max = $this->GetPageWidth()  - PDF_MARGIN_LEFT;
        $y_max = $this->GetPageHeight() - PDF_MARGIN_TOP;
        $num_cols = sizeof($allRows[0]);
        
        
        $this->left_positions = [];
        $this->cell_widths = [];
        
        
        $this->left_positions[0] = $current_left; # Vänstraste vänstermarginalen
        
        # Hur mycket text får det max plats på en rad. Två marginaler per text
        $total_width_for_text = $x_max - PDF_MARGIN_LEFT - (2*$num_cols);
        
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
            foreach($allRows as $row){
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
            for ($column_nr = 0; $column_nr < $num_cols; $column_nr++){
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
            for ($column_nr = 0; $column_nr < $num_cols; $column_nr++){
                if ($max_col_text_width[$column_nr] > $max_col_text_width[$widest_column]) $widest_column = $column_nr; # Hitta bredaste kolumnen
            }
            # Hur stor andel utgör varje kolumnms text av all text
            $with_part = [];
            for ($column_nr = 0; $column_nr < $num_cols; $column_nr++){
                $with_part[$column_nr] = $max_col_text_width[$column_nr] / $total_with_all_max_widths; # Hur stor andel utgör den av all text
                if ($num_cols > 3 && $with_part[$column_nr] > 0.4) $with_part[$column_nr] = 0.4;
            }
            
            # Minska bredaste kolumnen om andra inte får plats
            $avg_part = 0;
            $column_nr = [];
            $changes = [];
            for ($column_nr = 0; $column_nr < $num_cols; $column_nr++){
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
            for ($column_nr = 0; $column_nr < $num_cols; $column_nr++) $with_part[$column_nr] = $with_part[$column_nr]/$avg_part;
            
            # Sätt alla kolumnbredder
            for ($column_nr = 0; $column_nr < $num_cols; $column_nr++){
                $this->cell_widths[$column_nr] = round($total_width_for_text * $with_part[$column_nr]);
            }
            for ($column_nr = 0; $column_nr < $num_cols; $column_nr++){
                $text_fit_in_column_after[] = ($this->cell_widths[$column_nr] > $max_col_text_width[$column_nr]) ? 'Ja' : 'NEJ';
            }
            
            if (static::$debug) {
                echo "   Antalet kolumner är : $num_cols <br>\n";
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
            
            /*
            # Beräkna vänster-marginaler
            for ($column_nr = 1; $column_nr < $num_cols; $column_nr++){
                $this->left_positions[$column_nr] = $current_left + $this->cell_widths[$column_nr-1] + static::$Margin*2;
                $current_left = $this->left_positions[$column_nr];
            }
            */
            return $this->cell_widths;
    }
    
}

