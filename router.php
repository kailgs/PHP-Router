<?php
    class Router {
        // rootPath = base directory, routes = array of the routes
        private string $rootPath;
        private array  $routes;
        
        // url = without GET parameters, fullUrl = with GET parameters
        private string $gUrl;
        private string $fullUrl;

        // constructor only needs the root path, if its not the emtpy string
        public function __construct(string $rootPath = "") {
            $this->rootPath = $rootPath;
            $this->routes   = array();
        }

        // adds a new route
        public function route(string $route, callable $func) {
            $this->routes[$this->rootPath.$route] = $func;
        }

        // reacts to the given route
        public function run() {
            $this->fullUrl = $_SERVER['REQUEST_URI'];
            $this->setURL($this->fullUrl);

            foreach ($this->routes as $bRout => $func) {
                if ($this->checkUrl($bRout, $this->gUrl)) {
                    $params = $this->extractParams($bRout, $this->gUrl);
                    call_user_func_array($func, $params);
                    break;
                }
            }
        }

        // stores the url without the get parameters
        private function setURL($fullUrl) {
            $this->gUrl = (str_contains($fullUrl, '?')) ? explode('?', $fullUrl)[0] : $fullUrl;
        }

        private function checkUrl($bRoute, $gRoute) {
            if ( count(($urls = $this->decomposeUrls($bRoute, $gRoute))[0]) != count($urls[1]) ) {
                return false;
            }

            for ($i = 0; $i < count($urls[0]); $i++) { 
                if (substr($urls[0][$i], 0, 1) != '{' && $urls[0][$i] != $urls[1][$i]) {
                    return false;
                }
            }
            
            return true;
        }

        public function decomposeUrls(...$urls) {
            $res = array();
            foreach ($urls as $url) {
                $tmp = array();
                foreach(array_filter(explode("/", $url), 'strlen') as $partID => $part)
                    array_push($tmp, $part);
                array_push($res, $tmp);
            }
            return $res;
        }

        private function extractParams($bUrl, $gUrl) {
            $urls = $this->decomposeUrls($bUrl, $gUrl);
            $params = array();
            for ($i = 0; $i < count($urls[0]); $i++) { 
                if (substr($urls[0][$i], 0, 1) == '{') {
                    array_push($params, $urls[1][$i]);
                }
            }
            return $params;
        }
    }
?>