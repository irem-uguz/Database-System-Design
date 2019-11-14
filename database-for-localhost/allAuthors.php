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
					if ($result = $conn->query("SELECT * FROM author")) {
					?>
						<table border=1>
						<tr>
							<th>Author Name-Surname</th>
						</tr>
					<?php
							while($row = $result->fetch_assoc() ) {
							?>
								<tr>
								<td><?php echo $row['nameSurname']; ?></td>
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