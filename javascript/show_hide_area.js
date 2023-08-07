<style>


.hidden {
  display: none;
}


</style>
<script>

function hopp() {
	alert('hopp');
}

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


</script>