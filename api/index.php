<?php
$db = require_once __DIR__ . "/lib/db.php";
require_once __DIR__ . "/class/User.php";
require_once __DIR__ . "/class/Article.php";
require_once __DIR__ . "/class/Rest.php";

// start session
session_start();

$user = new User($db);
$article = new Article($db);
$api = new Rest($user, $article);

// start api
$api->run();

// var_dump($_SERVER);
// $user->register('admin1', 'admin');
// var_dump($user->login('admin1', 'admin'));
// var_dump($ar->create('title', 'content', 1));
// var_dump($ar->view(0));
// var_dump($ar->edit(0, 'title', 'test', 1));
// var_dump($ar->delete(0, 1));
