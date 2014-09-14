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
    die("Invalid Filename: $fpath$fn");
}

if(file_exists($fpath.$fn))
{
    die("File Exists!");
}

$fh = fopen($fpath.$fn, 'w');
fwrite($fh, "$cmt $zone - $fn\n");
fclose($fh);

print <<<END
<script>
window.parent['navzone'].location.reload();
</script>
<meta http-equiv=refresh content='1;URL=editor.php?src=$zone/$fn'>
END;

?>
