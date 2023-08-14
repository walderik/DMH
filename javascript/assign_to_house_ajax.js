
function allowDrop(ev) {
	ev.preventDefault();
}


function drag(ev) {
	ev.dataTransfer.setData("text", ev.target.id);
}

function drop_in_house(ev, house) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("text");
	var house_count = document.getElementById('count_' + house.id);
	var in_house = document.getElementById('in_' + house.id);
	var dragged_object = document.getElementById(data);

	let house_array = house.id.split('_');
	let data_array = data.split('_');
	var toHouseId = house_array[1];

	if (data_array[0] == "person") {
		var personId = data_array[1];
		var groupId = data_array[2];
		if (data_array.length > 3) {
			//Kommer från ett hus
			if (groupId == "X") {
				//Enskild som låg i hus
				var fromHouseId = data_array[3];
				var from_house_count = document.getElementById('count_house_' + fromHouseId);

				/* Flytta en person från ett hus till ett annat hus */
				var callString = "../ajax/assign_to_house.php?personId=" + personId + "&fromHouseId=" + fromHouseId + "&toHouseId=" + toHouseId;

				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var result = this.responseText.split(";");

						from_house_count.innerHTML = result[1] + " pers";
						house_count.innerHTML = result[2] + " pers";
					}
				};
				xmlhttp.open("GET", callString, true);
				xmlhttp.send();

				//Ändra id
				dragged_object.id = "person_" + personId + "_X_" + toHouseId;
				in_house.appendChild(dragged_object);
			} else {



				//Från grupp som är i hus  
				var fromHouseId = data_array[3];
				var from_group_count = document.getElementById('count_group_' + groupId + '_' + fromHouseId);
				var from_house_count = document.getElementById('count_house_' + fromHouseId);


				/* Flytta en person från en grupp i ett hus till ett annat hus */
				var callString = "../ajax/assign_to_house.php?personId=" + personId + "&fromGroupId=" + groupId + "&fromHouseId=" + fromHouseId + "&toHouseId=" + toHouseId;

				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var result = this.responseText.split(";");

						from_group_count.innerHTML = result[0] + " pers";
						from_house_count.innerHTML = result[1] + " pers";
						house_count.innerHTML = result[2] + " pers";
					}
				};
				xmlhttp.open("GET", callString, true);
				xmlhttp.send();

				//Ändra id
				dragged_object.id = "person_" + personId + "_X_" + toHouseId;
				in_house.appendChild(dragged_object);
			}
		} else {

			if (groupId == "X") {
				//Enskild som inte låg i hus, gör inget särskilt

				/* Flytta en person inte är tilldelad ett hus */
				var callString = "../ajax/assign_to_house.php?personId=" + personId + "&toHouseId=" + toHouseId;

				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var result = this.responseText.split(";");

						house_count.innerHTML = result[2] + " pers";
					}
				};
				xmlhttp.open("GET", callString, true);
				xmlhttp.send();


				//Ändra id
				dragged_object.id = "person_" + personId + "_X_" + toHouseId;
				in_house.appendChild(dragged_object);
			} else {
				//Från grupp som inte är i hus  
				//Om det man drar är en person från en grupp måste gruppens antal uppdateras
				var from_group_count = document.getElementById('count_group_' + groupId);


				/* Flytta en person från en grupp som inte är tilldelad ett hus */
				var callString = "../ajax/assign_to_house.php?personId=" + personId + "&fromGroupId=" + groupId + "&toHouseId=" + toHouseId;

				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var result = this.responseText.split(";");

						from_group_count.innerHTML = result[0] + " pers";
						house_count.innerHTML = result[2] + " pers";
					}
				};
				xmlhttp.open("GET", callString, true);
				xmlhttp.send();

				//Ändra id
				dragged_object.id = "person_" + personId + "_X_" + toHouseId;
				in_house.appendChild(dragged_object);
			}

		}

	} else {
		//Grupp
		var groupId = data_array[1];
		if (data_array.length > 2) {
			//Antalet i huset man flyttar från måste uppdateras
			var fromHouseId = data_array[2];
			var from_house_count = document.getElementById('count_house_' + fromHouseId);

			/* Flytta en grupp från ett hus till ett annat */
			var callString = "../ajax/assign_to_house.php?groupId=" + groupId + "&fromHouseId=" + fromHouseId + "&toHouseId=" + toHouseId;

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var result = this.responseText.split(";");
					//Uppdatera antalet i huset man drar från
					from_house_count.innerHTML = result[0] + " st";
					//Uppdatera antalet i huset man drar till
					house_count.innerHTML = result[1] + " st";
				}
			};
			xmlhttp.open("GET", callString, true);
			xmlhttp.send();

			//Ändra id
			dragged_object.id = "group_" + groupId + "_" + toHouseId;
			in_house.appendChild(dragged_object);
		} else {
			/* Flytta grupp från otilldelad til ett hus */
			var callString = "../ajax/assign_to_house.php?groupId=" + groupId + "&toHouseId=" + toHouseId;

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var result = this.responseText.split(";");
					//Uppdatera antalet i huset man drar till
					house_count.innerHTML = result[1] + " st";
				}
			};
			xmlhttp.open("GET", callString, true);
			xmlhttp.send();

			//Ändra id
			dragged_object.id = "group_" + groupId + "_" + toHouseId;
			in_house.appendChild(dragged_object);
		}

	}
}

