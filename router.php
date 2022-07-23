<?php
    class Router {
        // rootPath = base directory, routes = array of the routes
        private static string $basePath = '';
        private static array  $routes  = array();
        
        // url = without GET parameters, fullUrl = with GET parameters
        private static Route $url;

        // constructor only needs the root path, if its not the emtpy string
        public static function setBasePath(string $basePath) 
        {
            self::$basePath = $basePath;
        }

        // adds a new route
        public static function route(string $route, callable $func) 
        {
            array_push(self::$routes, [new Route($route, self::$basePath), $func]);
        }

        // reacts to the given route
        public static function run() 
        {
            // Store url
            self::$url = new Route($_SERVER['REQUEST_URI']);

            // Get right route
            foreach (self::$routes as $route) {
                if (Route::compareRoutes($route[0], self::$url)) {
                    $params = $route[0]->getParametersOf(self::$url);
                    echo call_user_func_array($route[1], $params);
                    break;
                }
            }
        }
    }

    class Route {
        // Route part => [POS, TYPE, VALUE, REGEXP] where type in ["path", "var"] and REGEXP by default the empty string
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
                array_push($res, ["pos" => $i, "type" => $type, "part" => $part, "regex" => '']);
            }

            return $res;
        }

        public function where($values, $regex="") 
        {
            if (!is_array($values)) 
                $values = [$values => $regex];

            foreach ($values as $part => $regex) {
                for ($i=0; $i < count($this->rParts); $i++) {
                    if ($part == $this->rParts[$i]["part"] && $this->rParts[$i]["type"] == 'var' ) {
                        $this->rParts[$i]["regex"] = $regex;
                        break;
                    }                
                }
            }
        }

        // bRoute = base route = The route stored by dev --- gRoute = given route = The route entered by user
        public static function compareRoutes(Route $bRoute, Route $gRoute)
        {
            if ($len = $bRoute->length() != $gRoute->length())
                return false;

            for ($i = 0; $i < $len; $i++) {
                if ($bRoute[$i]["type"] == 'path' && $bRoute[$i]["value"] != $gRoute[$i]["value"])
                    return false;

                if ($bRoute[$i]["type"] == 'var' && $bRoute[$i]["regex"] != '' && !preg_match($bRoute[$i]["regex"], $gRoute[$i]["part"])) {
                    return false;
                }
            }

            return true;
        }

        public function getParametersOf(Route $gRoute) 
        {
            $params = array();
            for ($i=0; $i<$this->length(); $i++) {
                if ($this->rParts[$i]["type"] == 'var')
                    array_push($params, $gRoute[$i]["part"]);
            }
            return $params;
        }

        public function length()
        {
            return count($this->rParts);
        }

        public function printRoute()
        {
            var_dump($this->route, $this->rParts);
        }
    }
?>