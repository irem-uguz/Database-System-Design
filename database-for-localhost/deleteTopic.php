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
	$topic=$_POST['topic'];
	$topic = $conn->real_escape_string($topic);
	$idQuery="SELECT topicId FROM topic WHERE name = '$topic'";
	$id=0;
	if ($result = $conn->query ($idQuery)) {
		$id = $result->fetch_assoc()['topicId'];
		$query = "DELETE FROM topic WHERE topicId = $id";
		if ($delete = $conn->query ($query)) {
			?>
    		    		<p>Topic deleted successfully</p>
    		    		<?php
    		}else {
    	    		?>
    	    		 <p>Error!! Topic couldn't be deleted.</p>
    	    		 <?php
   	    	}
    		$query = "DELETE FROM papertopic WHERE topicId = $id";
    		if ($delete = $conn->query ($query)) {
    		    ?>
    		    <p>Topic deleted from papers successfully</p>
    		    <?php
    	   }else {
    		    ?>
    		    <p>Error!! Topic couldn't be deleted from papers.</p>
    		    <?php
    		}
    	}
    }
	?>
</body>
</html>