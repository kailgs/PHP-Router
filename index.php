<?php
require('router.php');

$router = new Router("/dpsg");

$router->route('/', function() {
    echo "Das ist die Startseite!";
});

$router->route('/gruppen/{id}/{pepeLaugh}', function($id, $pepe) {
    echo "Das ist die Gruppenseite!<br>";
    echo "Mit der Nummer: ".$id." UND DAS IST: ". $pepe;
});

$router->route('/gruppen/{id}/jamoins', function($id) {
    echo "Das ist die ANDERE Gruppenseite!<br>";
    echo "Mit der NUMMEr: ".$id;
});

$router->route('/kontakt', function() {
    echo "Das ist die Kontaktseite!";
});

$router->run();
?>