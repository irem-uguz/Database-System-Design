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
	?><p> title is <?php echo $title;?></p><?php
	$authors = $_POST ['authors'];
	$author_array = explode ( ",", $authors );
	$abstract = $_POST ['abstract'];
	$abstract = $conn->real_escape_string($abstract);
	?><p> abstract is <?php echo $abstract;?></p><?php
	$topics = $_POST ['topics'];
	$topic_array = explode ( ",", $topics );
	$Result = ( int ) $_POST ['result'];
	?><p> result is <?php echo $Result;?></p><?php
	$paperId = 0;
	if ($result = $conn->query ( "INSERT INTO paper (title , abstract, result) VALUES ('$title','$abstract', $Result)" )) {
		if ($result = $conn->query ( "SELECT * FROM paper WHERE title = '$title'" )) {
			$paperId = $result->fetch_assoc () ['paperId'];
		} else {
			?><p>Error!! 1</p><?php
			exit ();
		}
	} else {
		?><p>Error!! 2</p><?php
		exit ();
	}
	foreach ( $author_array as $author ) {
		$authorId = 0;
		$author = $conn->real_escape_string($author);
		if ($result = $conn->query ( "SELECT * FROM author WHERE nameSurname = '$author'" )) {
			$authorId = $result->fetch_assoc () ['authorId'];
			if ($result = $conn->query ( "INSERT INTO authorpaper (authorId, paperId) VALUES ($authorId,$paperId)" )) {
			} else {
				?><p>Error!! 3</p><?php
				exit ();
			}
		} else {
			?><p>Error!! 4</p><?php
			exit ();
		}
	}
	foreach ( $topic_array as $topic ) {
		$topicId = 0;
		$topic = $conn->real_escape_string($topic);
		if ($result = $conn->query ( "SELECT * FROM topic WHERE name = '$topic'" )) {
			$topicId = $result->fetch_assoc () ['topicId'];
			if ($result = $conn->query ( "INSERT INTO papertopic (topicId, paperId) VALUES ($topicId,$paperId)" )) {
			} else {
				?><p>Error!! 5</p><?php
				exit ();
			}
		} else {
			?><p>Error!! 6</p><?php
			exit ();
		}
	}
	if ($result = $conn->query ( "SELECT * FROM paper WHERE  paperId = $paperId" )) {
		$sotaNum = $result->fetch_assoc () ['sotaNum'];
		if ($sotaNum == 1 && $rresult = $conn->query ( "SELECT * FROM authorpaper WHERE paperId = $paperId" )) {
			while ( $author = $rresult->fetch_assoc () ) {
				$authorId = $author ['authorId'];
				if ($result = $conn->query ( "UPDATE author SET numofsota = numofsota+1 WHERE authorId = $authorId" )) {
				} else {
					?><p>Error!! 7</p><?php
					exit ();
				}
			}
		} else if ($sotaNum == 1) {
			?><p>Error!! 8</p><?php
			exit ();
		}
	}
}
?>
</body>
</html>