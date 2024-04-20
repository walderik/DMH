function calc_points(obj, val) {
	//alert("calc_points");
	var sum_points_element = document.getElementById('points');
	//alert (sum_points_element.innerHTML);
	var sum_points = Number(sum_points_element.innerHTML);

	//alert (sum_points);
	//alert(val);
	if (obj.checked) sum_points_element.innerHTML = sum_points + val;
	else sum_points_element.innerHTML = sum_points - val;
	
}
