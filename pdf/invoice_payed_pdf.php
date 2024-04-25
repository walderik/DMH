<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';

class Invoice_payed_PDF extends Invoice_PDF {
    
    
    function Header()
    {
        global $root, $current_larp;
         $omlogo = $root . '/images/'.$current_larp->getCampaign()->Abbreviation.'_logo_vit.jpg';
         $this->Image($omlogo, 10, 10, -200);
         //Put the watermark
         $this->SetFont('Arial','B',50);
         $this->SetTextColor(255,192,203);
         $this->SetDrawColor(255,192,203);
         $this->SetXY(70, 35);
         $this->MultiCell(70,20,encode_utf_to_iso("Betalad"),1,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
    }
    
}
