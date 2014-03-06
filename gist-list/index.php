<!DOCTYPE html>
<html>
<body>
<?php

require 'GistList.php';
$gists = new GistList('DerFichtl');

echo $gists->renderHtmlList();
echo '<hr />';
echo $gists->renderHtmlGist($gists[0]);
echo '<hr />';
echo $gists->renderHtmlComments($gists[0]);