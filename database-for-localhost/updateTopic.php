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
	$newName = $conn->real_escape_string($newName);
	$idQuery="SELECT topicId FROM topic WHERE name = '$oldName'";
	$id=0;
	if ($result = $conn->query ($idQuery)) {
		$id = $result->fetch_assoc()['topicId'];
		$query = "UPDATE topic SET name = '$newName' WHERE topicId = $id";
		if ($update = $conn->query ($query)) {
			?>
    		    		<p>Topic updated successfully</p>
    		    		<?php
    		    	}else {
    		    		?>
    		    		 <p>Error!! Topic couldn't be updated.</p>
    		    		 <?php
    		    	}
    	}
    }

	?>
</body>
</html>