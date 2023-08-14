function recalculate(selectObject, larpId) {

  var currency = document.getElementById("Currency").value;
  var value = selectObject.value;
  var ids = selectObject.id.split(":");
  var resourceId = ids[0];
  var titledeedId = ids[1];
  
  var callString = "../ajax/setresource.php?resourceId=" + resourceId + "&titledeedId=" + titledeedId + "&value=" + value + "&larpId=" + larpId; 

  var xmlhttp = new XMLHttpRequest();
   xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var result = this.responseText.split(" ");
        document.getElementById("Balance_"+resourceId).innerHTML = result[1];
        document.getElementById("Cards_"+resourceId).innerHTML = result[2];
        document.getElementById("Result_"+titledeedId).innerHTML = result[0] + " " + currency;
      }
    };
  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}



function recalculateMoney(selectObject, larpId) {

  var currency = document.getElementById("Currency").value;
  var value = selectObject.value;
  var titledeedId = selectObject.id;
  
  var callString = "../ajax/setresource.php?titledeedId=" + titledeedId + "&value=" + value + "&larpId=" + larpId; 

  var xmlhttp = new XMLHttpRequest();
   xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var result = this.responseText.split(" ");
        document.getElementById("Money_sum").innerHTML = result[1];
        document.getElementById("Result_"+titledeedId).innerHTML = result[0] + " " + currency;
      }
    };
  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}


