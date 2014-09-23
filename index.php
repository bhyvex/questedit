<?php

include("quests.php");

$zone = $_GET['zone'];
$sid = $_GET['npcid'];
$sname = $_GET['npcname'];

$mode = "lua";

print <<<END
<html>
<head>
<style>

body
{
    margin: 0px;
    padding: 0px;
    font-family: Verdana, Arial;
    font-size: 14px;
}

#layout
{
    width: 100%;
    height: 100%;
}

#editor
{
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    font-size: 16px;
}

.z_entry
{
    cursor: default;
}

.z_entry:hover
{
    background-color: #cccccc;
}

.z_entry_sel
{
    background-color: #77ff77;
}

.f_entry
{
    cursor: default;
}

.f_entry:hover
{
    background-color: #cccccc;
}

.f_entry_sel
{
    background-color: #77ff77;
}

#savebn
{
    background-color: #77cc77;
    font-size: 20px;
}

</style>
<link rel="stylesheet" type="text/css" href="css/w2ui-1.4.1.min.css" />
<script src="js/jquery-1.9.1.js"></script>
<script src="js/w2ui-1.4.1.min.js"></script>
<script src="ace-builds/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>

var fn = "";
var qdir = '$qdir';
var zone = '$zone';
var sid = '$sid';
var sname = 'sname';
var editor;
var navui;

var questfiles = {};
var activezone = "";
var activefile = "";
var activetab = "welcome";
var filedata = {};
var lasttab = "";
var qs = {};

$(document).ready( function() {

    var lstyle = "border: 2px solid #cccccc;";
    var menutxt = "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
    menutxt += "<tr><td><div id=zonename>No Zone</div></td><td><div id=filename>No File</div></td><td><button type=button id=savebn onClick='saveFile();'>SAVE</button></td>";
    menutxt += "<td align=right>&nbsp;</td></tr>";
    menutxt += "</table>";

    $("#layout").w2layout({
        name: 'mainlayout',
        padding: 0,
        panels: [
            { type: 'top', size: 50, resizable: true, style: lstyle, content: '<div id=menubar>'+menutxt+'</div>' },
            { type: 'left', size: 250, resizable: true, style: lstyle },
            { type: 'main', style: lstyle, content: '<div id=editor>Welcome to the quest editing interface!</div>',
                tabs: {
                    active: 'welcome',
                    tabs: [
                        { id: 'welcome', caption: 'Start', closable: true }
                    ],
                    onClick: function(event) {
                        procTab(event);
                    },
                    onClose: function(event) {
                        closeTab(event);
                    }
                }
            }
        ]
    });

    editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.setShowPrintMargin(false);
    editor.getSession().setMode("ace/mode/$mode");
    editor.getSession().setUseWorker(false);
    editor.resize();

    editor.getSession().on('change', function(e)
    {
        if(lasttab != activetab) { return; }
        if(activetab =="welcome") { return; }
        filedata[activetab].clean = false;
        filedata[activetab].file = editor.getValue();
        var tpts = activetab.split("-");
        w2ui.mainlayout.get('main').tabs.set(activetab, { caption: tpts[1]+"*" });
    });

    w2ui.mainlayout.content('left', $().w2layout({
        name: 'navlayout',
        panels: [
            { type: 'top', size: '50%', resizable: true, style: lstyle, content: '<div id=navfiles></div>' },
            { type: 'main', size: '50%', resizable: true, style: lstyle, content: '<div id=navzones></div>' }
        ]
    }));

    getZones();
});

function procTab(e)
{
    var tn = e.target;
    if(activetab == tn) { return; }

    lasttab = activetab;
    activetab = tn;

    if(tn == "welcome")
    {
        editor.setValue("Welcome to the quest editing interface!");
        return;
    }

    var wasclean = filedata[tn].clean;

    editor.setValue(filedata[tn].file, -1);
    editor.getSession().setMode({ path: "ace/mode/" + filedata[tn].mode, v: Date.now() });
    editor.clearSelection();

    //Don't let tab switches put a bad dirty flag on the file.
    filedata[tn].clean = wasclean;
    lasttab = activetab;
    $("#filename").html("File: " + filedata[tn].filename);
    $("#zonename").html("Zone: " + filedata[tn].zone);
}

function closeTab(e)
{
    var tn = e.target;
    if(tn != activetab) { w2ui.mainlayout.get('main').tabs.click(tn); e.preventDefault(); return; }

    if(tn == "welcome") { editor.setValue(""); focusNewTab(tn); return; }

    if(!filedata[tn].clean)
    {
        var cl = confirm("File " + tn + " has unsaved changes.  Really close?");
        if(!cl)
        {
            e.preventDefault();
            return;
        }
    }

    editor.setValue("");
    delete filedata[tn];
    activetab = "";

    focusNewTab(tn);
}

function focusNewTab(ct)
{
    for(var x in w2ui.mainlayout.get('main').tabs.tabs)
    {
        if(w2ui.mainlayout.get('main').tabs.tabs[x].id != ct)
        {
            w2ui.mainlayout.get('main').tabs.click( w2ui.mainlayout.get('main').tabs.tabs[x].id );
        }
    }
}

