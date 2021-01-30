<?php
ini_set('display_errors', 1);
require '../vendor/autoload.php';
$request = $_SERVER['REQUEST_URI'];
//var_dump($request);
$app = new \App\App;


// echo $twig->render('test.html.twig');

$app->run($request);