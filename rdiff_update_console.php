<html>
<head>
<title>File rdiff synchronization</title>
</head>
<body>

<h1>File Synchronizer</h1>
<h3>Based on rdiff: a controlled rsync application.</h3>
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
  <td><a href="backup.mbz.sig"> <?php echo exec('du -h backup.mbz.sig'); ?> (Signature File)</a></td>
 </tr>
 <tr>
  <td><a href="backup.mbz.delta"> <?php echo exec('du -h backup.mbz.delta'); ?> (Patch File)</a></td>
 </tr>
</table>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="submit" value="update"> <a href="settings.php"> Settings</a> 
</form>

<?php 
 require('varset.php');
 if ($_SERVER["REQUEST_METHOD"] == "POST"){
 exec('rm backup.mbz.sig; rm backup.mbz.delta; rdiff signature backup.mbz backup.mbz.sig');
 $target_url = $url.'/rdiff_make_patch.php';
 $file_name_with_full_path = realpath('./backup.mbz.sig');
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
