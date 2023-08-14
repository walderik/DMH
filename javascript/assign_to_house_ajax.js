
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
	var houseId = house_array[1];

	if (data_array[0] == "person") {
		var personId = data_array[1];
		var groupId = data_array[2];
		if (data_array.length > 3) {
			//Kommer från ett hus
			//Om personen låg i ett hus måste det gamla husets antal ändras
			if (groupId != "X") {
				//Från grupp som är i hus  
				//Om det man drar är en person från en grupp måste gruppens antal uppdateras
				var old_group_count = document.getElementById('count_group_' + groupId);
				old_group_count.innerHTML = "XXX";

				var oldHouseId = data_array[3];
				var old_house_count = document.getElementById('count_house_' + oldHouseId);
				old_house_count.innerHTML = "XXX";
			} else {
				//Enskild som låg i hus
				var oldHouseId = data_array[3];
				var old_house_count = document.getElementById('count_house_' + oldHouseId);
				old_house_count.innerHTML = "XXX";
			}
		} else {

			if (groupId != "X") {
				//Från grupp som inte är i hus  
				//Om det man drar är en person från en grupp måste gruppens antal uppdateras
				var old_group_count = document.getElementById('count_group_' + groupId);
				old_group_count.innerHTML = "XXX";
			} else {
				//Enskild som inte låg i hus, gör inget särskilt
			}

		}
		//Ändra id
		dragged_object.id = "person_" + personId + "_0_" + houseId;
		//Uppdatera antalet i huset man drar till
		house_count.innerHTML = "XXX";
		in_house.appendChild(dragged_object);

	} else {
		//Grupp
		var groupId = data_array[1];
		if (data_array.length > 2) {
			//Antalet i huset man flyttar från måste uppdateras
			var oldHouseId = data_array[2];
			var old_house_count = document.getElementById('count_house_' + oldHouseId);

			/* Flytta en grupp från ett hus till ett annat */
			var callString = "../ajax/assign_to_house.php?groupId=" + groupId + "&fromHouseId=" + fromHouseId + "&toHouseId=" + toHouseId;

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var result = this.responseText.split(";");
					//Uppdatera antalet i huset man drar från
					old_house_count.innerHTML = result[0] + " st";
					//Uppdatera antalet i huset man drar till
					house_count.innerHTML = result[1] + " st";
				}
			};
			xmlhttp.open("GET", callString, true);
			xmlhttp.send();

			//Ändra id
			dragged_object.id = "group_" + groupId + "_" + houseId;
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
			dragged_object.id = "group_" + groupId + "_" + houseId;
			in_house.appendChild(dragged_object);
		}

	}
}

function drop_unassigned_group(ev, el) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("text");
	let data_array = data.split('_');

	if ((data_array[0] == "group") && (data_array.length > 2)) {
		var groupId = data_array[1];
		var oldHouseId = data_array[2];
		var old_house_count = document.getElementById('count_house_' + oldHouseId);

		var callString = "../ajax/assign_to_house.php?groupId=" + groupId + "&fromHouseId=" + fromHouseId;

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var result = this.responseText.split(";");
				//Uppdatera antalet i huset man drar från
				old_house_count.innerHTML = result[0] + " st";
			}
		};

		xmlhttp.open("GET", callString, true);
		xmlhttp.send();


		var box = document.getElementById('unassigned_groups');
		box.appendChild(document.getElementById(data));

	}
}

function drop_unassigned_person(ev, el) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("text");
	if (data.indexOf("person_") === 0) {
		var box = document.getElementById('unassigned_persons');

		//Uppdatera antalet i huset man drar från
		var oldHouseId = data_array[2];
		var old_house_count = document.getElementById('count_house_' + oldHouseId);
				old_house_count.innerHTML = "XXX";
		
		box.appendChild(document.getElementById(data));
	}
}

