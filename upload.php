<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
//echo ini_get("upload_max_filesize");
//echo ini_get('upload_max_filesize');
exec('rm *.mbz');
$uploaddir = getcwd().'/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";
exec('mv *.mbz backup.mbz');
?>

<button onclick="goBack()">Return</button>

<script>
function goBack() {
    window.history.back();
}
</script>
