<?php
    class Router {
        // rootPath = base directory, routes = array of the routes
        private static string $basePath;
        private static array  $routes  = array();
        
        // url = without GET parameters, fullUrl = with GET parameters
        private static string $fullUrl;
        private static string $gUrl; 

        // constructor only needs the root path, if its not the emtpy string
        public static function setBasePath(string $basePath = "") 
        {
            self::$basePath = $basePath;
        }

        // adds a new route
        public static function route(string $route, callable $func) 
        {
            self::$routes[self::$basePath.$route] = $func;
        }

        // reacts to the given route
        public static function run() 
        {
            // Store url
            self::$fullUrl = $_SERVER['REQUEST_URI'];
            self::$gUrl    = (str_contains(self::$fullUrl, '?')) ? explode('?', self::$fullUrl)[0] : self::$fullUrl;

            // Get right route
            foreach (self::$routes as $bRout => $func) {
                if (self::checkUrl($bRout, self::$gUrl)) {
                    $params = self::extractParams($bRout, self::$gUrl);
                    echo call_user_func_array($func, $params);
                    break;
                }
            }
        }

        // Compares a requested route with a saved route -> return true/false
        private static function checkUrl($bRoute, $gRoute) 
        {
            if ( count(($urls = self::decomposeUrls($bRoute, $gRoute))[0]) != count($urls[1]) ) {
                return false;
            }

            for ($i = 0; $i < count($urls[0]); $i++) { 
                if (!preg_match('/^{/', $urls[0][$i]) && $urls[0][$i] != $urls[1][$i]) {
                    return false;
                }

                // URL element is a variable and contains a regular expression
                if ( ($withName = preg_match('/^{[a-zA-Z_]+?:\(.*?\)}$/', $urls[0][$i])) || preg_match('/^{\(.*?\)}$/', $urls[0][$i])) {
                    $start = ($withName * strpos($urls[0][$i], ':')) + 2;
                    $end   = strlen($urls[0][$i]) - 2;
                    $expr  = substr($urls[0][$i], $start, ($end - $start));
                    echo 'URL: ' . $urls[0][$i] . '<br>' . 'EXPR: ' . $expr . '<br>' . 'WITH NAME: ' . $withName . '<br>';
                }
            }
            
            return true;
        }

        public static function decomposeUrls(...$urls) 
        {
            $res = array();
            foreach ($urls as $url) {
                $tmp = array();
                foreach(array_filter(explode("/", $url), 'strlen') as $partID => $part)
                    array_push($tmp, $part);
                array_push($res, $tmp);
            }
            return $res;
        }

        private static function extractParams(string $bUrl, string $gUrl) 
        {
            $urls = self::decomposeUrls($bUrl, $gUrl);
            $params = array();
            for ($i = 0; $i < count($urls[0]); $i++) { 
                if (preg_match('/^{.*/', $urls[0][$i])) {
                    array_push($params, $urls[1][$i]);
                }
            }
            return $params;
        }
    }

    class Route {
        // Route part => [part] = [TYPE, VALUE, REGEXP] where type in ["path", "var"] and REGEXP by default the empty string
        public  array  $rParts;
        private string $route;

        public function __construct(string $route, string $basePath = "")
        {
            $this->route    = $basePath . $route;
            $this->rParts   = $this->desconstructRoute($route);
        }

        private function desconstructRoute(string $route)
        {
            $res  = array();
            foreach (array_values(array_filter(explode("/", $route), 'strlen')) as $i => $part) {
                $type = str_starts_with($part, '{') ? 'var' : 'path';
                $part = ($type == 'var') ? trim($part, '{}') : $part;
                $res[$part] = [$type, $i];
            }

            return $res;
        }

        public function where($expressions, $regExp="") 
        {
            if (!is_array($expressions)) 
                $expressions = array([$expressions => $regExp]);

            foreach ($expressions as $expr) {
                foreach ($this->rParts as $part) {
                    
                }
            }

        }

        public function printRoute()
        {
            var_dump($this->route, $this->rParts);
        }
    }
?>