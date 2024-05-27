<?php 

function nav($baseUrl, $navName, $navNameHtml = '')
{
    $nav = '';
    $navNameHtml = ($navNameHtml != '') ? $navNameHtml : ucwords($navName);
    $url = end(explode('/', $_SERVER['REDIRECT_URL']));
    if($url == $navName) {
        $nav = "<a class=\"nav-link active\" aria-current=\"page\" href=\"$baseUrl/$navName\">$navNameHtml</a>";
    } else {
        $nav = "<a class=\"nav-link\" href=\"$baseUrl/$navName\">$navNameHtml</a>";
    }

    return $nav;
}