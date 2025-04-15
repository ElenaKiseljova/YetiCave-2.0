<?php
require_once 'helpers.php';
require_once 'set.php';

$pageContent = includeTemplate('pages/error.php');

$layoutData = [
  'title' => "Ошибка {$_SERVER['REDIRECT_STATUS']}",
  'content' => $pageContent,
  'dbConnection' => $con,
  'filePath' => __FILE__
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
