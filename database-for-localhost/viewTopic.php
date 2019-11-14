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
	$topicName = $_POST['topic'];
	$topicName = $conn->real_escape_string($topicName);
	$topicQuery = "SELECT * FROM topic WHERE name='";
	$topicQuery .= $topicName;
	$topicQuery .= "'";
	$topicResult = $conn->query ($topicQuery)->fetch_assoc();
	$topicId = $topicResult['topicId'];
	$topicSota = $topicResult['sota'];
	$query = "SELECT * FROM papertopic WHERE topicId = ";
	$query .= $topicId;
	if ($result = $conn->query ($query)) {
		?>
		<table border=1>
		<tr>
			<th>Topic Name</th>
			<th>Paper Title</th>
			<th>Result</th>
		</tr>
		<?php
		while( $row = $result->fetch_assoc() ) {
			$paperQuery = "SELECT * FROM paper WHERE result=";
			$paperQuery .= $topicSota;
			$paperQuery .= " AND paperId =";
			$paperQuery .= $row['paperId'];
			if($paperResult = $conn->query($paperQuery)){
			while($paper = $paperResult->fetch_assoc()){
		?>
		<tr>
			<td><?php echo $topicName; ?></td>
			<td><?php echo $paper['title']; ?></td>
			<td><?php echo $paper['result']; ?></td>
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