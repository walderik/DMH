<?php
# Läs mer på http://www.fpdf.org/

# För avgöra om intriger skall skrivas ut:
# $larp->isIntriguesReleased()

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';


class AlchemyAlchemistSheet_PDF extends PDF_MemImage {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $alchemist;
    public $role;
    public $person;
    public $larp;
    public $current_left;
    public $cell_y_space;        # Standardhöjden på en cell 
    public $current_cell_height; # Nuvarande höjden på den här radens celler
    public $cell_width;
    
    
    public $handfonts = ['cherish','dancingscript','daniel','dawningofanewday','ekologiehand','homemadeapple','mynerve',
        'reeniebeanie','simplyglamorous','splash','sueellenfrancisco','zeyada'];
    
    public $calligraphyfonts = ['BelweGotisch', 'AliceInWonderland', 'DSCaslonGotisch', 'eaglelake', 'UncialAntiqua'];
    
    
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
        
        $boxWidth = 80;
        $this->SetXY($mitten-$boxWidth/2, 3);
        $this->SetFont('Helvetica','',static::$text_fontsize/1.1);
        $this->SetFillColor(255,255,255);

        if (isset($this->alchemist)) $txt = 'Alkemist '.$this->alchemist->getRole()->Name;
        else $txt = 'Alkemist';
       
        

        $this->MultiCell($boxWidth, 4, encode_utf_to_iso($txt), 0, 'C', true);
        
        $this->SetXY($mitten-15,  static::$y_min + 2*static::$Margin);
        $y = $this->GetY();
        $rowImageHeight = 0;
        $lovest_y = $y;
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title($left, ?String $text = null) {
        global $y;

        if (empty($text)) $text =  $this->larp->Name;
        
        $font_size = (850 / strlen(encode_utf_to_iso($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, encode_utf_to_iso($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        //$this->bar();
    }
    
    function single_alchemist_sheet(Alchemy_Alchemist $alchemist_in, LARP $larp_in) {
        global $x, $y, $left, $left2, $mitten;
        $space = 3;
        
        $this->alchemist = $alchemist_in;
        $this->role = $this->alchemist->getRole();
        
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
         
        $y += 5;
        
        $this->bar();
        
        # Uppräkning av ett antal fält som kan finnas eller inte
        $this->draw_field('alchemisttype');
        $this->draw_field('level');
        $this->draw_field('teacher');
        $this->draw_field('students');
        
        
        # Fixa till om vi skapat ett udda antal fält
        if ($this->current_left == $left2) $this->draw_field('empty');
        
        $y = $this->GetY() + static::$cell_y + $space*4;
 
        
        $recipes = $this->alchemist->getRecipes(true);
        if (!empty($recipes)) {
            $this->current_left = $left;
            $this->SetXY($this->current_left, $y);
            $this->SetFont('Helvetica','B',static::$text_fontsize);
            $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Dina recept"),0,0,'L');
            
            
            //Först en lista med alla recept
            $recipenames = "";
            foreach ($recipes as $recipe) {
                $recipenames .= $recipe->Name . " (nivå $recipe->Level)\n\n";
            }
            $y = $this->GetY() + $space*2;
            $this->SetXY($this->current_left, $y);
            $this->SetFont('Helvetica','',static::$text_fontsize);
            $text = trim(encode_utf_to_iso($recipenames));
            $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
            
        }
        
        //Lista alla lövjeristerna och var de bor (Namn Grupp, Boende)
        $suppliers = Alchemy_Supplier::allByComingToLarp($this->larp);
        
        if (!empty($suppliers)) {
            $this->current_left = $left;
            $y = $this->GetY() + $space*5;
            $this->SetXY($this->current_left, $y);
            $this->SetFont('Helvetica','B',static::$text_fontsize);
            $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Alla lövjerister"),0,0,'L');
            
            
            
            $list = "";
            foreach ($suppliers as $supplier) {
                $supplier_role = $supplier->getRole();
                $list .= $supplier_role->Name;
                $group = $supplier_role->getGroup();
                if (isset($group)) $list .= " i $group->Name";
                $supplier_person = $supplier_role->getPerson();
                if (!is_null($supplier_person)) {
                    $housing = $supplier_role->getPerson()->getHouseAtLarp($this->larp);
                    if (isset($housing) && ($this->larp->DisplayHousing==1)) $list .= " Bor: $housing->Name";
                }
                $list .= "\n\n";
            }
            $y = $this->GetY() + $space*2;
            $this->SetXY($this->current_left, $y);
            $this->SetFont('Helvetica','',static::$text_fontsize);
            $text = trim(encode_utf_to_iso($list));
            $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
            
        }
        
        
        
         if (!empty($recipes)) {
        //Sen alla recept i detalj
             $recipefont = 'eaglelake';
             $this->AddFont($recipefont);
             
             foreach ($recipes as $recipe) {
                $y = 0;
                $this->AddPage();
                
                $recipe_fontsize = static::$text_fontsize-2;

                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize+2);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso($recipe->Name),0,0,'L');
                
                $y = $this->GetY() + $space*2;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize);
                $text = trim(encode_utf_to_iso("Nivå ".$recipe->Level));
                $this->Cell($this->cell_width, static::$cell_y,$text,0,0,'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
                
                //Ingredienser
                $txt = "";
                if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
                    $ingredients = $recipe->getSelectedIngredients();
                    foreach ($ingredients as $ingredient) {
                        $txt .= "$ingredient->Name (Nivå $ingredient->Level)";
                        if ($ingredient->isCatalyst()) $txt .= " - Katalysator";
                        $txt .= "\n\n";
                    }
                } elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
                    
                    $essences = Alchemy_Essence::all();
                    
                    $selectedEssences = $recipe->getSelectedEssenceIds();
                    foreach($selectedEssences as $selectedEssenceArr) {
                        $selectedEssence = null;
                        foreach ($essences as $essence) {
                            if ($essence->Id == $selectedEssenceArr[0]) {
                                $selectedEssence = $essence;
                                break;
                            }
                        }
                        
                        $txt .= "$selectedEssence->Name (Nivå ".$selectedEssenceArr[1].")\n\n";
                    }
                    $txt .= "Katalysator (Nivå $recipe->Level)";
                }

                $y = $this->GetY() + $space*3;
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize+2);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Tillverkas av"),0,0,'L');
                
                
                
                $y = $this->GetY() + $space*2;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize);
                $text = trim(encode_utf_to_iso($txt));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
                
                    
                
                $y = $this->GetY() + $space*3;
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize+2);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Beskrivning"),0,0,'L');
                
