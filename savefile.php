<?php

include("quests.php");

$fn = $_POST['filename'];
$zone = $_POST['zone'];
$content = $_POST['file'];

$savefile = $qdir.$zone."/".$fn;

if(!quest_dir($savefile))
{
    die("ERROR|Bad filename!");
}

$fh = fopen($savefile, "w");
if(!$fh) { die("ERROR|Unable to open file: $savefile"); }

fwrite($fh, $content);
fclose($fh);

$ret = array();
$ret['filename'] = $fn;
$ret['zone'] = $zone;

print "SAVED|".json_encode($ret);;

?>
