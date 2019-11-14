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
	$query="INSERT INTO topic (name) VALUES ('$topic')";
	if ($result = $conn->query ($query)) {
		?>
    		<p>Topic <?php echo $topic;?> added successfully</p>
    		<?php
    	}else {
    		?>
    		 <p>Error!! Topic <?php echo $topic;?> couldn't be added.</p>
    		 <?php
    	}
}
	?>
</body>
</html>