function register_payment(registrationId, amount, date, selectObject) {
	
  selectObject.disabled = 'disabled';

  var callString = "../ajax/register_payment.php?registrationId=" + registrationId + "&" + "amount=" + amount + "&" + "date=" + date; 

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
		  selectObject.innerHTML = this.responseText;
      }
    };

  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}



