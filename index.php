<?php
include 'router.php';

#echo preg_match("{([a-zA-Z]+:)?\(.*?\)}", "(id)") . '<br>';
#echo preg_match('/{?([a-zA-Z]+:)?\(.*\)}?/', "{s(dd)}");
#echo preg_match('/^({[a-zA-Z_]+?:\(.*?\)})|(\(.*?\))$/', "()");
echo preg_match('/^{[a-zA-Z_]+?:\(.*?\)}$/', "{id:(/*.\/)}"); 

echo '<br>';
$p = array();
echo parse_str('http://example.com/gruppen/{id:(/*.\/)}', $p);
var_dump($p);
echo '<br>';

Router::setBasePath('/dpsg');

Router::route('/', function() {
    return "Das ist die Startseite!";
});

Router::route('/gruppen/{id:(/*.\/)}/{pepeLaugh}', function($id, $pepe) {
    echo "Das ist die Gruppenseite!<br>";
    return "Mit der Nummer: ".$id." UND DAS IST: ". $pepe;
});

Router::route('/gruppen/{id:(/*.\/)}', function($id) {
    echo "Das ist die ANDERE Gruppenseite!<br>";
    return "Mit der NUMMEr: ".$id;
});

Router::route('/kontakt', function() {
    return "Das ist die Kontaktseite!";
});

Router::run();
?>