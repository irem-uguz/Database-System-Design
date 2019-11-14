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
$conn = new mysqli ( $servername, $username, $password, $dbname );
if ($conn->connect_errno) {
	die ( "connection failed: " . $conn->connec_error );
} else {
	if ($result = $conn->query ( "SELECT * FROM paper" )) {
		?>
		<table border=1>
		<tr>
			<th>Authors</th>
			<th>Title</th>
			<th>Abstract</th>
			<th>Topics</th>
			<th>Result</th>
		</tr>
		<?php
		while ( $row = $result->fetch_assoc () ) {
			$authors = "";
			$authorQuery = "SELECT * FROM authorpaper WHERE paperId=";
			$authorQuery .= $row['paperId'];
			$authorResult = $conn->query($authorQuery);
			while($authorNo = $authorResult->fetch_assoc()){
				$authorNQ = "SELECT * FROM author WHERE authorId=";
				$authorNQ .= $authorNo['authorId'];
				$authorR = $conn->query($authorNQ);
				$author = "";
				if($authorR->num_rows===1){
					$author = $authorR->fetch_assoc()['nameSurname'];
					$authors .= $author;
					$authors .= "\n";
				}
				$authorR->close();
			}
			$authorResult->close();
			$topics = "";
			$topicQuery = "SELECT * FROM papertopic WHERE paperId=";
			$topicQuery .= $row['paperId'];
			$topicResult = $conn->query($topicQuery);
			while($topicNo = $topicResult->fetch_assoc()){
				$topicNQ = "SELECT * FROM topic WHERE topicId=";
				$topicNQ .= $topicNo['topicId'];
				$topicR = $conn->query($topicNQ);
				$topic = "";
				if($topicR->num_rows===1){
					$topic = $topicR->fetch_assoc()['name'];
					$topics .= $topic;
					$topics .= "\n";
				}
				$topicR->close();
			}
			$topicResult->close();
		?>
		<tr>
			<td><?php echo $authors; ?></td>
			<td><?php echo $row['title']; ?></td>
			<td><?php echo $row['abstract']; ?></td>
			<td><?php echo $topics; ?></td>
			<td><?php echo $row['result']; ?></td>
		</tr>
							<?php
		}
		$result->close ();
		?>
					</table>
					<?php
	}
}
?>
		</body>
</html>