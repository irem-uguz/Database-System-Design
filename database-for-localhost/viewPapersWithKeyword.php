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
	$keyword = $_POST['keyword'];
	$keyword = $conn->real_escape_string($keyword);
	$keywordQuery = "SELECT * FROM paper WHERE title LIKE '%{$keyword}%' OR abstract LIKE '%{$keyword}%'";
	if ($result = $conn->query ($keywordQuery)) {
		?>
		<table border=1>
		<tr>
			<th>Paper Titles</th>
			<th>Abstracts</th>
		</tr>
		<?php
		while( $row = $result->fetch_assoc() ) {
		?>
		<tr>
			<td><?php echo $row['title']; ?></td>
			<td><?php echo $row['abstract']; ?></td>
		</tr>
		<?php
			}
			$result->close();
	}
}
?>
</body>
</html>