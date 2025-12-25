<?php

?>




<!-- A modal dialog containing a form -->
<dialog id="favDialog">
  <form>
    <p>
      <label>
        Välj karaktär:
        <select>
          <option>Brine shrimp</option>
          <option>Red panda</option>
          <option>Spider monkey</option>
        </select>
      </label>
      
      <br>
      <div class='itemname'>Karaktär&nbsp;<font style='color:red'>*</font></div>
Vilken karaktär vill du spela på lajvet?<br>
<select  name='roleId' id='roleId'>
   <option value='1539' >Namn Namn</option>
</select>
<br>
<div class='itemname'>Intrigideer&nbsp;</div>
Är det någon typ av spel du särskilt önskar eller något som du inte önskar spel på?  Exempel kan vara 'Min karaktär har en skuld till en icke namngiven karaktär/mördat någon/svikit sin familj/ett oäkta barn/lurat flera personer på pengar.'<br>
<textarea type='text' id='IntrigueIdeas' name='IntrigueIdeas' rows='4' maxlength='60000'></textarea>
</div>

<div class='itemcontainer '>
<div class='itemname'>Intrigtyper&nbsp;</div>
Vilken typ av intriger vill du ha?<br>
<table class='selectionDropdown'>
<tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType15' name='IntrigueTypeId[]' value='15' >
<label for='IntrigueType15'>Diplomati</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType16' name='IntrigueTypeId[]' class="IntrigueTypeId" value='16' >
<label for='IntrigueType16'>Handel</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType17' name='IntrigueTypeId[]' class="IntrigueTypeId" value='17' >
<label for='IntrigueType17'>Kriminalitet</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType18' name='IntrigueTypeId[]' class="IntrigueTypeId" value='18' >
<label for='IntrigueType18'>Lägerhäng</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType19' name='IntrigueTypeId[]' class="IntrigueTypeId" value='19' >
<label for='IntrigueType19'>Magi och alkemi</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType20' name='IntrigueTypeId[]' class="IntrigueTypeId" value='20' >
<label for='IntrigueType20'>Relationer</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType21' name='IntrigueTypeId[]' class="IntrigueTypeId" value='21' >
<label for='IntrigueType21'>Religion</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType22' name='IntrigueTypeId[]' class="IntrigueTypeId" value='22' >
<label for='IntrigueType22'>Skattjakt (barn)</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType23' name='IntrigueTypeId[]' class="IntrigueTypeId" value='23' >
<label for='IntrigueType23'>Sociala tillställningar</label><br>
</td><td> </td></tr><tr><td  style='white-space: nowrap;'><input type='checkbox' id='IntrigueType24' name='IntrigueTypeId[]' class="IntrigueTypeId" value='24' >
<label for='IntrigueType24'>Strid</label><br>
</td><td> </td></tr></table>
</details></div>


    </p>
    <div>
      <button value="cancel" formmethod="dialog">Cancel</button>
      <button id="confirmBtn" value="default">Confirm</button>
    </div>
  </form>
</dialog>
<p>
  <button id="showDialog">Show the dialog</button>
</p>
<output></output>


<script>
const showButton = document.getElementById("showDialog");
const favDialog = document.getElementById("favDialog");
const outputBox = document.querySelector("output");
const roleEl = favDialog.querySelector("#roleId");
const inrigueIdeasEl = favDialog.querySelector("#IntrigueIdeas");
const IntrigueTypeEl = favDialog.querySelector(".IntrigueTypeId");
const confirmBtn = favDialog.querySelector("#confirmBtn");

// "Show the dialog" button opens the <dialog> modally
showButton.addEventListener("click", () => {
  favDialog.showModal();
});

// "Cancel" button closes the dialog without submitting because of [formmethod="dialog"], triggering a close event.
favDialog.addEventListener("close", (e) => {
  outputBox.value =
    favDialog.returnValue === "default"
      ? "No return value."
      : `ReturnValue: ${favDialog.returnValue}.`; // Have to check for "default" rather than empty string
});

// Prevent the "confirm" button from the default behavior of submitting the form, and close the dialog with the `close()` method, which triggers the "close" event.
confirmBtn.addEventListener("click", (event) => {
  event.preventDefault(); // We don't want to submit this fake form
  favDialog.close(roleEl.value+"#"+IntrigueTypeEl.value); // Have to send the select box value here.
});



</script>