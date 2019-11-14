<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=MS932">
</head>
<body>
	<form action="addAuthor.php" method="POST">
		Author Name: <input type="text" name="author">
		<input type="submit" value="Add Author">
	</form>
	<form action="updateAuthor.php" method="POST">
		Update Author: <input type="text" name="oldName">
		New Name: <input type="text" name="newName">
		<input type="submit" value="Update Author">
	</form>
	<form action="deleteAuthor.php" method="POST">
		Delete Author: <input type="text" name="author">
		<input type="submit" value="Delete Author">
	</form>
	<form action="addTopic.php" method="POST">
		Topic Name: <input type="text" name="topic">
		<input type="submit" value="Add Topic">
	</form>
	<form action="updateTopic.php" method="POST">
		Update Topic: <input type="text" name="oldName">
		New Name: <input type="text" name="newName">
		<input type="submit" value="Update Topic">
	</form>
	<form action="deleteTopic.php" method="POST">
		Delete Topic: <input type="text" name="topic">
		<input type="submit" value="Delete Topic">
	</form>
	<form action="addPaper.php" method="POST">
		Paper Title: <input type="text" name="title">
		Authors: <input type="text" name="authors">
		Abstract: <input type="text" name="abstract">
		Topics: <input type="text" name="topics">
		Result: <input type="text" name="result">
		<input type="submit" value="Add Paper">
	</form>
	<p>Update Paper</p>
	<form action="updatePaper.php" method="POST">
		Paper Title: <input type="text" name="oldTitle"><br>
		New Paper Title: <input type="text" name="newTitle"><br>
		Add Authors: <input type="text" name="addAuthors"><br>
		Delete Authors: <input type="text" name="deleteAuthors"><br>
		New Abstract: <input type="text" name="abstract"><br>
		Add Topics: <input type="text" name="addTopics"><br>
		Delete Topics: <input type="text" name="deleteTopics"><br>
		New Result: <input type="text" name="result"><br>
		<input type="submit" value="Update Paper"><br>
	</form>
	<form action="deletePaper.php" method="POST">
		Paper Title: <input type="text" name="title">
		<input type="submit" value="Delete Paper">
	</form>
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