function saveAmount(selectObject) {


  var value = selectObject.value;
  var id = selectObject.id;
  
  var callString = "../ajax/setringedientamount.php?supplierIngredientId=" + id + "&value=" + value; 

  var xmlhttp = new XMLHttpRequest();
   xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var result = this.responseText;
       }
    };
  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}




