<?php
session_start();
if (file_exists($_SESSION['option'] . '/varset.php')) {
  require($_SESSION['option'] . '/varset.php');
}
if (file_exists('main_varset.php')) {
  require('main_varset.php');
}
?>

<html>
<head>
<title>rdiff console settings</title>
</head>
<body>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<?php if(isset($_SESSION['option'])){ ?>
Url of Master: <input type="text" name="url" value="<?php echo $url; ?>"> <br>
content_url: <input type="text" name="content_url" value="<?php echo $content_url; ?>">
<?php } else { ?>
Directory of your Moodle: <input type="text" name="directory" value="<?php echo $directory; ?>"> <br>
Moodle url: <input type="text" name="moodle_url" value="<?php echo $moodle_url; ?>"> <br>
<?php } ?>
<input type="submit" value="change">
<input type="submit" name="check_dependencies" value="check dependencies"><a href="rdiff_update_console.php">Return</a>

</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if(isset($_SESSION['option'])){
    $myfile = fopen($_SESSION['option'] . '/varset.php', 'w') or die('Unable to open file!');
    $txt = '
    <?php
    $url ="'.$_POST["url"].'" ; 
    $content_url = "'.$_POST["content_url"].'" ;
    ?>
    ';
    fwrite($myfile, $txt);
    fclose($myfile);
  } else {
    $myfile = fopen('main_varset.php', 'w') or die('Unable to open file!');
    $txt = '
    <?php 
    $directory = "'.$_POST["directory"].'";
    $moodle_url = "'.$_POST["moodle_url"].'";
    ?>
    ';
    fwrite($myfile, $txt);
    fclose($myfile);
  }
  if(isset($_POST['check_dependencies'])){
    $package = array('rdiff','php-curl','duplicity');
    foreach($package as $value){
      if(strpos(shell_exec("dpkg -s $value"), 'Status: install ok installed') != NULL){
        echo "$value installed <br>";
      } else {
        echo "$value not installed <br>";
      }
    }
  }
  if (file_exists($directory.'/local/fajar-moodle-sync/get_course_id.php')){ 
?>
    <form method="post" action="http://<?php echo $moodle_url.'/local/fajar-moodle-sync/get_course_id.php';?>">
    <input type="submit" value="generate course list">
<?php
  } else {
    if (!copy('get_course_id.php', $directory . '/local/fajar-moodle-sync/get_course_id.php')) {
        echo $directory;
        echo 'failed to copy get_course_id.php...\n';
    }
  }
}

?>

</body>
</html>
