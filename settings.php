<html>
<head>
<title>rdiff console settings</title>
</head>
<body>
<?php
require('varset.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Url: <input type="text" name="url" value="<?php echo $url; ?>">
<input type="submit" value="change"> 

<button onclick="goBack()">Return</button>
<script>
function goBack() {
    window.history.back();
}
</script>

</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $myfile = fopen("varset.php", "w") or die("Unable to open file!");
 $txt = '
 <?php
 $url ="'.$_POST["url"].'" ; ?>
 ';
 fwrite($myfile, $txt);
 fclose($myfile);
 header("Refresh:0");
}
?>

</body>
</html>
