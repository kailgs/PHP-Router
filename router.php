<?php
    class Router {
        private string $root;
        private array $routes;

        public function __construct(string $root) {
            $this->root   = $root;
            $this->routes = array();
        }

        public function route(string $route, callable $func) {
            $this->routes[$route] = $func;
        }

        public function printRoutes() {
            echo '<b>Saved Routes:</b><br>';
            foreach ($this->routes as $path => $func) {
                echo $path.'<br>';
            }
        }
    }
?>