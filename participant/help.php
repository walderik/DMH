<?php require 'header.php'; 
include 'navigation.php';
?>

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-questionmark"></i>
		Omnes Mundi - Hjälp
	</div>

    <div class='itemcontainer'>
    Omnes Mundi (latin: alla världar) är vårt system för att hantera anmälningar, handel, husfördelning, brev, intriger, utvärderingar mm. 
    Här beskrivs hur du använder systemet som deltagare.
    </div>
 
    <div class='itemcontainer'>
	 	<div class='itemname'>Symboler</div>
	 	<?php echo showStatusIcon(false); ?> - Något behöver göras<br>
	 	<?php echo showStatusIcon(true); ?> - Allt klart på den här punkten<br>
 	 	<i class='fa-solid fa-pen'></i> - Ändra<br>
	 	<i class='fa-solid fa-trash'></i> - Ta bort<br>
	 	<i class='fa-solid fa-image-portrait'></i> - Ladda upp bild (går bara efter anmälan). Om du vill byta bild får du först ta bort den gamla.<br>
	 	<i class='fa-solid fa-skull-crossbones'></i> - Död	

	</div>

	<div class='subheader'>Första gången</div>
    <div class='itemcontainer'>
		<div class='itemname'>1. Skapa deltagare</div>
		Under fliken "skapa" kan du välja "deltagare". Där kan du skriva in dina personuppgifter. 
		Dessa uppgifter kan sedan användas av alla lajv som använder sig av systemet. 
		Du kan ha mer än en deltagare per konto. Det vanligaste exemplet är att en vårdnadshavare kan 
		hantera sina barn på ett och samma konto.
	</div>

    <div class='itemcontainer'>
		<div class='itemname'>2. Om du är gruppledare - Skapa och anmäl grupp</div>
		Under fliken "skapa" kan du välja "grupp". Där kan du som tänkt vara gruppledare (=off-kontaktperson) 
		för en grupp skriva in uppgifterna för en grupp. Denna grupp är knuten till den valda lajvkampanjen. 
		Gruppen måste anmälas och godkännas innan någon karaktär kan välja att vara med i gruppen.
	</div>

    <div class='itemcontainer'>
		<div class='itemname'>3. Skapa karaktär</div>
		Under fliken "skapa" kan du välja "karaktär". Där kan du skapa en eller flera karaktärer. 
		Dessa karaktärer är knutna till den valda lajvkampanjen. Om karaktären är knuten till en grupp väljer du detta vid registreringen. 
	</div>

    <div class='itemcontainer'>
		<div class='itemname'>4. Anmälan till lajvet</div>
		Om lajvet är öppet kan du anmäla en eller flera karaktärer till lajvet
	</div>
	
	<div class='subheader'>Efter första gången</div>

    <div class='itemcontainer'>
		<div class='itemname'>Vidare användning</div>
        Innan du anmält dig till ett lajv kan du när som helst gå in och ändra uppgifter i systemet kopplat till deltagare, karaktär eller grupp. 
        När anmälan är klar är de anmälda karaktärerna och grupperna. Kontakta då lajvets arrangör om något behöver ändras i efterhand.<br> 
        Uppgifterna om deltagare kan sjäv redigera när något ändrar sig eller om det är något du inte vill att Berghems Vänner ska veta om 
        dig längre. Det enda du inte kan ändra är ditt personnummer. Om du behöver ändra det får du kontakta Berghems Vänner så löser vi det.<br>
        Kontrollera gärna inför varje lajv att uppgiftera fortfarande stämmer.
    </div>

    <div class='itemcontainer'>
		<div class='itemname'>Inför lajvet</div>    
        Innan lajvstart går det att gå in att se var du kommer att bo och vilka du delar hus med, vilka resurser din karaktär har samt läsa dina tilldelade intriger.
        Innan lajvet kan man också få möjlighet att lägga in rykten, brev och telegram (vilka funktioner som finns tillgängliga beror på vilket lajv det är). Rykten, brev och telegram godkänns sedan av arrangörerna och kommer sedan ut i spel på samma sätt som arrangörsskapade rykten, brev och telegram. Om lajvet har använder annonser så kan du använda det också, för att tex hitta samåkning innan lajvet.
        Det finns en lista på alla deltagare. Där kan du gå in och läsa på om karaktärer och grupper som kommer på lajvet. Om man har laddat upp ett foto på sin karaktär kommer den att synas där så att andra lättare känner igen dig på lajvet.
        Det finns även en lista på alla funktionärer så att lättare kan hitta rätt person på plats.
    </div>
    
	<div class='subheader'>Efter lajvet</div>    
    <div class='itemcontainer'>
		<div class='itemname'>Vad hände din karaktär/grupp?</div>    
		Efter lajvet kan du fylla i vad som hände dina karaktärer (och om du är gruppledare vad som hände din grupp). 
		Klicka på länken "Vad hände?" bredvid respektive karaktär eller grupp. Denna information kan du uppdatera löpande 
		fall du kommer på något du har glömt. Informationen är viktig för oss för att kunna utvärdera och spinna vidare på 
		intrigerna till nästa lajv.
	</div>
	
    <div class='itemcontainer'>
		<div class='itemname'>Utvärdering</div>    
		Efter lajvet kan du även göra vår utvärdering av lajvet. Denna utvärdering berör din upplevelse av lajvet. 
		Utvärderingen är anonym och kan endast göras en gång.
	</div>
	
		<div class='subheader'>Bilder</div>  
    <div class='itemcontainer'>
        Du kan ladda upp en bild på din karaktär. Den måste vara i jpg, gif eller png format. Den får vara max 0.5 MB. <br>
        Om du redan har en bild och vill byta ut den får du börja med att ta bort den gamla och sedan kan du ladda upp en ny bild.
    </div>


	<div class='subheader'>Liten ordförklaring</div>  
   <div class='itemcontainer'>
        En <b>deltagare</b> är en fysisk person som tänkt åka på ett lajv. Här fyller du i personuppgifter kopplat till dig själv. 
        Du kan även hantera <b>flera deltagare</b> på samma inloggningskonto. Ett typiskt exempel är en förälder som även har hand sina barns anmälningar.
	</div>

   <div class='itemcontainer'>
    En <b>gruppansvarig</b> är en deltagare som fungerar som kontaktperson åt en lajv-grupp (ex en familj, en liga med banditer, ett rallarlag). 
    I de flesta fall är det så att en gruppledare även fungerar som ledare “IN-lajv” för gruppen, men det behöver inte vara så. 
    Gruppledaren är den som sköter kontakten med arrangörsteamet och som tar emot information/intriger som inte alla i gruppen bör känna till.
	</div>

   <div class='itemcontainer'>
    En <b>karaktär</b> är en roll på lajvet. Varje deltagare kan ha flera karaktärer. T ex kan man välja att spela olika karaktärer på olika lajv. 
    Man kan också ha backup-karaktärer eller dubbel-karaktärer. 
	</div>

   <div class='itemcontainer'>
    <b>Skapa</b> betyder att man lägger in en deltagare, grupp eller karaktär i systemet. Det är <b>inte</b> samma som att anmäla sig till lajvet.<br>
    Det som är skapat kommer att ligga kvar till framtida lajv. Deltagare är gemansamt för alla kampanjer som använder systemet, medan grupper och karaktärer hör till en specifik kampanj, men kan anmälas till alla lajv i kampanjen.
	</div>

   <div class='itemcontainer'>
    För att få åka på lajvet behöver du <b>anmäla</b> (och betala för) alla deltagare som ska åka på lajvet. Det räcker inte att bara skapa karaktären.
    Vid anmälan av en deltagare väljer du vilken eller vilka karaktärer du (och andra deltagare du anmäler) vill spela under det aktuella lajvet.
	</div>
	</div>

</body>
</html>