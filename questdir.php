<?php

include("quests.php");

$files = scandir($qdir);
$ret = array();

foreach($files as $f)
{
    if($f == "." or $f == ".." or $f == ".svn") { continue; }
    $ret[$f] = array();

    $qfiles = scandir($qdir.$f);
    foreach($qfiles as $qf)
    {
        if(strtolower(substr($qf, -2)) != 'pl' and strtolower(substr($qf, -3)) != 'lua') { continue; }
        $ret[$f][] = $qf;
    }
}

print "QUESTDIR|" . json_encode($ret);

?>
