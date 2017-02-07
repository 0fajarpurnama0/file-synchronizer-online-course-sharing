<?php
session_start();
$option = $_POST['option'];
foreach (glob($option . '/backup*') as $filename) {
  if (file_exists($filename)) {
    unlink($filename);
    if (file_exists($filename)) {
      exec("rm -r $filename");
    }
  }
}
$uploaddir = getcwd().'/';
$uploadfile = $uploaddir . $option . "/" . basename($_FILES['userfile']['name']);
echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";
$extension = pathinfo($uploadfile, PATHINFO_EXTENSION);
echo 'Uploaded File Extension: ' . $extension;
if (strcmp($extension,'gz')==0) {
  rename($uploadfile, $uploaddir . $option . "/backup.tar." . $extension);
} else {
  rename($uploadfile, $uploaddir . $option . "/backup." . $extension);
}
chmod($uploaddir . $option . "/backup." . $extension, 0777);
?>

<a href="rdiff_update_console.php">Return</a>
