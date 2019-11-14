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
    	$author = $_POST['author'];
    	$author = $conn->real_escape_string($author);
    	$idQuery="SELECT authorId FROM author WHERE nameSurname = '$author'";
    	$id=0;
    	if ($result = $conn->query ($idQuery)) {
    		$id = $result->fetch_assoc()['authorId'];
    		$query = "DELETE FROM author WHERE authorId = $id";
    		if ($delete = $conn->query ($query)) {
    			?>
    		    		<p>Author deleted successfully</p>
    		    		<?php
    		}else {
    	    		?>
    	    		 <p>Error!! Author couldn't be deleted.</p>
    	    		 <?php
   	    	}
    		$query = "DELETE FROM authorpaper WHERE authorId = $id";
    		if ($delete = $conn->query ($query)) {
    		    ?>
    		    <p>Author deleted from papers successfully</p>
    		    <?php
    	   }else {
    		    ?>
    		    <p>Error!! Author couldn't be deleted from papers.</p>
    		    <?php
    		}
    	}
    }
	?>
</body>
</html>