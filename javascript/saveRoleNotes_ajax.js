function saveNotesForRole(textArea) {

	var text = textArea.value;

	var ids = textArea.id.split(":");
	var roleId = ids[1];

	var textbr = text.replace(/\r\n|\r|\n/g, "\n");
	var callString = "../ajax/saveRoleNotes.php?roleId=" + roleId + "&text=" + encodeURIComponent(textbr);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", callString, true);
	xmlhttp.send();
}