                $y = $this->GetY() + $space*2;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize);
                $text = trim(encode_utf_to_iso($recipe->Description));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
                
                
                
                $y = $this->GetY() + $space*3;
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize+2);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Beredning"),0,0,'L');
                
                $y = $this->GetY() + $space*2;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize);
                $text = trim(encode_utf_to_iso($recipe->Preparation));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
 
                $y = $this->GetY() + $space*3;
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize+2);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Effekt"),0,0,'L');
                
                $y = $this->GetY() + $space*2;
                $this->SetXY($this->current_left, $y);
                $this->SetFont($recipefont,'',$recipe_fontsize);
                $text = trim(encode_utf_to_iso($recipe->Effect));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
                
                
                if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
                    $y = $this->GetY() + $space*3;
                    $this->current_left = $left;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont($recipefont,'',$recipe_fontsize+2);
                    $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Bieffekt"),0,0,'L');
                    
                    $y = $this->GetY() + $space*2;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont($recipefont,'',$recipe_fontsize);
                    $text = trim(encode_utf_to_iso($recipe->SideEffect));
                    $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                    $y = $this->GetY() + $space;
                    $this->SetXY($left, $y);
                }
                    
            }
        }
        
         

        
            
                
 	}
	
	function all_alchemist_sheets(LARP $larp_in) {
	    $this->larp = $larp_in;

	    $alchemists = Alchemy_Alchemist::allByComingToLarp($larp_in);
	    foreach($alchemists as $alchemist) {
	        $this->single_alchemist_sheet($alchemist, $larp_in);
	    }
	}

	
	
	# Dynamiska småfält
	
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function alchemisttype($left) {
	    $this->set_header($left, 'Alkemisttyp');
	    
	    $this->set_text($left, $this->alchemist->getAlchemistType());
	    return true;
	}
	
	protected function level($left) {
	    $this->set_header($left, 'Nivå');
	    $this->set_text($left, $this->alchemist->Level);
	    return true;
	}
	
	protected function teacher($left) {
	    $this->set_header($left, 'Lärare');
	    $teacher = $this->alchemist->getTeacher();
	    if (empty($teacher)) return true;
	    $this->set_text($left, $teacher->getRole()->Name);
	    return true;
	}
	
	protected function students($left) {
	    $students = $this->alchemist->getStudents();
	    $this->set_header($left, 'Elever');
	    if (empty($students)) return;
	    
	    $student_names = array();
	    foreach($students as $student) {
	        $str = $student->getRole()->Name." (";
	        $str.="nivå $student->Level)";
	        $student_names[] = $str;
	    }
	    $txt = implode(", ", $student_names);

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
	    $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso($text),0,0,'L');
	}
	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_text($venster, $text) {
	    global $y;
	    
	    if (empty($text)) return;
	    
	    $text = trim(encode_utf_to_iso($text));
	    
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
	    
	    $text = encode_utf_to_iso($text);
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
	    
	    $text = encode_utf_to_iso($text);
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
