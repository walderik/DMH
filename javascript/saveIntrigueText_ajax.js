function saveIntrigueTextForActor(textArea) {

	var text = textArea.value;

	var ids = textArea.id.split(":");
	var intrigueActorId = ids[1];

	var textbr = text.replace(/\r\n|\r|\n/g, "\n");
	var callString = "../ajax/saveIntrigueText.php?type=intrigue&intrigueActorId=" + intrigueActorId + "&text=" + encodeURIComponent(textbr);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", callString, true);
	xmlhttp.send();
}

function saveOffInfoForActor(textArea) {

	var text = textArea.value;

	var ids = textArea.id.split(":");
	var intrigueActorId = ids[1];

	var textbr = text.replace(/\r\n|\r|\n/g, "\n");
	var callString = "../ajax/saveIntrigueText.php?type=off&intrigueActorId=" + intrigueActorId + "&text=" + encodeURIComponent(textbr);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", callString, true);
	xmlhttp.send();
}

function saveOrganizerNotesForActor(textArea) {

	var text = textArea.value;

	var ids = textArea.id.split(":");
	var intrigueActorId = ids[1];

	var textbr = text.replace(/\r\n|\r|\n/g, "\n");
	var callString = "../ajax/saveIntrigueText.php?type=notes&intrigueActorId=" + intrigueActorId + "&text=" + encodeURIComponent(textbr);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", callString, true);
	xmlhttp.send();
}
