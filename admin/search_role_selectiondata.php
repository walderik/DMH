<?php

include_once 'header.php';
include_once '../includes/selection_data_control.php';


$options = getAllOptionsForRoles($current_larp);

include 'navigation.php';
?>


	<script>
	function getSearchedRoles(button) {
		button.disabled = 'disabled';

		var type = document.getElementById('selection_type').value;
		var value = document.getElementById('selection_value').value;

		var callString = "../ajax/search_roles.php?operation=search&larpId=" + <?php echo $current_larp->Id ?> + "&type=" + type + "&value=" + value;

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var result = this.responseText;

				var res_div = document.getElementById('result');

				res_div.innerHTML = result;
				button.disabled = '';
			}
		};
		xmlhttp.open("GET", callString, true);
		xmlhttp.send();



	}

</script>

<script>
function changeDropdown() {
    alert("change");
    var selection_type_select = document.getElementById('selection_type');
    var selection_value_select = document.getElementById('selection_value');
	var type = document.getElementById('selection_type').value;

	selection_type_select.disabled = disabled;
	
    remove_options(selection_value_select);


	var callString = "../ajax/search_roles.php?operation=values&larpId=" + <?php echo $current_larp->Id ?> + "&type=" + type ;

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
	        var result = this.responseText.split(";");
	        for (i=0; i < result.length; i++) {
		        options = result[i].split(:);
                createOption(selection_value_select, options[0], options[1]);
	        }

			selection_type_select.disabled = '';
		}
	};
}








</script>



	<div class="content">
		<h1>Sök karaktärer efter val</h1>
		<table>
		<tr>
		<td>Basdatatyp</td>
		<td>
		<?php 
		$types = getAllTypesForRoles($current_larp);

		echo "<select name='selection_type' id='selection_type' onchange='updateDropdown()'>";
		$first = true;
		foreach ($types as $key => $type) {
		    if ($first) {
		        $firstKey = $key;
		        echo "<option value='$key'>$type</option>";
		        $first = false;
		    } else echo "<option value='$key'>$type</option>";
		}
		echo "</select>";
		
		?>
		
		
		
		</td>
		</tr>
		<tr>
		<td>Värde</td>
		<td>
		<?php 
		echo "<select name='selection_value' id='selection_value'>";
		$values = $options[$firstKey];
		foreach ($values as $value) {
		    echo "<option value='$value->Id'>$value->Name</option>";
		}
		echo "</select>";
		
		
		?>
		</td>
		</tr>
		<tr>
		<td></td><td><button onclick='getSearchedRoles(this)'>Sök</button></td></tr>
		</table>
	
	


	<div id='result'>
	
	
	
	</div>
	</div>
<script>
function updateDropdown() {

    var selection_type_select = document.getElementById('selection_type');
    var selection_value_select = document.getElementById('selection_value');
	var type = selection_type_select.value;

	selection_type_select.disabled  =  true ;
	
    removeOptions(selection_value_select);
	var callString = "../ajax/search_roles.php?operation=values&larpId=<?php echo $current_larp->Id ?>&type=" + type;

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			//var result = this.responseText;
			var res_div = document.getElementById('result');
			res_div.innerHTML = "";;

			
			var resultArr = this.responseText.split(";");
	        for (i=0; i < resultArr.length; i++) {
		        //alert(i + " " + resultArr[i]);
		        var options = resultArr[i].split(":");
		        //alert("Text: " + options[1] + ", värde: "+options[0]);

		        var opt = document.createElement('option');
		        opt.value = options[0];
		        opt.innerHTML = options[1];
		        selection_value_select.appendChild(opt);
	        }

			selection_type_select.disabled = '';
		}
	};
	xmlhttp.open("GET", callString, true);
	xmlhttp.send();

  selection_type_select.disabled  =  false ;
}


function removeOptions(selectElement) {
	   var i, L = selectElement.options.length - 1;
	   for(i = L; i >= 0; i--) {
	      selectElement.remove(i);
	   }
	}
	

</script>
<script src="../javascript/table_sort.js"></script>

</body>
</html>
