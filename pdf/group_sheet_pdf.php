<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';


class Group_PDF extends PDF_MemImage {
    
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
        $this->MultiCell(30, 4, encode_utf_to_iso($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title($left, $text) {
        global $y;

        $font_size = (850 / strlen(encode_utf_to_iso($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, encode_utf_to_iso($text),0,0,'C');
        
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
        
        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso($this->group->Name),0,0,'L');
        
        $this->mittlinje();
        
        $persn = $this->person;        
        $this->set_header($left2, 'Ansvarig');
        if (!empty($persn)) {
            $this->set_text($left2, encode_utf_to_iso($persn->Name));
        }
        
    }

    
    function beskrivning() {
        global $y;
        $text = $this->group->Description;
        $this->set_rest_of_page('Beskrivning', $text);
        return true;
    }
    
    function intrigues() {
        global $y, $left, $lovest_y;
        
        $space = 3;
        
        # Kolla först att det finns intriger och att någon har en intrigtext
        $intrigues = Intrigue::getAllIntriguesForGroup($this->group->Id, $this->larp->Id);
        if (empty($intrigues)) return true;
        
        $tomma_intriger = true;
        foreach ($intrigues as $intrigue) {
            if (!$intrigue->isActive()) continue;
            $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $this->group);
            if (!empty($intrigueActor->IntrigueText) || !empty($intrigueActor->OffInfo) || !empty($intrigue->CommonText)) $tomma_intriger = false;
        }
        if ($tomma_intriger) return true;
        
        
        //$this->AddPage();
        $this->SetXY($left, $y);
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Intriger'),0,0,'L');
        $y = $this->GetY()+$space*3;
        $this->SetXY($left, $y);
        
        $this->SetFont('Arial','',static::$text_fontsize);
        
        //point to end of the array
        end($intrigues);
        //fetch key of the last element of the array.
        $lastElementKey = key($intrigues);

        
        foreach ($intrigues as $key => $intrigue) {
            if (!$intrigue->isActive()) continue;
            
            $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $this->group);
            if (empty($intrigueActor)) continue;
            
            if (!empty($intrigueActor->IntrigueText) || !empty($intrigueActor->OffInfo) || !empty($intrigue->CommonText)) {
                $text = trim(encode_utf_to_iso("Intrig ".$intrigue->Number.":"));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
            }
            
            if (!empty($intrigue->CommonText)) {
                $text = trim(encode_utf_to_iso($intrigue->CommonText));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
            }
            
            if (!empty($intrigueActor->IntrigueText)) {
                $text = trim(encode_utf_to_iso($intrigueActor->IntrigueText));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
            }
            
            if (!empty($intrigueActor->OffInfo)) {
                $text = trim(encode_utf_to_iso("OFF_INFORMATION: " . $intrigueActor->OffInfo));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
            }
            
            $this->shortbar();
            $y += 2;
            $this->SetXY($left, $y);
            
        }
        
        $known_groups = $this->group->getAllKnownGroups($this->larp);
        $known_roles = $this->group->getAllKnownRoles($this->larp);
        $known_props = $this->group->getAllKnownProps($this->larp);
        
        
        # Dom man känner till från intrigerna
        if (!empty($known_groups) || !empty($known_roles) || !empty($known_props)) {
            $this->bar();
            $y = $this->GetY()+$space*2;
            
            $this->current_left = $left;
            $this->SetXY($this->current_left, $y);
            $this->SetFont('Helvetica','B',static::$text_fontsize);
            $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Känner till'),0,0,'L');
            
            $y = $this->GetY() + $space*3;
            $this->SetXY($this->current_left, $y);
            $this->SetFont('Helvetica','',static::$text_fontsize);
            $rowImageHeight = 0;
            $lovest_y = $y;
            foreach ($known_groups as $group) {
                $image = null;
                if ($group->hasImage()) $image = Image::loadById($group->ImageId);
                $this->print_know_stuff($group->Name, $image);
            }

            foreach ($known_roles as $role) {
                $image = null;
                if ($role->hasImage()) $image = Image::loadById($role->ImageId);
                $text = $role->Name; #, $y, $lovest_y, $realHeight, ".$this->GetPageHeight();
                $role_group = $role->getGroup();
                if (!empty($role_group)) $text .= "\n\r($role_group->Name)";
                if ($role->isPC($this->larp) && !$role->isRegistered($current_larp))  $text .= "\n\rSpelas inte";
                elseif ($role->isNPC($this->larp) && !$role->isAssigned($current_larp))  $text .= "\n\rSpelas inte";
                
                $this->print_know_stuff($text, $image);
            }
            
            foreach ($known_props as $known_prop) {
                $image = null;
                $prop = $known_prop->getIntrigueProp()->getProp();
                if ($prop->hasImage()) $image = Image::loadById($prop->ImageId);
                $this->print_know_stuff($prop->Name, $image);
            }
            
            if ($lovest_y > $y) $y = $lovest_y;
            
        }
        
        
        return true;
    }
    
    protected function print_know_stuff($text, $image){
        global $y, $left, $left2, $rowImageHeight, $lovest_y;
        $space = 3;
        $image_width = 25;
        $realHeight = 0;
        
        if ($y + 40 > $this->GetPageHeight()) {
            $lovest_y = 0;
            $this->AddPage();
        }
        if ($this->current_left == $left) {
            $rowImageHeight = 0;
        }
        if (isset($image)) {
            $v = 'img'.md5($image->file_data);
            $GLOBALS[$v] = $image->file_data;
            list($width, $new_y) =  getimagesize('var://'.$v);
            
            $realHeight = round(($new_y / $width) * $image_width);
            if ($realHeight > $rowImageHeight) $rowImageHeight = $realHeight;
            $this->MemImage($image->file_data, $this->current_left, $y, $image_width);
            $this->SetXY($this->current_left + 25, $y); # Skjut texten till höger
        }
        
        $this->MultiCell(0, static::$cell_y-1, trim(encode_utf_to_iso($text)), 0, 'L');
        $new_y = $this->GetY() + $space;
        if (($new_y) > $lovest_y) $lovest_y = $new_y; // + $rowImageHeight;
        
        # Räkna upp en cell i bredd
        if ($this->current_left == $left) {
            $this->current_left = $left2;
            if (($new_y + $rowImageHeight) > $lovest_y) $lovest_y = $new_y + $rowImageHeight;
        } else {
            # Vi har just ritat den högra rutan
            $this->current_left = $left;
            $y = $this->GetY() + $space;
            if ($lovest_y > $y) $y = $lovest_y;
            $y += $rowImageHeight;
            $rowImageHeight = 0;
            $lovest_y = $y;
        }
        $this->SetXY($this->current_left, $y);
    }
    
    function rumours(){
        global $y, $left;
        $space = 3;
        
        $rumours = Rumour::allKnownByGroup($this->larp, $this->group);
        if (empty($rumours)) return true;
        
        //$this->AddPage();
        $this->SetXY($left, $y);
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $name = $this->group->Name;
        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Rykten $name känner till "),0,0,'L');
        $this->SetFont('Arial','',static::$text_fontsize-3);
        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("(Hjälp gärna till att sprida och reagera på dom) "),0,0,'L');
        
        $this->SetFont('Arial','',static::$text_fontsize);
        $y = $this->GetY()+$space*3;
        $this->SetXY($left, $y);
        
        foreach($rumours as $rumour) {
            $this->MultiCell(0, static::$cell_y-1, trim(encode_utf_to_iso($rumour->Text)), 0, 'L');
            $y = $this->GetY()+$space;
            $this->SetXY($left, $y);
        }
        
    }
    
    
    function new_group_sheet(Group $group_in, LARP $larp_in, bool $all_in=false, ?bool $no_history=false) {
        global $x, $y, $left, $left2, $mitten;
        $space = 3;
        
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
        
        if ($this->larp->isIntriguesReleased() || $this->all) {
            $this->intrigues();
            $this->rumours();
        }
        
        


        if (!$no_history) {
        
            $previous_larps = $this->group->getPreviousLarps($this->larp);
            if (isset($previous_larps) && count($previous_larps) > 0) {
                
                
                foreach ($previous_larps as $prevoius_larp) {
                    $this->AddPage();
                    
                    $previous_larp_group = LARP_Group::loadByIds($this->group->Id, $prevoius_larp->Id);
    
                    $this->title($left, "Historik $prevoius_larp->Name");
                    $this->names($left, $left2);
                    $y += $this->cell_y_space;
                    $this->bar();
                    
                    $y += 3;
                    
                    
                    
                    
                    if (!empty($previous_larp_group->Intrigue)) {
                        $this->current_left = $left;
                        $this->SetXY($this->current_left, $y);
                        $this->SetFont('Helvetica','B',static::$text_fontsize);
                        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Intrig'),0,0,'L');
                        
                        $y = $this->GetY() + $space*3;
                        $this->SetXY($this->current_left, $y);
                        $this->SetFont('Helvetica','',static::$text_fontsize);
                        
                        
                        $text = trim(encode_utf_to_iso($previous_larp_group->Intrigue));
                        $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                        $y = $this->GetY() + $space;
                        $this->SetXY($left, $y);
                    
                        $this->bar();
                        $y += 3;
                    }
                    
                    $intrigues = Intrigue::getAllIntriguesForGroup($this->group->Id, $prevoius_larp->Id);
                    foreach($intrigues as $intrigue) {
                        $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $this->group);
                        if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
                            
                            $this->current_left = $left;
                            $this->SetXY($this->current_left, $y);
                            $this->SetFont('Helvetica','B',static::$text_fontsize);
                            $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Intrig'),0,0,'L');
                            
                            $y = $this->GetY() + $space*3;
                            $this->SetXY($this->current_left, $y);
                            $this->SetFont('Helvetica','',static::$text_fontsize);
                            
                            
                            $text = trim(encode_utf_to_iso($intrigueActor->IntrigueText));
                            $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                            $y = $this->GetY() + $space;
                            $this->SetXY($left, $y);
                            
                            
                            
                            $this->current_left = $left;
                            $this->SetXY($this->current_left, $y);
                            $this->SetFont('Helvetica','B',static::$text_fontsize);
                            $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Vad hände?'),0,0,'L');
                            
                            $y = $this->GetY() + $space*3;
                            $this->SetXY($this->current_left, $y);
                            $this->SetFont('Helvetica','',static::$text_fontsize);
                            
                            
                            $text = (isset($intrigueActor->WhatHappened) && $intrigueActor->WhatHappened != "") ? $intrigueActor->WhatHappened : "Inget rapporterat";
                            $this->MultiCell(0, static::$cell_y-1, encode_utf_to_iso($text), 0, 'L');
                            $y = $this->GetY() + $space;
                            $this->SetXY($left, $y);
                            
                            $this->bar();
                            $y += 3;
                            
                        }
                    }
                    
                    
                    $this->current_left = $left;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont('Helvetica','B',static::$text_fontsize);
                    $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Vad hände för ".$this->group->Name."?"),0,0,'L');
                    
                    $y = $this->GetY() + $space*3;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont('Helvetica','',static::$text_fontsize);
                    
                    $text = (isset($previous_larp_group->WhatHappened) && $previous_larp_group->WhatHappened != "") ? $previous_larp_group->WhatHappened : "Inget rapporterat";
                    $this->MultiCell(0, static::$cell_y-1, encode_utf_to_iso($text), 0, 'L');
                    $y = $this->GetY() + $space;
                    $this->SetXY($left, $y);
                    
                    
                    $this->current_left = $left;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont('Helvetica','B',static::$text_fontsize);
                    $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Vad hände för andra?"),0,0,'L');
                    
                    $y = $this->GetY() + $space*3;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont('Helvetica','',static::$text_fontsize);
                    
                    $text = (isset($previous_larp_group->WhatHappendToOthers) && $previous_larp_group->WhatHappendToOthers != "") ? $previous_larp_group->WhatHappendToOthers : "Inget rapporterat";
                    $this->MultiCell(0, static::$cell_y-1, encode_utf_to_iso($text), 0, 'L');
                    $y = $this->GetY() + $space;
                    $this->SetXY($left, $y);

                    $this->current_left = $left;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont('Helvetica','B',static::$text_fontsize);
                    $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Vad händer efter lajvet?"),0,0,'L');
                    
                    $y = $this->GetY() + $space*3;
                    $this->SetXY($this->current_left, $y);
                    $this->SetFont('Helvetica','',static::$text_fontsize);
                    
                    $text = (isset($previous_larp_group->WhatHappensAfterLarp) && $previous_larp_group->WhatHappensAfterLarp != "") ? $previous_larp_group->WhatHappensAfterLarp : "Inget rapporterat";
                    $this->MultiCell(0, static::$cell_y-1, encode_utf_to_iso($text), 0, 'L');
                    $y = $this->GetY() + $space;
                    $this->SetXY($left, $y);
                }
                
            }
        }
        
	}
	
	# Skriv bara ut intriger och rykten
	function intrigue_info(Group $group_in, LARP $larp_in){
	    global $x, $y, $left, $left2, $mitten;
	    $space = 3;
	    
	    $this->group = $group_in;
	    $this->person = $this->group->getPerson();
	    
	    $this->larp = $larp_in;
	    
	    $this->larp_group = LARP_Group::loadByIds($this->group->Id, $this->larp->Id);
	    
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
	    
	    
	    $y += 3;
	    
	    $this->intrigues();
	    $this->rumours();
	}
	
	
	function even($number) {
	    if ($number % 2 == 0) {
	        return true;
	    }
	    return false;
	}
	
	
	function all_group_sheets(LARP $larp_in, $all_info, $no_history, $bara_intrig, $double_sided) {
	    $this->larp = $larp_in;

	    $groups = Group::getAllRegistered($this->larp);
	    foreach($groups as $group) {
	        if ($double_sided) {
	            if (!$this->even($this->PageNo())) $this->AddPage();
	        }
	        if ($bara_intrig) {
	            $this->intrigue_info($group, $larp_in);
	        } else {
	            
	           $this->new_group_sheet($group, $larp_in, $all_info, $no_history);
	        }
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
	    $this->set_text($left, $this->group->getWealth()->Name);
	    return true;
	}
	
	protected function want_intrigue($left) { 
	    $this->set_header($left, 'Vill gärna ha intrig');
	    if (isset($this->larp_group)) $text = $this->larp_group->WantIntrigue ? 'Ja' : 'Nej';
	    else $text = "OBS! Inte anmäld";

	    $this->set_text($left, $text);
	    return true;
	}
	
	protected function intrigtyper($left) {
	    if (!IntrigueType::isInUse($this->larp)) return false;

	    $this->set_header($left, 'Intrigtyper');

	    $text = commaStringFromArrayObject($this->group->getIntrigueTypes());
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
	    
	    if (isset($this->larp_group)) $text = $this->larp_group->RemainingIntrigues;
	    else $text = "OBS! Inte anmäld";
	    
	    
	    $this->set_text($left, $text);
	    return true;
	}

	protected function other_info($left) {
	    $this->set_header($left, 'Annat');    
	    $this->set_text($left, $this->group->OtherInformation);
	    return true;
	}

	
	protected function bor($left) {
	    if (!PlaceOfResidence::isInUse($this->larp)) return false;
	    
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
	
	private function shortbar() {
	    global $y;
	    $this->Line(static::$x_min+static::$Margin, $y, static::$x_max-static::$Margin, $y);
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
	    $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso($text),0,0,'L');
	}
	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_text($venster, $text) {
	    global $y;
	    
	    if (empty($text)) return;
	    
	    $text = trim(encode_utf_to_iso($text));
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
	    
	    $text = trim(encode_utf_to_iso($text));
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
