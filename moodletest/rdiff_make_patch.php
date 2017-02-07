<?php
require('../lib_main.php');
$manual_backup = new backup(NULL, $method, NULL);
$manual_backup->receive_signature();
$method = $_POST['method'];
$manual_backup = new backup(NULL, $method, NULL);
$manual_backup->generate_delta();
$url='local_apply_patch.php';
echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';
?>
