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
$conn = new mysqli( $servername, $username, $password, $dbname );
if ($conn->connect_errno) {
	die ( "connection failed: " . $conn->connec_error );
} else {
	$title = $_POST ['title'];
	$title = $conn->real_escape_string($title);
	$paperId = 0;
	$sota = 0;
	if ($result = $conn->query ( "SELECT * FROM paper WHERE title = '$title'" )) {
		$paperId = $result->fetch_assoc () ['paperId'];
		$sota = $result->fetch_assoc () ['sotaNum'];
	} else {
		?><p>Error!!</p><?php
		exit ();
	}
	if ($sota == 1 && $result = $conn->query ( "SELECT * FROM authorpaper WHERE paperId = $paperId" )) {
		while ( $author = $result->fetch_assoc () ) {
			$authorId = $author ['authorId'];
			if ($result = $conn->query ( "UPDATE author SET numofsota = numofsota-1 WHERE authorId = $authorId" )) {
			} else {
				?><p>Error!!</p><?php
				exit ();
			}
		}
	} else if ($sota == 1) {
		?><p>Error!!</p><?php
		exit ();
	}
	if ($result = $conn->query ( "DELETE from authorpaper WHERE paperId = $paperId" )) {
	} else {
		?><p>Error!!</p><?php
		exit ();
	}
	if ($result = $conn->query ( "DELETE from paperTopic WHERE paperId = $paperId" )) {
	} else {
		?><p>Error!!</p><?php
		exit ();
	}
	if ($result = $conn->query ( "DELETE from paper WHERE paperId = $paperId" )) {
	} else {
		?><p>Error!!</p><?php
			exit ();
	}
}
?>
</body>
</html>