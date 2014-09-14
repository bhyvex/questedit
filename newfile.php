<?php

include("quests.php");

$zone = $_GET['zone'];
$sid = $_GET['sid'];
$sname = $_GET['sname'];

if(strlen($sid) > 0 and $sid != "SID_INVALID") { $newfn = "$sid.$qext"; }
if(strlen($sname) > 0 and $sname != "SNAME_INVALID") { $newfn = "$sname.$qext"; }

print <<<END
<html>
<head>
<style>
body,td
{
    font-family: Verdana, Arial;
    font-size: 16px;
}
</style>
</head>
<body>
<form method=post action=createfile.php>
<h2>New File for $zone</h2><h2>
<input type=text name=filename value="$newfn"><br>
<input type=hidden name=zone value='$zone'>
<input type=submit value="Create">
</form>
</body>
</html>
END;

?>
