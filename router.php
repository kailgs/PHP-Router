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
            $nRoute = new Route($route, self::$basePath);
            array_push(self::$routes, [$nRoute, $func]);
            return $nRoute;
        }

        // reacts to the given route
        public static function run() 
        {
            self::$url = new Route($_SERVER['REQUEST_URI']);
            
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
            $this->rParts   = $this->desconstructRoute($this->route);
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
                        $this->rParts[$i]["regex"] = '/^' . $regex . '$/';
                        // var_dump($this->rParts);
                        break;
                    }                
                }
            }

            return $this;
        }

        // Needs a route template and a user entered route
        public function getParametersOf(Route $gRoute) 
        {
            $params = array();
            for ($i=0; $i<$this->length(); $i++) {
                if ($this->rParts[$i]["type"] == 'var') {
                    array_push($params, $gRoute->rParts[$i]["part"]);
                }
            }
            return $params;
        }

        public function length()
        {
            return count($this->rParts);
        }

        // tRoute = template route = The route stored by dev --- gRoute = given route = The route entered by user
        public static function compareRoutes(Route $tRoute, Route $gRoute)
        {
            $len = $tRoute->length();
            if ($len != $gRoute->length())
                return false;
            
            for ($i = 0; $i < $len; $i++) {
                if ($tRoute->rParts[$i]["type"] == 'path' && $tRoute->rParts[$i]["part"] != $gRoute->rParts[$i]["part"])
                    return false;

                if ($tRoute->rParts[$i]["type"] == 'var' && $tRoute->rParts[$i]["regex"] != '' && !preg_match($tRoute->rParts[$i]["regex"], $gRoute->rParts[$i]["part"])) {
                    return false;
                }
            }

            return true;
        }
    }
?>