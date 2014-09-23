<?php

include("quests.php");

//$src = $qdir.$_POST['src'];
$src = $qdir.$_POST['zone']."/".$_POST['filename'];

if(strtolower(substr($src, -3)) == "lua") { $mode = "lua"; }
else { $mode = "perl"; }

if(!quest_dir($src))
{
    die("ERROR|Invalid input file.");
}

//The file contents are hard to transport through json.  We could probably escape everything, but this works too.
$ret = array();
$ret['zone'] = $_POST['zone'];
$ret['filename'] = $_POST['filename'];
$ret['mode'] = $mode;

print "LOADFILE|".json_encode($ret)."|".file_get_contents($src);

?>
