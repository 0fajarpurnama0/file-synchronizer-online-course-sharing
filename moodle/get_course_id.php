<?php
//define('CLI_SCRIPT', true);
require_once('../../config.php');
$courses = get_courses();

//print_r($courses[4]->shortname);
foreach ($courses as $value) {
  $my_course["$value->id"] = $value->shortname;
  //echo "$value->shortname <br>";
}

print_r($my_course);

//echo $courses[1];

 $myfile = fopen("test.php", "w") or die("Unable to open file!");
 $txt = '<?php ';
 fwrite($myfile, $txt);
 foreach ($courses as $value) {
   $txt = '$my_course['."$value->id".']='."'"."$value->shortname"."'".';';
   fwrite($myfile, $txt);
 }
 $txt = '?>';
 fwrite($myfile, $txt);
 fclose($myfile);

