
function show_hide() {
	var rows = document.getElementsByClassName("show_hide");
	if (document.getElementById("btn_show").innerHTML == "Visa alla") {
		for (var i = 0; i < rows.length; i++) {
			rows[i].classList.remove('hidden');
			rows[i].classList.add('shown');
		}
		document.getElementById("btn_show").innerHTML = "Dölj de som inte matchar";

	}
	else {
		for (var i = 0; i < rows.length; i++) {
			rows[i].classList.remove('shown');
			rows[i].classList.add('hidden');
		}
		document.getElementById("btn_show").innerHTML = "Visa alla";
	}
}
