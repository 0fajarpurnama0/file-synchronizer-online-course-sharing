<html>
<head>
<title>Moodle Backup Rdiff Synchronization</title>
<link rel="stylesheet" type="text/css" href="main.css"/>
</head>
<body>

<h1>Main Console</h1>
<h3>Based on rdiff: a controlled rsync application.</h3>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<select name="option">
  <option value="moodle">moodle</option>
  <option value="manual">manual</option>
</select>
<input type="submit" value="choose">
</form>

<?php 
 if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (strcmp($_POST['option'],'moodle')==0){
    header("Location: moodle/rdiff_update_console.php");
  } else {
    header("Location: manual/rdiff_update_console.php");
  }  
 }      
?>
