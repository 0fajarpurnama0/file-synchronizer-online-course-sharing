<?php
session_start();
require($_SESSION['option'] . '/varset.php');
require('lib_main.php');
set_time_limit(0);

$_SESSION['method'];
$manual_backup = new backup($_SESSION['option'], $_SESSION['method'], $url);
$manual_backup->download_patch();
$manual_backup->apply_patch();
?>
<a href="rdiff_update_console.php"> Return</a>
