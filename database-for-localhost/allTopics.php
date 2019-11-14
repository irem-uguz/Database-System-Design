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
	if ($result = $conn->query("SELECT * FROM topic")) {
		?>
						<table border=1>
						<tr>
							<th>Name</th>
							<th>SOTA result</th>
						</tr>
					<?php
							while($row = $result->fetch_assoc() ) {
							?>
								<tr>
								<td><?php echo $row['name']; ?></td>
								<td><?php echo $row['sota']; ?></td>
								</tr>
							<?php
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