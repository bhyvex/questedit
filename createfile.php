<?php

include("quests.php");

$fn = $_POST['filename'];
$zone = $_POST['zone'];

$fpath = $qdir.$zone."/";
$test = dirname($fpath.$fn);

if(strtolower(substr($fn, -3)) == "lua") { $cmt = "--"; }
else { $cmt = "#"; }

if(!quest_dir($test))
{
    die("ERROR|Invalid Filename: $fpath$fn");
}

if(file_exists($fpath.$fn))
{
    die("ERROR|File Exists!");
}

$fh = fopen($fpath.$fn, 'w');
fwrite($fh, "$cmt $zone - $fn\n");
fclose($fh);

$nf = array();
$nf['zone'] = $zone;
$nf['filename'] = $fn;

print "NEWFILE|" . json_encode($nf);

?>
