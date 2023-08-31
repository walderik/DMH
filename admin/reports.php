<?php
include_once 'header.php';

include 'navigation.php';
?>
<div class="content">   
    <h1>Rapporter</h1>
    <h2>Spel</h2>
    <div>
    <h3>Karaktärer och grupper</h3>

        <a href='character_sheet.php?' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla karaktärer som en stor PDF (tar tid att generera)'></i> Allt om alla karaktärer</a><br>
        <a href='group_sheet.php?' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla grupper som en stor PDF (tar tid att generera)'></i> Allt om alla grupper</a><br>
    <h3>Intriger</h3>
        <a href="reports/print_timeline_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Körschema</a><br> 
        <a href="reports/intrigues_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla intriger</a><br><br>  
        <a href='character_sheet.php?bara_intrig=japp' target='_blank'><i class='fa-solid fa-file-pdf' title='Alla intriger (tar tid att generera)'></i> Alla intriger per karaktär</a><br>
        <a href="logic/all_letters_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla godkända brev</a><br> 
        <a href="logic/all_telegrams_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla godkända telegram</a><br>  
        <a href="logic/props_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Rekvisita</a><br>
        <a href="reports/npc_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla NPC'er som ska spelas</a> 
    <h3>Handel</h3>
        <a href="logic/all_titledeeds_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Ägarbevis till lagfarterna</a><br>
        <a href="logic/all_resources_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Resurskort till lagfarterna</a><br>
        <a href="reports/titledeeds_info_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Lista med lagfarter (för notarie)</a><br>  
        <a href="reports/titledeeds_info_pdf.php?all_info=1" target="_blank"><i class="fa-solid fa-file-pdf"></i>Lista med lagfarter (för handelsansvarig)</a><br> 
        <a href="reports/resources_info_pdf.php?all_info=1" target="_blank"><i class="fa-solid fa-file-pdf"></i>Prislista</a><br><br>  


	</div>
	<h2>Praktiskt</h2>
	<div>
    <h3>Kök</h3>
    <a href="reports/matlista.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Allergier & typ av mat</a><br>  
    <h3>Boende</h3>
            <a href="reports/housing_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Boende per deltagare och per hus/lägerplats</a><br>
    <h3>Ekonomi</h3>
        <a href="reports/economy.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Rapport till kassören</a><br>
        <a href="logic/all_bookkeeping_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i> Alla verifikationer till kassören</a><br>
    	</p>
    <h3>Övrigt</h3>
		<a href="reports/check_in_out_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>In och utcheckning</a><br>
		<a href="reports/medical.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Hälsoinformation till sjukvårdare/trygghetsvärdar</a><br>
    </div>
    <h2>Övrigt</h2>
    <p>
		<a href="../includes/font_test.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla fonter</a>
    
    </p>
</div>