<?php
include 'router.php';

Router::setBasePath('/dpsg');

Router::route('/', function() {
    return "Das ist die Startseite!";
});

Router::route('/gruppen/{id}/{test}', function($id, $test) {
    return "Gruppe mit der Nummer: ".$id." UND DAS IST: ". $test;
})->whereNumeric('id')->whereAlphaNumeric('test');

Router::route('/bestellungen/{bestellungID}/{kategorieID}', function($id, $kat) {
    return "BestellID: ".$id." mit der Kategorie: ". $kat;
})->whereNumeric(['bestellungID', 'kategorieID']);

Router::route('/gruppen/{id}', function($id) {
    return "<br>GRUPPE mit ID: " .$id . "<br>";
})->where('id', '[0-9]+');

Router::route('/user/{id}/{name}', function($id, $name) {
    return "<br>USER mit ID: " .$id . " und dem NAME: " . $name . "<br>";
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

Router::route('/kategorie/{id}/{name}', function($id, $name) {
    return "<br>KATEGORIE mit ID: " .$id . " und dem NAME: " . $name . "<br>";
})->where('id', '[0-9]+')->where('name', '[a-z]+');

Router::run();


?>