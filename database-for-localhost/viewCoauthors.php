<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=MS932">
<title>Insert title here</title>
</head>
<body>
    <?php
    $author = $_POST['author'];
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cmpe321";
    $conn = new mysqli($servername, $username, $password, $dbname);
    $author = $conn->real_escape_string($author);
    if ($conn->connect_errno) {
    	die ( "connection failed: " . $conn->connec_error );
    } else {
    	$query = "CALL coauthors('$author')";
    	if ($result = $conn->query ($query)) {
    		?>
    			<table border=1>
    			<tr>
    				<th>Co-Author Names</th>
    			</tr>
    			<?php
    			while( $row = $result->fetch_assoc() ) {
    			?>
    			<tr>
    				<td><?php echo $row['nameSurname']; ?></td>
    			</tr>
    			<?php
    				}
    				$result->close();
    		}
    }

	?>
</body>
</html>