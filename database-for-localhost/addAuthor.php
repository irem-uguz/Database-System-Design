<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=MS932">
</head>
<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cmpe321";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_errno) {
    	die ( "connection failed: " . $conn->connec_error );
    } else {
    	$author = $_POST['author'];
    	$author = $conn->real_escape_string($author);
    	$query="INSERT INTO author (nameSurname) VALUES ('$author')";
    	if ($result = $conn->query ($query)) {
    		?>
    		<p>Author <?php echo $author;?> added successfully</p>
    		<?php
    	}else {
    		?>
    		 <p>Error!! Author <?php echo $author;?> couldn't be added.</p>
    		 <?php
    	}
    }

	?>
</body>
</html>