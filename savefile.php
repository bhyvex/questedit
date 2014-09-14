<?php

include("quests.php");

$fn = $_POST['filename'];
$content = $_POST['content'];

if(!quest_dir($fn))
{
    die("ERROR|Bad filename!");
}

$fh = fopen($fn, "w");
if(!$fh) { die("ERROR|Unable to open file: $fn"); }

fwrite($fh, $content);
fclose($fh);

print "SAVED|$fn";

?>
