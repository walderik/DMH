function setMoney(selectObject, larpId) {
  var value = selectObject.value;
  var roleId = selectObject.id;

  var callString = "../ajax/setmoney.php?roleId=" + roleId + "&" + "value=" + value + "&" + "larpId=" + larpId; 

  var xmlhttp = new XMLHttpRequest();

  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}

function setMoneyEnd(selectObject, larpId) {
  var value = selectObject.value;
  var roleId = selectObject.id;

  var callString = "../ajax/setmoney.php?roleId=" + roleId + "&" + "value=" + value + "&" + "larpId=" + larpId + "&" + "isEnd=" + 1; 

  var xmlhttp = new XMLHttpRequest();

  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}


function setMoneyGroup(selectObject, larpId) {
  var value = selectObject.value;
  var groupId = selectObject.id;

  var callString = "../ajax/setmoney.php?groupId=" + groupId + "&" + "value=" + value + "&" + "larpId=" + larpId; 

  var xmlhttp = new XMLHttpRequest();
 
  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}

function setMoneyGroupEnd(selectObject, larpId) {
  var value = selectObject.value;
  var groupId = selectObject.id;

  var callString = "../ajax/setmoney.php?groupId=" + groupId + "&" + "value=" + value + "&" + "larpId=" + larpId + "&" + "isEnd=" + 1; 

  var xmlhttp = new XMLHttpRequest();
 
  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}

