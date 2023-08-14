
function show_hide_area(id, element) {

	var obj = document.getElementById(id);
	var hasClass = obj.classList.contains('hidden');
	if (hasClass) {
		obj.classList.remove('hidden');
		element.innerHTML = "<i class='fa-solid fa-caret-down'></i>";
	}
	else {
		obj.classList.add('hidden');
		element.innerHTML = "<i class='fa-solid fa-caret-left'></i>";
	}
}
