<?php

include("quests.php");

$dlist = scandir($qdir);

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

foreach($dlist as $d)
{
    if($d == "." or $d == ".." or $d == ".svn") { continue; }
    print "<a href='fsearch.php?zone=$d' target=navzone>$d</a><br>";
}

print "</body></html>";

?>