function procData(d)
{
    var pts = d.split("|", 2);
    switch(pts[0])
    {
        case "QUESTDIR":
            questfiles = JSON.parse(pts[1]);
            updateNav();
            break;

        case "LOADFILE":
            var fd = JSON.parse(pts[1]);
            var tmp = pts[0] + pts[1];
            fd.file = d.substr(tmp.length+2);
            loadFile(fd);
            break;

        case "NEWFILE":
            var fd = JSON.parse(pts[1]);

            //The create script should already do this check.
            //We're just double-checking to keep things sane.
            for(var x in questfiles[fd.zone])
            {
                if(questfiles[fd.zone][x] == fd.filename) { alert("Duplicate Filename!"); return; }
            }
            questfiles[fd.zone].push(fd.filename);
            selectZone($("#z_"+fd.zone).get(), fd.zone);
            selectFile(null, fd.filename);
            break;

        case "SAVED":
            var ret = JSON.parse(pts[1]);
            var tn = "tab_"+ret.zone+"-"+ret.filename;
            if(!filedata[tn]) { alert("Missing file data after save!"); return; }
            filedata[tn].clean = true;
            w2ui.mainlayout.get('main').tabs.set(tn, { caption: filedata[tn].filename });
            break;

        default:
            alert(d);
            break;
    }
}

function updateNav()
{
    var zlist = "";
    for(var x in questfiles)
    {
        zlist += "<div class='z_entry' id='z_"+x+"' onClick='selectZone(this, \""+x+"\");'>"+x+"</div>";
    }
    $("#navzones").empty();
    $("#navzones").append(zlist);

    location.search.substr(1).split("&").forEach(function(item) { qs[item.split("=")[0]] = item.split("=")[1]})
    selectZone($("#z_"+qs.zone).get(), qs.zone);

    if(qs.file && !qs.create)
    {
        selectFile(null, qs.file);
    }

    if(qs.file && qs.create == "1")
    {
        $("#newfile").val(qs.file);
        newFile();
    }
}

function selectZone(sender, z)
{
    $(".z_entry_sel").removeClass("z_entry_sel");
    $(sender).addClass("z_entry_sel");
    activezone = z;
    $("#zonename").html("Zone: "+z)

    history.pushState("page_pop", '?zone=' + z, '?zone=' + z);

    updateFiles(z);
}

function updateFiles(z)
{
    var flist = "<input type=text name=newfile id=newfile><button type=button onClick='newFile();'>+</button><hr>";
    for(var x = 0; x < questfiles[z].length; x++)
    {
        var fn = questfiles[z][x];
        flist += "<div class='f_entry' onClick='selectFile(this, \""+fn+"\");'>"+fn+"</div>";
    }
    $("#navfiles").empty();
    $("#navfiles").append(flist);
}

function selectFile(sender, f)
{
    //$(".f_entry_sel").removeClass("f_entry_sel");
    //$(sender).addClass("f_entry_sel");
    activefile = f;
    var dt = {};
    dt.zone = activezone;
    dt.filename = f;

    history.pushState("page_pop", '?zone=' + activezone + "&file=" + f , '?zone=' + activezone + "&file=" + f);

    $.post("loadfile.php", dt, function(data) { procData(data); } );
}

function newFile()
{
    var fd = {};
    fd.zone = activezone;
    fd.filename = $("#newfile").val();

    $.post("createfile.php", fd, function(data) { procData(data); } );
}

function loadFile(fd)
{
    //If this is a file we already have open we should compare the two.
    //If they match, go to the existing tab, if not, prompt for what to do.
    fd.tab = "tab_" + fd.zone + "-" + fd.filename;

    if(w2ui.mainlayout.get('main').tabs.get(fd.tab) && filedata[fd.tab])
    {
        if(filedata[fd.tab].clean)
        {
            w2ui.mainlayout.get('main').tabs.click(fd.tab);
            return;
        }
        else
        {
            var replace = confirm("Source file does not match current file.  Load from original file?");
            if(!replace)
            {
                w2ui.mainlayout.get('main').tabs.click(fd.tab);
                return;
            }
            else
            {
                fd.clean = true;
                filedata[fd.tab] = fd;
                w2ui.mainlayout.get('main').tabs.set(fd.tab, { caption: fd.filename });
                activetab = "";
                w2ui.mainlayout.get('main').tabs.click(fd.tab);
                return;
            }
        }
    }

    fd.clean = true;
    filedata[fd.tab] = fd;

    w2ui.mainlayout.get('main').tabs.tabs.push({ id: fd.tab, caption: fd.filename, closable: true });
    w2ui.mainlayout.get('main').tabs.refresh();
    w2ui.mainlayout.get('main').tabs.click(fd.tab);
}

function getZones()
{
    $.get("questdir.php", function(data) { procData(data); });
}

function saveFile()
{
    if(activetab == "" || activetab == "welcome") { alert("Nothing to save!"); return; }
    if(!filedata[activetab]) { alert("No file data!"); return; }

    var pd = filedata[activetab];

    $.post("savefile.php", pd, function(data) { procData(data); } );
}

</script>
</head>
<body>
<div id="layout"></div>
</body>
</html>
END;
?>
