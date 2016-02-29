<?php
require('varset.php');
set_time_limit(0);
//This is the file where we save the    information
$fp = fopen ('backup.mbz.delta', 'w+');
$url = $url.'/backup.mbz.delta';
//Here is the file we are downloading, replace spaces with %20
$ch = curl_init(str_replace(" ","%20",$url));
curl_setopt($ch, CURLOPT_TIMEOUT, 50);
// write curl response to file
curl_setopt($ch, CURLOPT_FILE, $fp); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// get curl response
curl_exec($ch); 
curl_close($ch);
fclose($fp);
exec("rdiff patch backup.mbz backup.mbz.delta backup1.mbz; mv backup.mbz backup.mbz.backup; mv backup1.mbz backup.mbz; rm backup1.mbz");
echo "update complete";
?>
<a href="rdiff_update_console.php"> Return</a>
