<?php
include 'router.php';

$router = new Router("/dpsg");

Router::route('/', function() {
    echo "Das ist die Startseite!";
});

Router::route('/gruppen/{id}/{pepeLaugh}', function($id, $pepe) {
    echo "Das ist die Gruppenseite!<br>";
    echo "Mit der Nummer: ".$id." UND DAS IST: ". $pepe;
});

Router::route('/gruppen/{id}/jamoins', function($id) {
    echo "Das ist die ANDERE Gruppenseite!<br>";
    echo "Mit der NUMMEr: ".$id;
});

Router::route('/kontakt', function() {
    echo "Das ist die Kontaktseite!";
});

Router::run();
?>