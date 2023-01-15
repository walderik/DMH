<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link href="css/navigation.css" rel="stylesheet" type="text/css">
<title>Insert title here</title>
</head>
<body class="loggedin"y>


// Jag har kvar filen bara för att kunna se hur man gör undermenyer
<nav id="navigation">
  <a href="#" class="logo">Studio<span>+<span></a>
  <ul class="links">
    <li><a href="#">About</a></li>
    <li class="dropdown"><a href="#" class="trigger-drop">Work<i class="arrow"></i></a>
      <ul class="drop">
        <li><a href="#">Art</a></li>
        <li><a href="#">Photography</a></li>
        <li><a href="#">Audio</a></li>
        <li><a href="#">Films</a></li>
      </ul>
    </li>
    <li class="dropdown"><a href="#" class="trigger-drop">Contact<i class="arrow"></i></a>
      <ul class="drop">
        <li><a href="#">Email</a></li>
        <li><a href="#">Phone</a></li>
      </ul>
    </li>
  </ul>
</nav>

		<div class="content">
			<h2>Aktivt lajv</h2>
			<label for="larp">Välj lajv:</label>
			<form action="../includes/set_larp.php" method="POST">
    			 <input type="submit" value="Välj">
			 </form>
			 </div>

</body>

</html>