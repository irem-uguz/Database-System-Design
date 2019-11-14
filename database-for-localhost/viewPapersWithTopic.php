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
	$topic = $_POST['topic'];
	$topic = $conn->real_escape_string($topic);
	$topicQuery = "SELECT * FROM topic WHERE name='$topic'";
	$topicResult = $conn->query ($topicQuery)->fetch_assoc();
	$topicId = $topicResult['topicId'];
	$query = "SELECT * FROM papertopic WHERE topicId = $topicId ";
	if ($result = $conn->query ($query)) {
		?>
		<table border=1>
		<tr>
			<th>Paper Titles</th>
		</tr>
		<?php
		while( $row = $result->fetch_assoc() ) {
			$paperQuery = "SELECT * FROM paper WHERE paperId=";
			$paperQuery .= $row['paperId'];
			if($paperResult = $conn->query($paperQuery)){
			while($paper = $paperResult->fetch_assoc()){
		?>
		<tr>
			<td><?php echo $paper['title']; ?></td>
		</tr>
		<?php
			}
			}
		}
		$result->close();
		?>
				</table>
				<?php
	}
}
?>
</body>
</html>