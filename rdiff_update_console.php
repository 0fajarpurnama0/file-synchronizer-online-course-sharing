<html>
<head>
<title>Moodle Backup Rdiffdir Synchronization</title>
<link rel="stylesheet" type="text/css" href="main.css"/>
</head>
<body>

<h1>Moodle Backup Rdiffdir Synchronizer</h1>
<h3>Based on rdiff directory: a controlled rsync application.</h3>

<form enctype="multipart/form-data" action="upload.php" method="POST">
    Insert backup (.mbz) file: <input name="userfile" type="file" />
    <input type="submit" value="upload" />
</form>

<table border="1" style="width:30%">
 <tr>
  <td>File</td>
 </tr>
 <tr>
  <td><a href="backup.mbz"> <?php echo exec('du -h backup.mbz'); ?> (Current Backup)</a></td>
 </tr>
 <tr>
  <td><a href="backup.mbz.backup"> <?php echo exec('du -h backup.mbz.backup'); ?> (Previous Backup)</a></td>
 </tr>
 <tr>
  <td><a href="backup.sig"> <?php echo exec('du -h backup.sig'); ?> (Signature File)</a></td>
 </tr>
 <tr>
  <td><a href="backup.delta"> <?php echo exec('du -h backup.delta'); ?> (Patch File)</a></td>
 </tr>
</table>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="submit" value="update"> <a href="settings.php"> Settings</a> 
</form>

<?php 
 require('varset.php');
 if ($_SERVER["REQUEST_METHOD"] == "POST"){
 exec('rm backup.sig; rm backup.delta; rm -r backup; mkdir backup; tar xf backup.mbz -C backup; rdiffdir signature backup backup.sig');
 $target_url = $url.'/rdiff_make_patch.php';
 $file_name_with_full_path = realpath('./backup.sig');
 $post = array('extra_info' => '123456','file_contents'=>new \CURLFile($file_name_with_full_path));
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL,$target_url);
 curl_setopt($ch, CURLOPT_POST,1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 $result=curl_exec ($ch);
 curl_close ($ch);
 echo $result;
}
?>
