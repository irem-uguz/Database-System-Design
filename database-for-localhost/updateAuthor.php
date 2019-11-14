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
    	$oldName=$_POST['oldName'];
    	$oldName = $conn->real_escape_string($oldName);
    	$newName=$_POST['newName'];
    	$newName= $conn->real_escape_string($newName);
    	$idQuery="SELECT authorId FROM author WHERE nameSurname = '$oldName'";
    	$id=0;
    	if ($result = $conn->query ($idQuery)) {
    		$id = $result->fetch_assoc()['authorId'];
    		$query = "UPDATE author SET nameSurname = '$newName' WHERE authorId = $id";
    		if ($update = $conn->query ($query)) {
    			?>
    		    		<p>Author updated successfully</p>
    		    		<?php
    		    	}else {
    		    		?>
    		    		 <p>Error!! Author couldn't be updated.</p>
    		    		 <?php
    		    	}
    	}
    }

	?>
</body>
</html>