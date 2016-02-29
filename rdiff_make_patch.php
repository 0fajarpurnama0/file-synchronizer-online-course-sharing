<?php
exec('rm backup.mbz.sig; rm backup.mbz.delta');
$uploaddir = realpath('./') . '/';
$uploadfile = $uploaddir . basename($_FILES['file_contents']['name']);
echo '<pre>';
	if (move_uploaded_file($_FILES['file_contents']['tmp_name'], $uploadfile)) {
	    echo "File is valid, and was successfully uploaded.\n";
	} else {
	    echo "Possible file upload attack!\n";
	}
	echo 'Here is some more debugging info:';
	print_r($_FILES);
	echo "\n<hr />\n";
	print_r($_POST);
print "</pr" . "e>\n";
exec('rdiff delta backup.mbz.sig backup.mbz backup.mbz.delta');
$url='http://localhost/php-practice/sync/demo/local_apply_patch.php';
echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';
/*
$target_url = "http://localhost/php-practice/sync/rdiff_apply_patch.php";
$file_name_with_full_path = realpath('./backup.mbz.delta');
$post = array('extra_info' => '123456','file_contents'=>new \CURLFile($file_name_with_full_path));
$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$target_url);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$result=curl_exec ($ch);
	curl_close ($ch);
/*
?>
