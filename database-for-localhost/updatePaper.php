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
	$oldTitle = $_POST ['oldTitle'];
	$oldTitle = $conn->real_escape_string($oldTitle);
	$paperId = 0;
	$oldsota = 0;
	if ($result = $conn->query ( "SELECT * FROM paper WHERE title = '$oldTitle'" )) {
		$paperId = $result->fetch_assoc () ['paperId'];
		$oldsota = $result->fetch_assoc () ['sotaNum'];
	} else {
		?><p>Error!!1</p><?php
		exit ();
	}
	if (!empty($_POST ['addAuthors'])) {
		$addAuthors = $_POST ['addAuthors'];
		$add_authors = explode ( ",", $addAuthors );
		foreach ( $add_authors as $author ) {
			$author = $conn->real_escape_string($author);
			$authorId = 0;
			if ($rresult = $conn->query ( "SELECT * FROM author WHERE nameSurname = '$author'" )) {
				$authorId = $rresult->fetch_assoc () ['authorId'];
				if ($result = $conn->query ( "INSERT INTO authorpaper (authorId, paperId) VALUES ($authorId,$paperId)" )) {
				} else {
					?><p>Error!!2 paper id is <?php echo $paperId;?> author id is <?php echo $paperId;?></p><?php
					exit ();
				}
			} else {
				?><p>Error!!3</p><?php
				exit ();
			}
		}
	}
	if (!empty ( $_POST ['deleteAuthors'] )) {
		$deleteAuthors = $_POST ['deleteAuthors'];
		$delete_authors = explode ( ",", $deleteAuthors );
		foreach ( $delete_authors as $author ) {
			$authorId = 0;
			$author = $conn->real_escape_string($author);
			if ($rresult = $conn->query ( "SELECT * FROM author WHERE nameSurname = '$author'" )) {
				$authorId = $rresult->fetch_assoc () ['authorId'];
				if ($result = $conn->query ( "DELETE FROM authorpaper WHERE authorId = $authorId AND paperId = $paperId" )) {
				} else {
					?><p>Error!!4</p><?php
					exit ();
				}
			} else {
				?><p>Error!!5</p><?php
				exit ();
			}
		}
	}
	if (!empty ( $_POST ['addTopics'] )) {
		$addTopics = $_POST ['addTopics'];
		$add_topics = explode ( ",", $addTopics );
		foreach ( $add_topics as $topic ) {
			$topicId = 0;
			$topic = $conn->real_escape_string($topic);
			if ($rresult = $conn->query ( "SELECT * FROM topic WHERE name = '$topic'" )) {
				$topicId = $rresult->fetch_assoc () ['topicId'];
				if ($result = $conn->query ( "INSERT INTO papertopic (topicId, paperId) VALUES ($topicId,$paperId)" )) {
				} else {
					?><p>Error!!6</p><?php
					exit ();
				}
			} else {
				?><p>Error!!7</p><?php
				exit ();
			}
		}
	}
	if (!empty ( $_POST ['deleteTopics'] )) {
		$deleteTopics = $_POST ['deleteTopics'];
		$delete_topics = explode ( ",", $deleteTopics );
		foreach ( $delete_topics as $topic ) {
			$topicId = 0;
			$topic = $conn->real_escape_string($topic);
			if ($rresult = $conn->query ( "SELECT * FROM topic WHERE name = '$topic'" )) {
				$topicId = $rresult->fetch_assoc () ['topicId'];
				if ($result = $conn->query ( "DELETE FROM papertopic WHERE topicId = $topicId AND paperId = $paperId" )) {
				} else {
					?><p>Error!!8</p><?php
					exit ();
				}
			} else {
				?><p>Error!!9</p><?php
				exit ();
			}
		}
	}
	if (!empty ( $_POST ['newTitle'] )) {
		$newTitle = $_POST ['newTitle'];
		$newTitle = $conn->real_escape_string($newTitle);
		if ($result = $conn->query ( "UPDATE paper SET title = '$newTitle' WHERE paperId = $paperId" )) {
		} else {
			?><p>Error!!10</p><?php
			exit ();
		}
	}
	if (!empty ( $_POST ['abstract'] )) {
		$abstract = $_POST ['abstract'];
		$abstract = $conn->real_escape_string($abstract);
		if ($result = $conn->query ( "UPDATE paper SET abstract = '$abstract' WHERE paperId = $paperId" )) {
		} else {
			?><p>Error!!11</p><?php
			exit ();
		}
	}
	if (!empty ( $_POST ['result'] )) {
		$Result = ( int ) $_POST ['result'];
		if ($result = $conn->query ( "UPDATE paper SET result = $Result WHERE paperId = $paperId" )) {
		} else {
			?><p>Error!!12</p><?php
			exit ();
		}
	}
	if ($result = $conn->query ( "SELECT * FROM paper WHERE  paperId = $paperId" )) {
		$sotaNum = $result->fetch_assoc () ['sotaNum'];
		if ($rresult = $conn->query ( "SELECT * FROM authorpaper WHERE paperId = $paperId" )) {
			while ( $row = $rresult->fetch_assoc()) {
				$authorId = $row ['authorId'];
				if ($oldsota == 0 && $sotaNum == 1) {
					if ($result = $conn->query ( "UPDATE author SET numofsota = numofsota+1 WHERE authorId = $authorId" )) {
					} else {
						?><p>Error!!13</p><?php
						exit ();
					}
				} else if ($oldsota == 1 && $sotaNum == 0) {
					if ($result = $conn->query( "UPDATE author SET numofsota = numofsota-1 WHERE authorId = $authorId" )) {
					} else {
						?><p>Error!!14</p><?php
						exit ();
					}
				}
			}
		} else {
			?><p>Error!!15</p><?php
			exit ();
		}
	}
}
?>
</body>
</html>