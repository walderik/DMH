<?php
# Läs mer på http://www.fpdf.org/

# För avgöra om intriger skall skrivas ut:
# $larp->isIntriguesReleased()

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';


class CharacterSheet_PDF extends PDF_MemImage {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $role;
    public $isReserve;
    public $person;
    public $isMyslajvare;
    public $larp;
    public $all;
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
        if ($this->all) {
            $txt = $this->role->Name;
        } else {
            $txt = 'Karaktärsblad';
        }

        $this->MultiCell(30, 4, encode_utf_to_iso($txt), 0, 'C', true);
        
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
        
        $this->bar();
    }
    
    # Namnen på karaktär och spelare
    function names($left, $left2) {
        global $y, $mitten;
        
        $persn = $this->person;        
        $this->set_header($left, 'Spelare');
        
        if (empty($persn)) {
            $namn = 'Okänd (?)';
        } else {
            $age = $persn->getAgeAtLarp($this->larp);
            $namn = "$persn->Name ($age)";
        }
        $this->set_text($left, $namn);
        
        $this->mittlinje();
   
        $this->SetXY($left2, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
        if ($this->isReserve) {
            $type = 'RESERV';
        } if ($this->role->IsDead) {
            $type = 'Avliden';
            $this->cross_over();
        } else {
            $type = ($this->role->isMain($this->larp)) ? 'Huvudkaraktär' : 'Sidokaraktär';
        }
         
        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso($type),0,0,'L');
        
        $font_size = (strlen($this->role->Name)>20) ? 14 : 24;
        $this->SetXY($left2, $y + static::$Margin + 1);
        $this->SetFont('Helvetica','B', $font_size); # Extra stora bokstäver på karaktärens namn
       
        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso($this->role->Name),0,0,'L');
    }

    
    function beskrivning() {
        global $y;
        $text = $this->role->Description;
        $this->set_rest_of_page('Beskrivning', $text);
        return true;
    }
    
    function intrigues($new_page = true) {
        global $y, $left, $lovest_y;
        
        $space = 3;
        
        # Kolla först att det finns intriger och att någon har en intrigtext
        $intrigues = Intrigue::getAllIntriguesForRole($this->role->Id, $this->larp->Id);
        $subdivisions = Subdivision::allForRole($this->role);
        
        if (empty($intrigues) && empty($subdivisions)) return true;
        
        $tomma_intriger = true;
        foreach ($intrigues as $intrigue) {
            if (!$intrigue->isActive()) continue;
            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $this->role);  
            $role = $intrigueActor->getRole();
            $isInSubdivision = $role->inSubdivisionInIntrigue($intrigue);
            
            if (!empty($intrigueActor->IntrigueText) || !empty($intrigueActor->OffInfo) || (!$isInSubdivision && !empty($intrigue->CommonText))) $tomma_intriger = false;
        }
        foreach ($subdivisions as $subdivision) {
            $subdivisionIntrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $this->larp->Id);
            foreach ($subdivisionIntrigues as $intrigue) {
                if (!$intrigue->isActive()) continue;
                $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
                if (!empty($intrigueActor->IntrigueText) || !empty($intrigueActor->OffInfo) || !empty($intrigue->CommonText)) $tomma_intriger = false;
            }
        }
                
        if ($tomma_intriger) return true;
  
        if ($new_page) $this->AddPage();
        else $y = $this->GetY()+$space*3;
        $this->SetXY($left, $y);
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Intriger'),0,0,'L');
        $y = $this->GetY()+$space*3;
        $this->SetXY($left, $y);
        
        $this->SetFont('Arial','',static::$text_fontsize);
        
        $intrigue_numbers = array();
        
        foreach ($intrigues as $intrigue) {
            if (!$intrigue->isActive()) continue;
            
            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $this->role);
            if (empty($intrigueActor)) continue;

            $role = $intrigueActor->getRole();
            $isInSubdivision = $role->inSubdivisionInIntrigue($intrigue);
            
            if (!empty($intrigueActor->IntrigueText) || !empty($intrigueActor->OffInfo) || (!$isInSubdivision && !empty($intrigue->CommonText))) {
                $intrigue_numbers[$intrigue->Number] = $intrigue->Number;
                $this->printIntrigue($intrigue, $intrigueActor, true);
                
                $this->shortbar();
                $y += 2;
                $this->SetXY($left, $y);
            }
            
            
        }
        $known_groups = $this->role->getAllKnownGroups($this->larp);
        $known_roles = $this->role->getAllKnownRoles($this->larp);
        $known_npcgroups = $this->role->getAllKnownNPCGroups($this->larp);
        $known_npcs = $this->role->getAllKnownNPCs($this->larp);
        $known_props = $this->role->getAllKnownProps($this->larp);
        

        foreach ($subdivisions as $subdivision) {
            $subdivisionIntrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $this->larp->Id);
            foreach ($subdivisionIntrigues as $intrigue) {
                if ($intrigue->isActive()) {
                    $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
                    if (empty($intrigueActor)) continue;
                    
                    if (!empty($intrigueActor->IntrigueText) || !empty($intrigueActor->OffInfo) || !empty($intrigue->CommonText)) {
                        $intrigue_numbers[$intrigue->Number] = $intrigue->Number;
                    }
                    if ($subdivision->isVisibleToParticipants()) {
                        $this->SetXY($left, $y);
                        $this->SetFont('Helvetica','B',static::$text_fontsize);
                        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('För '.$subdivision->Name),0,0,'L');
                        $y = $this->GetY()+$space*2;
                        $this->SetXY($left, $y);
                        $this->SetFont('Helvetica','',static::$text_fontsize);
                        
                    }
                    $this->printIntrigue($intrigue, $intrigueActor, true);

                    $this->shortbar();
                    $y += 2;
                    $this->SetXY($left, $y);
                    
                }
            }
            
            $known_groups = array_merge($known_groups,$subdivision->getAllKnownGroups($this->larp));
            $known_roles = array_merge($known_roles,$subdivision->getAllKnownRoles($this->larp));
            
            $known_npcgroups = array_merge($known_npcgroups,$subdivision->getAllKnownNPCGroups($this->larp));
            $known_npcs = array_merge($known_npcs,$subdivision->getAllKnownNPCs($this->larp));
            $known_props = array_merge($known_props,$subdivision->getAllKnownProps($this->larp));
        }

        natsort($intrigue_numbers);
        
        if (!empty($intrigue_numbers)) {
            //$this->bar();
            $y = $this->GetY()+$space;
            $this->SetXY($left, $y);
            $this->SetFont('Arial','',static::$text_fontsize-3);
            $text = encode_utf_to_iso("Intrignummer: " . implode(', ', $intrigue_numbers).".\nDet/dem kan behövas om du behöver hjälp av arrangörerna med en intrig under lajvet");
            $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
            $this->SetFont('Arial','',static::$text_fontsize);
            $y = $this->GetY() + $space;
            $this->SetXY($left, $y);
        }
        
        
        
        # Dom man känner till från intrigerna
        if (!empty($known_groups) || !empty($known_roles) || !empty($known_npcgroups || !empty($known_npcs) || !empty($known_props))) {
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
                $this->print_know_stuff($text, $image);
            }
            
            foreach ($known_npcgroups as $known_npcgroup) {
                $image = null;
                $npcgroup = $known_npcgroup->getIntrigueNPCGroup()->getNPCGroup();
                $this->print_know_stuff("$npcgroup->Name - NPC-grupp", $image);
            }
            foreach ($known_npcs as $known_npc) {
                $image = null;
                $npc = $known_npc->getIntrigueNPC()->getNPC();
                $text = "$npc->Name - NPC";
                $npc_group = $npc->getNPCGroup();
                if (!empty($npc_group)) $text .="\n\r($npc_group->Name)";
                if ($npc->hasImage()) $image = Image::loadById($npc->ImageId);
                $this->print_know_stuff($text, $image);
            }
            foreach ($known_props as $known_prop) {
                $image = null;
                $prop = $known_prop->getIntrigueProp()->getProp();
                if ($prop->hasImage()) $image = Image::loadById($prop->ImageId);
                $this->print_know_stuff($prop->Name, $image);
            }
        }
        $y = $this->GetY();
        if ($lovest_y > $y) $y = $lovest_y;
        return true;
    }
    
    protected function printIntrigue(Intrigue $intrigue, IntrigueActor $intrigueActor, bool $showOffInfo) {
        global $left, $y;
        $space = 3;
        
        if ($intrigueActor->isRoleActor()) {
            $role = $intrigueActor->getRole();
            $isInSubdivision = $role->inSubdivisionInIntrigue($intrigue);
        } else $isInSubdivision = false;
        
        if (!empty($intrigue->CommonText) && !$isInSubdivision) {
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
        
        if ($showOffInfo && !empty($intrigueActor->OffInfo)) {
            $text = trim(encode_utf_to_iso("OFF_INFORMATION: " . $intrigueActor->OffInfo));
            $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
            $y = $this->GetY() + $space;
            $this->SetXY($left, $y);
        }
        
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
        if (($new_y) > $lovest_y) $lovest_y = $new_y + $rowImageHeight;
        
        # Räkna upp en cell i bredd
        if ($this->current_left == $left) {
            $this->current_left = $left2;
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
    
    function rumours($new_page = true){
        global $y, $left;
        $space = 3;
        
        $rumours = Rumour::allKnownByRole($this->larp, $this->role);
        if (empty($rumours)) return true;
        
        $y = $y + 2*$space;
        if ($new_page) $this->AddPage();
        $this->SetXY($left, $y);
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $name = $this->role->Name;
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
    
    
    function history(){
        global $y, $left, $left2;
        $space = 3;
        
        $previous_larps = $this->role->getPreviousLarps();
        if (isset($previous_larps) && count($previous_larps) > 0) {
            
            foreach ($previous_larps as $prevoius_larp) {
                $this->AddPage();
                
                
                $previous_larp_role = LARP_Role::loadByIds($this->role->Id, $prevoius_larp->Id);
                $this->title($left, "Historik $prevoius_larp->Name");
                
                $this->names($left, $left2);
                
                $y += $this->cell_y_space;
                $this->bar();
                
                $y += 3;
                
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','B',static::$text_fontsize);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Intrig'),0,0,'L');
                
                $y = $this->GetY() + $space*3;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','',static::$text_fontsize);
                
                
                if (!empty($previous_larp_role->Intrigue)) {
                    $text = trim(encode_utf_to_iso($previous_larp_role->Intrigue));
                    $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                    $y = $this->GetY() + $space;
                    $this->SetXY($left, $y);
                    
                    $this->bar();
                    $y += 3;
                    
                }
                
                $intrigues = Intrigue::getAllIntriguesForRole($this->role->Id, $prevoius_larp->Id);
                foreach($intrigues as $intrigue) {
                    $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $this->role);
                    if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
                        /*
                        $this->current_left = $left;
                        $this->SetXY($this->current_left, $y);
                        $this->SetFont('Helvetica','B',static::$text_fontsize);
                        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Intrig'),0,0,'L');
                        */
                        $y = $this->GetY() + $space;
                        $this->SetXY($this->current_left, $y);
                        $this->SetFont('Helvetica','',static::$text_fontsize);
                        
                        $this->printIntrigue($intrigue, $intrigueActor, false);
                        
                        
                        $this->current_left = $left;
                        $this->SetXY($this->current_left, $y);
                        $this->SetFont('Helvetica','B',static::$text_fontsize);
                        $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Vad hände?'),0,0,'L');
                        
                        $y = $this->GetY() + $space*3;
                        $this->SetXY($this->current_left, $y);
                        $this->SetFont('Helvetica','',static::$text_fontsize);
                        
                        $text = encode_utf_to_iso(!empty($intrigueActor->WhatHappened) ? $intrigueActor->WhatHappened : "Inget rapporterat");
                        $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                        $y = $this->GetY() + $space;
                        $this->SetXY($left, $y);
                        
                        $this->bar();
                        $y += 3;
                        
                        
                    }
                }
                
                $subdivisions = Subdivision::allForRole($this->role);
                foreach ($subdivisions as $subdivision) {
                    $subdivisionIntrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $prevoius_larp->Id);
                    foreach ($subdivisionIntrigues as $intrigue) {
                        if ($intrigue->isActive()) {
                            $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
                            if (empty($intrigueActor)) continue;
                            
                            
                            if ($subdivision->isVisibleToParticipants()) {
                                $this->SetXY($left, $y);
                                $this->SetFont('Helvetica','B',static::$text_fontsize);
                                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('För '.$subdivision->Name),0,0,'L');
                                $y = $this->GetY()+$space*2;
                                $this->SetXY($left, $y);
                                $this->SetFont('Helvetica','',static::$text_fontsize);
                                
                            }
                            
                            $this->printIntrigue($intrigue, $intrigueActor, false);
                            
                            
                            $this->current_left = $left;
                            $this->SetXY($this->current_left, $y);
                            $this->SetFont('Helvetica','B',static::$text_fontsize);
                            $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso('Vad hände?'),0,0,'L');
                            
                            $y = $this->GetY() + $space*3;
                            $this->SetXY($this->current_left, $y);
                            $this->SetFont('Helvetica','',static::$text_fontsize);
                            
                            $text = encode_utf_to_iso(!empty($intrigueActor->WhatHappened) ? $intrigueActor->WhatHappened : "Inget rapporterat");
                            $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                            $y = $this->GetY() + $space;
                            $this->SetXY($left, $y);
                            
                            $this->bar();
                            $y += 3;
                            
                            
                            
                        }
                    }
                    
                }
                
                
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','B',static::$text_fontsize);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Vad hände för ".$this->role->Name."?"),0,0,'L');
                
                $y = $this->GetY() + $space*3;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','',static::$text_fontsize);
                
                $text = encode_utf_to_iso((isset($previous_larp_role->WhatHappened) && $previous_larp_role->WhatHappened != "") ? $previous_larp_role->WhatHappened : "Inget rapporterat");
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
                
                
                $this->current_left = $left;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','B',static::$text_fontsize);
                $this->Cell($this->cell_width, static::$cell_y, encode_utf_to_iso("Vad hände för andra?"),0,0,'L');
                
                $y = $this->GetY() + $space*3;
                $this->SetXY($this->current_left, $y);
                $this->SetFont('Helvetica','',static::$text_fontsize);
                
                $text = encode_utf_to_iso((isset($previous_larp_role->WhatHappendToOthers) && $previous_larp_role->WhatHappendToOthers != "") ? $previous_larp_role->WhatHappendToOthers : "Inget rapporterat");
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
                
            }
        
        }
    }
    
    # Skriv bara ut intriger och rykten
    function intrigue_info(Role $role_in, LARP $larp_in){
        global $current_user, $x, $y, $left, $left2, $mitten;
        
        $this->role = $role_in;
        $this->person = $this->role->getPerson();
        $this->isMyslajvare = $this->role->isMysLajvare();
        $this->larp = $larp_in;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->current_cell_height = $this->cell_y_space;
        
        $this->isReserve = Reserve_LARP_Role::isReserve($this->role->Id, $this->larp->Id);
        
        # Säkerställer att bara arrngörer någonsin kan få se all info om en karaktär
        if (!(AccessControl::hasAccessLarp($current_user, $larp_in))) return;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $this->cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $left;
        
        $this->AddPage();
        
        $this->title($left);
        $this->names($left, $left2);
        
        $y += $this->cell_y_space;
        
        $this->bar();
        
        $y += 3;
        
        if (!$this->isMyslajvare && !$this->isReserve) {
            $this->intrigues(false);
            $this->rumours(false);
        }
    }
    
    function new_character_sheet(Role $role_in, LARP $larp_in, bool $all_information=false) {
        global $current_user, $x, $y, $left, $left2, $mitten;
        $space = 3;
        
        $this->role = $role_in;
        $this->person = $this->role->getPerson();
        $this->isMyslajvare = $this->role->isMysLajvare();
        $this->larp = $larp_in;
        $this->all = $all_information;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->current_cell_height = $this->cell_y_space;
        
        $this->isReserve = Reserve_LARP_Role::isReserve($this->role->Id, $this->larp->Id);
        
        # Säkerställer att bara arrngörer någonsin kan få se all info om en karaktär
        if ($this->all && !(AccessControl::hasAccessLarp($current_user, $larp_in))) $this->all = false;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $this->cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);     
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $left;
        
        $this->AddPage();
        
        $this->title($left);
        $this->names($left, $left2);
        
        $y += $this->cell_y_space;
        
        $this->bar();
        
        # Uppräkning av ett antal fält som kan finnas eller inte
        $this->draw_field('epost');
        $this->draw_field('group');
        $this->draw_field('erfarenhet');
        $this->draw_field('yrke');
        $this->draw_field('lajvar_typ');
        $this->draw_field('rikedom');
        if ($this->all) $this->draw_field('intrigtyper');
        $this->draw_field('birth_place');
        if ($this->all) $this->draw_field('intrigsuggestions');
        $this->draw_field('bor');
        if ($this->all) $this->draw_field('notOkIntrigues');
        $this->draw_field('reason_for_being_in_here');
        
        $this->draw_field('race');
        $this->draw_field('ability');
        $this->draw_field('religion');
        $this->draw_field('council');
        $this->draw_field('guard');
        
        if ($this->all) $this->draw_field('darkSecret');
        if ($this->all) $this->draw_field('darkSecretSuggestion');
        
        if ($this->all) $this->draw_field('charactersWithRelations');
        if ($this->all) $this->draw_field('tidigareLajv');
       
        if ($this->all) $this->draw_field('organizerNotes');
        
        # Fixa till om vi skapat ett udda antal fält
        if ($this->current_left == $left2) $this->draw_field('empty');
        
        $this->beskrivning();


        if (!$this->isMyslajvare && !$this->isReserve && ($this->larp->isIntriguesReleased() || $this->all)) {
            $this->intrigues(false);
            $this->rumours(false);
        }
        
        $this->history();
        
	}
	
	function all_character_sheets(LARP $larp_in, bool $bara_intrig, ?bool $all_info=true) {
	    $this->larp = $larp_in;

	    $roles = $this->larp->getAllMainRoles(false);
	    foreach($roles as $role) {
	        if ($bara_intrig) {
                $this->intrigue_info($role, $larp_in);
	        } else {
	            $this->new_character_sheet($role, $larp_in, $all_info);
	        }
	    }
	    $roles = $this->larp->getAllNotMainRoles(false);
	    foreach($roles as $role) {
	        if ($bara_intrig) {
	           $this->intrigue_info($role, $larp_in);
	        } else {
	            $this->new_character_sheet($role, $larp_in, $all_info);
	        }
	    }
	}

	
	function selected_character_sheets($roles, LARP $larp_in, bool $bara_intrig, ?bool $all_info=true) {
	    foreach($roles as $role) {
	        if ($bara_intrig) {
	            $this->intrigue_info($role, $larp_in);
	        } else {
	            $this->new_character_sheet($role, $larp_in, $all_info);
	        }
	    }
	}
	
	
	
	# Dynamiska småfält
	
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function yrke($left) {
	    $this->set_header($left, 'Yrke');
	    $this->set_text($left, $this->role->Profession);
	    return true;
	}
	
	protected function epost($left) {
	    $this->set_header($left, 'Epost');
	    if (empty($this->person)) return true;
	    $this->set_text($left, $this->person->Email);
	    return true;
	}
	
	protected function erfarenhet($left) {
	    $this->set_header($left, 'Erfarenhet');
	    if (empty($this->person)) return true;
	    $this->set_text($left, $this->person->getExperience()->Name);
	    return true;
	}
	
	protected function rikedom($left) {
	    if (!Wealth::isInUse($this->larp)) return false;
	    
	    if ($this->isMyslajvare) return false;
	    $wea = $this->role->getWealth();
	    if (empty($wea)) $txt = "OBS! Rikedom saknas";
	    else $txt = $wea->Name;
	    $this->set_header($left, 'Rikedom');
	    $this->set_text($left, $txt);
	    return true;
	}
	
	protected function intrigtyper($left) {
	    if (!IntrigueType::isInUse($this->larp)) return false;
	    
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Intrigtyper');

	    $text = commaStringFromArrayObject($this->role->getIntrigueTypes());
	    $this->set_text($left, $text);
	    return true;
	}
	
	protected function intrigsuggestions($left) {  
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Intrigförslag');

	    $this->set_text($left, $this->role->IntrigueSuggestions);
	    return true;
	}
	
	protected function notOkIntrigues($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Inget spel på');
	    $this->set_text($left, $this->role->NotAcceptableIntrigues);
	    return true;
	}
	
	protected function darkSecret($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Mörk hemlighet');
	    $this->set_text($left, $this->role->DarkSecret);
	    return true;
	}
	
	protected function darkSecretSuggestion($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Spel på Mörk hemlighet');
	    $this->set_text($left, $this->role->DarkSecretIntrigueIdeas);
	    return true;
	}
	
	protected function charactersWithRelations($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Viktigare personer');
	    $this->set_text($left, $this->role->CharactersWithRelations);
	    return true;
	}
	
	protected function tidigareLajv($left){
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Tidigare lajv');
	    $this->set_text($left, $this->role->PreviousLarps);
	    return true;
	}

	
	protected function OrganizerNotes($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Anteckning');
	    $this->set_text($left, $this->role->OrganizerNotes);
	    return true;
	}
	
	protected function lajvar_typ($left) {
	    if (!LarperType::isInUse($this->larp)) return false;
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Lajvartyp');
	    if (empty($this->person)) return true;
	    $mertext = (empty(trim($this->role->TypeOfLarperComment))) ? '' : " (".trim($this->role->TypeOfLarperComment).")";
	    $text = $this->role->getLarperType()->Name.$mertext;
	    $this->set_text($left, $text );
	    return true;
	}
	
	protected function group($left) {
	    $this->set_header($left, 'Grupp');
	    $group = $this->role->getGroup();
	    if (empty($group)) return true;
	    $this->set_text($left, $group->Name);
	    return true;
	}
	
	protected function birth_place($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Född');
	    $this->set_text($left, $this->role->Birthplace);
	    return true;
	}
	
	protected function bor($left) {
	    if (!PlaceOfResidence::isInUse($this->larp)) return false;
	    if ($this->isMyslajvare) return false;
	    $por = $this->role->getPlaceOfResidence();
	    if (empty($por)) $txt = "OBS! Boplats saknas";
	    else $txt = $por->Name;
	    $this->set_header($left, 'Bor');
	    $this->set_text($left, $txt);
	    return true;
	}
	
	
	protected function race($left) {
	    if (!Race::isInUse($this->larp)) return false;
	    $this->set_header($left, 'Ras');
	    $mertext = (empty(trim($this->role->RaceComment))) ? '' : " (".trim($this->role->RaceComment).")";
	    $text = $this->role->getRace()->Name.$mertext;
	    $this->set_text($left, $text );
	    return true;
	}
	
	
	
	protected function ability($left) {
	    if (!Ability::isInUse($this->larp)) return false;
	    
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Kunskap');

	    $mertext = (empty(trim($this->role->AbilityComment))) ? '' : " (".trim($this->role->AbilityComment).")";
	    
	    $text = commaStringFromArrayObject($this->role->getAbilities()).$mertext;
	    $this->set_text($left, $text);
	    return true;
	}
	
	protected function religion($left) {
	    if (!Religion::isInUse($this->larp)) return false;
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Religion');
	    $mertext = (empty(trim($this->role->Religion))) ? '' : " (".trim($this->role->Religion).")";
	    $text = $this->role->getReligion()->Name.$mertext;
	    $this->set_text($left, $text );
	    return true;
	}
	

	protected function council($left) {
	    if (!Council::isInUse($this->larp)) return false;
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Byråd');
	    $mertext = (empty(trim($this->role->Council))) ? '' : " (".trim($this->role->Council).")";
	    $text = $this->role->getCouncil()->Name.$mertext;
	    $this->set_text($left, $text );
	    return true;
	}
	
	protected function guard($left) {
	    if (!Guard::isInUse($this->larp)) return false;
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Markvakt');
	    $text = $this->role->getGuard()->Name;
	    $this->set_text($left, $text );
	    return true;
	}
	
	
	
	
	protected function reason_for_being_in_here($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Orsak för att vistas här');
	    $this->set_text($left, $this->role->ReasonForBeingInSlowRiver);
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
