<?php

//Directory where quests are found
$qdir = "/opt/server/quests/";
//Preferred script type, either 'lua' or 'pl'
$qext = "lua";

function quest_dir($d)
{
    global $qdir;

    $len = strlen($qdir);

    $d = realpath($d);
    if(substr($d, 0, $len) != $qdir)
    {
        return(0);
    }
    return(1);
}

?>
