<?php

include("quests.php");

$src = $qdir.$_GET['src'];
if(strtolower(substr($src, -3)) == "lua") { $mode = "lua"; }
else { $mode = "perl"; }

if(!quest_dir($src))
{
    die("No input file.");
}

$fl = file_get_contents($src);

$doc = <<<END
<!DOCTYPE html>
<html lang="en">
<head>
<title>ACE in Action</title>
<style type="text/css" media="screen">
    #editor {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        font-size: 16px;
    }
</style>
<script>

var dirty = 0;

(function()
{
    window.parent.setFile('$src');
})();

</script>
</head>
<body>

<div id="editor">$fl</div>

<script src="ace-builds/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
var editor = ace.edit("editor");
editor.setTheme("ace/theme/monokai");
editor.setShowPrintMargin(false);
editor.getSession().setMode("ace/mode/$mode");
editor.resize();

editor.getSession().on('change', function(e)
{
    dirty = 1;
    window.parent.setChanged();
});

</script>
</body>
</html>
END;

print "$doc";

?>
