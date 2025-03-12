<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';
require_once $root . '/includes/FPDI-2.4.1/src/autoload.php';

class ConcatPdf extends \setasign\Fpdi\Fpdi
{
    public $files = array();
    
    public function setFiles($files)
    {
        $this->files = $files;
    }
    
    public function concat()
    {
        foreach($this->files AS $file) {
            $pageCount = $this->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pageId = $this->importPage($pageNo);
                $s = $this->getTemplatesize($pageId);
                $this->AddPage($s['orientation'], $s);
                $this->useImportedPage($pageId);
            }
        }
    }
}

$pdf = new ConcatPdf();
$pdf->setFiles(array('Boombastic-Box.pdf', 'Fantastic-Speaker.pdf', 'Noisy-Tube.pdf'));
$pdf->concat();

$pdf->Output('I', 'concat.pdf');
