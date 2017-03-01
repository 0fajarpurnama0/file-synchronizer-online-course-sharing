<?php
require('../../lib_main.php');
$manual_backup = new backup(getcwd, NULL, $_POST['split'], NULL, NULL);
$manual_backup->receive_signature();
$manual_backup = new backup(getcwd, $_POST['method'], $_POST['split'], NULL, NULL);
$manual_backup->generate_delta();
//$url='local_apply_patch.php';
//echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';
?>
