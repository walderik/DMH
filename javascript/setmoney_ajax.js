<script  type="text/javascript">


function setMoney(selectObject) {
  var value = selectObject.value;
  var larpId = <?php echo $current_larp->Id?>;
  var roleId = selectObject.id;

  var callString = "../ajax/setmoney.php?roleId=" + roleId + "&" + "value=" + value + "&" + "larpId=" + larpId; 

  var xmlhttp = new XMLHttpRequest();

  xmlhttp.open("GET", callString, true);
  xmlhttp.send();
}
</script>