function drop_unassigned_group(ev, el) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("text");
	let data_array = data.split('_');
	var dragged_object = document.getElementById(data);


	if ((data_array[0] == "group") && (data_array.length > 2)) {
		var groupId = data_array[1];
		var fromHouseId = data_array[2];
		var from_house_count = document.getElementById('count_house_' + fromHouseId);

		var callString = "../ajax/assign_to_house.php?groupId=" + groupId + "&fromHouseId=" + fromHouseId;

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var result = this.responseText.split(";");
				//Uppdatera antalet i huset man drar från
				from_house_count.innerHTML = result[0] + " pers";
			}
		};

		xmlhttp.open("GET", callString, true);
		xmlhttp.send();


		var box = document.getElementById('unassigned_groups');
		box.appendChild(dragged_object);
		//Ändra id
		dragged_object.id = "group_" + groupId;

	}
}

function drop_unassigned_person(ev, el) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("text");
	let data_array = data.split('_');
	var dragged_object = document.getElementById(data);

	if (data.indexOf("person_") === 0) {
		var personId = data_array[1];
		var groupId = data_array[2];


		if (groupId == "X") {
			//Enskild som låg i hus
			var fromHouseId = data_array[3];
			var from_house_count = document.getElementById('count_house_' + fromHouseId);

			/* Flytta en person från ett hus till icke tilldelad */
			var callString = "../ajax/assign_to_house.php?personId=" + personId + "&fromHouseId=" + fromHouseId;

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var result = this.responseText.split(";");

					from_house_count.innerHTML = result[1] + " pers";
				}
			};
			xmlhttp.open("GET", callString, true);
			xmlhttp.send();
		} else {
			//Från grupp som är i hus  
			var from_group_count = document.getElementById('count_group_' + groupId + '_' + fromHouseId);
			var fromHouseId = data_array[3];
			var from_house_count = document.getElementById('count_house_' + fromHouseId);


			/* Flytta en person från en grupp i ett hus till icke tilldelad */
			var callString = "../ajax/assign_to_house.php?personId=" + personId + "&fromGroupId=" + groupId + "&fromHouseId=" + fromHouseId;

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var result = this.responseText.split(";");

					from_group_count.innerHTML = result[0] + " pers";
					from_house_count.innerHTML = result[1] + " pers";
				}
			};
			xmlhttp.open("GET", callString, true);
			xmlhttp.send();

		}
		//Ändra id
		dragged_object.id = "person_" + personId + "_X";
		var box = document.getElementById('unassigned_persons');
		box.appendChild(dragged_object);
	}
}

