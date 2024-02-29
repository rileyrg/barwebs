<?php
function editorFieldHTML($f)
{
    return @file_get_contents("controls/html/".$f.".html",true);
}
?>