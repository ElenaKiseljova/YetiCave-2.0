<?php
require_once 'helpers.php';
require_once 'data.php';

$page_content = include_template('main.php', ['categories' => $categories,  'lots' => $lots]);

$layout_content = include_template('layout.php', ['title' => 'Главная', 'content' => $page_content, 'categories' => $categories,]);

print($layout_content);
