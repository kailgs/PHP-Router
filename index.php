<?php
require('router.php');

$router = new Router("/dpsg");

$router->route('/', function() {

});

$router->route('/gruppen', function() {

});

$router->route('/kontakt', function() {

});

$router->printRoutes();
?>