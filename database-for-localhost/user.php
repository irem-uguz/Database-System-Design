<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=MS932">
<title>User options</title>
</head>
<body>
	<button type="button" onClick="location.href='allAuthors.php'">View All Authors</button><br><br>
	<button type="button" onClick="location.href='allPapers.php'">View All Papers</button><br><br>
	<button type="button" onClick="location.href='allTopics.php'">View All Topics</button><br><br>
	<form action="viewAuthor.php" method="POST">
		Author Name: <input type="text" name="author">
		<input type="submit" value="Search All Papers">
	</form>
	<form action="viewTopic.php" method="POST">
		Topic Name: <input type="text" name="topic">
		<input type="submit" value="View SOTA and paper">
	</form>
	<form action="viewPapersWithTopic.php" method="POST">
		View Papers With Topic Name: <input type="text" name="topic">
		<input type="submit" value="Papers">
	</form>
	<button type="button" onClick="location.href='rankAuthors.php'">Rank All Authors By SOTA Result</button><br><br>
	<form action="viewPapersWithKeyword.php" method="POST">
		View Papers With Keyword: <input type="text" name="keyword">
		<input type="submit" value="Papers">
	</form>
	<form action="viewCoauthors.php" method="POST">
		View Co-authors Of Author: <input type="text" name="author">
		<input type="submit" value="Co-Authors">
	</form>
    </body>
</html>