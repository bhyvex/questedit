<?php

include("quests.php");

$zone = $_GET['zone'];
$sid = $_GET['npcid'];
$sname = $_GET['npcname'];

print <<<END
<script src="jquery-1.9.1.js"></script>
<script>

var fn = "";
var qdir = '$qdir';

function setFile(f)
{
    fn = f;
    fnp = f.split("/");
    fp = fnp[fnp.length-1];
    window.frames['navzone'].document.getElementById('savebutton').innerHTML = "SAVE " + fp;
}

function setChanged()
{
    $(window.frames['navzone'].document.getElementById('savebutton')).css("background-color", "#FF7777");
}

function doSave()
{
    var content = window.frames['editpane'].editor.getValue();
    var qs = {};
    qs.filename = fn;
    qs.content = content;
    $.post("savefile.php", qs, function(data) { procData(data); });
}

function procData(d)
{
    var pts = d.split("|");
    switch(pts[0])
    {
        case "SAVED":
            var svfile = pts[1].substr(qdir.length);
            window.open("editor.php?src="+ encodeURIComponent(svfile), "editpane");
            $(window.frames['navzone'].document.getElementById('savebutton')).css("background-color", "#00FF00");
            break;

        default:
            alert(d);
            break;
    }
}

</script>
<frameset cols=250,*>
<frame name=navzone id=navzone src="fsearch.php?zone=$zone&sid=$sid&sname=$sname"></frame>
<frame name=editpane id=editpane src="editor.php"></frame>
</frameset>
END;

?>
