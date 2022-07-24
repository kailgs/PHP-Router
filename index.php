<?php
include 'router.php';

Router::setBasePath('/dpsg');

Router::route('/', function() {
    return "Das ist die Startseite!";
});

Router::route('/gruppen/{id}/{test}', function($id, $pepe) {
    echo "Das ist die Gruppenseite!<br>";
    return "Mit der Nummer: ".$id." UND DAS IST: ". $pepe;
});

Router::route('/gruppen/{id}', function($id) {
    return "<br>GRUPPE mit ID: " .$id . "<br>";
})->where('id', '[0-9]+');

Router::route('/user/{id}/{name}', function($id, $name) {
    return "<br>USER mit ID: " .$id . " und dem NAME: " . $name . "<br>";
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

Router::run();


?>