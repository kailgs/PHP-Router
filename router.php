<?php
    class Router {
        // rootPath = base directory, routes = array of the routes
        private static string $rootPath;
        private static array  $routes  = array();
        
        // url = without GET parameters, fullUrl = with GET parameters
        private static string $fullUrl;
        private static string $gUrl; 

        // constructor only needs the root path, if its not the emtpy string
        public function __construct(string $rootPath = "") {
            self::$rootPath = $rootPath;
        }

        // adds a new route
        public static function route(string $route, callable $func) {
            self::$routes[self::$rootPath.$route] = $func;
        }

        // reacts to the given route
        public static function run() {
            // Store url
            self::$fullUrl = $_SERVER['REQUEST_URI'];
            self::$gUrl    = (str_contains(self::$fullUrl, '?')) ? explode('?', self::$fullUrl)[0] : self::$fullUrl;

            // Get right route
            foreach (self::$routes as $bRout => $func) {
                if (self::checkUrl($bRout, self::$gUrl)) {
                    $params = self::extractParams($bRout, self::$gUrl);
                    call_user_func_array($func, $params);
                    break;
                }
            }
        }

        private static function checkUrl($bRoute, $gRoute) {
            if ( count(($urls = self::decomposeUrls($bRoute, $gRoute))[0]) != count($urls[1]) ) {
                return false;
            }

            for ($i = 0; $i < count($urls[0]); $i++) { 
                if (substr($urls[0][$i], 0, 1) != '{' && $urls[0][$i] != $urls[1][$i]) {
                    return false;
                }
            }
            
            return true;
        }

        public static function decomposeUrls(...$urls) {
            $res = array();
            foreach ($urls as $url) {
                $tmp = array();
                foreach(array_filter(explode("/", $url), 'strlen') as $partID => $part)
                    array_push($tmp, $part);
                array_push($res, $tmp);
            }
            return $res;
        }

        private static function extractParams($bUrl, $gUrl) {
            $urls = self::decomposeUrls($bUrl, $gUrl);
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