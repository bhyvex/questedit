<?php

include("quests.php");

print <<<END
<html>
<style>
body,td
{
    font-family: Verdana, Arial;
    font-size: 12px;
}
</style>
<body>
END;

$zone = $_GET['zone'];
$sid = $_GET['sid'];
$sname = $_GET['sname'];

if(strlen($sid) < 1) { $sid = "SID_INVALID"; }
if(strlen($sname) < 1) { $sname = "SNAME_INVALID"; }

$sdir = $qdir.$zone;

$ret = scandir($sdir);

$show = array();

foreach($ret as $r)
{
    if($sid == "SID_INVALID" and $sname == "SNAME_INVALID")
    {
        if(strtolower(substr($r, -3)) == "lua" or strtolower(substr($r, -2)) == "pl")
        {
            $show[] = $r;
        }
    }

    if(substr($r, 0, strlen($sid)) == $sid)
    {
        $show[] = $r;
    }

    if(substr($r, 0, strlen($sname)) == $sname)
    {
        $show[] = $r;
    }
}

print "<div id=menu><button type=button id=savebutton onClick='window.parent.doSave();'>SAVE</button><br>";
print "<a href='newfile.php?zone=$zone&sid=$sid&sname=$sname' target=editpane>Create File</a>";
print "<hr></div>";
print "<div id=files>";
foreach($show as $s)
{
    $us = urlencode($s);
    $fp = $zone."/$us";
    print "<a href=\"editor.php?src=$fp\" target=editpane>$s</a><br>";
}

print "</div></body></html>";

?>
