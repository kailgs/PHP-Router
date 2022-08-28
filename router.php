<?php
    function stringArrayToString($values) {
        $res = "[";
        for ($i = 1; $i < count($values); $i++)
            $res .= $values[$i] . ", ";
        return $res . end($values) . "]";
    }

    class Router {
        // basePath = base directory, routes = array of the routes
        private static string $basePath = '';
        private static array  $routes   = array();
        private static Route  $url;
        private static $fallback;

        // Constructor only needs the root path, if its not the emtpy string
        public static function setBasePath(string $basePath) 
        {
            self::$basePath = $basePath;
        }

        // Adds a new route
        public static function route(string $route, callable $func) 
        {
            $nRoute = new Route($route, self::$basePath);
            array_push(self::$routes, [$nRoute, $func]);
            return $nRoute;
        }

        // Reacts to the given route
        public static function run() 
        {
            self::$url = new Route($_SERVER['REQUEST_URI']);
            foreach (self::$routes as $route) {
                if (Route::compareRoutes($route[0], self::$url)) {
                    $params = $route[0]->getParametersOf(self::$url);
                    echo call_user_func_array($route[1], $params);
                    return;
                }
            }

            // Handling 404
            http_response_code(404);
            if (self::$fallback) {
                echo call_user_func(self::$fallback);
            } else {
                echo "Page not found";
            }
        }

        public static function fallback(callable $func) {
            self::$fallback = $func;
        }
    }

    class Route {
        // Route part => [pos, type, value, filtertype, list, regexp, flength] where type in ["path", "var"] and REGEXP by default the empty string
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
                array_push($res, ["pos" => $i, "type" => $type, "part" => $part, "filtertype" => "none", "list" => array(), "regex" => '']);
            }

            return $res;
        }

        public function where($values, $regex="") 
        {            
            if (!is_array($values)) 
                $values = [$values => $regex];
            
            $setVariables = array();
            $gotVariables = array();
            foreach ($values as $part => $regex) {
                array_push($gotVariables, $part);
                for ($i=0; $i < count($this->rParts); $i++) {
                    if ($part == $this->rParts[$i]["part"] && $this->rParts[$i]["type"] == 'var' ) {
                        $this->rParts[$i]["filtertype"] = "regex";
                        $this->rParts[$i]["regex"] = '/^' . $regex . '$/';
                        array_push($setVariables, $part);
                        break;
                    }                
                }
            }

            if (count($setVariables) != count($values))
                trigger_error("Error in route: <b>". $this->route . "</b>: Not all given route parts were found. Given: ". stringArrayToString($gotVariables) . ", Found " . stringArrayToString($setVariables), E_USER_WARNING);

            return $this;
        }

        public function whereLength($values, $length=64)
        {
            if (!is_array($values)) 
                $values = [$values => $length];
            
            $setVariables = array();
            $gotVariables = array();
            foreach ($values as $part => $length) {
                array_push($gotVariables, $part);
                for ($i=0; $i < count($this->rParts); $i++) {
                    if ($part == $this->rParts[$i]["part"] && $this->rParts[$i]["type"] == 'var' ) {
                        $this->rParts[$i]["filtertype"] = "length";
                        $this->rParts[$i]["fLength"] = $length;
                        array_push($setVariables, $part);
                        break;
                    }                
                }
            }

            if (count($setVariables) != count($values))
                trigger_error("Error in route: <b>". $this->route . "</b>: Not all given route parts were found. Given: ". stringArrayToString($gotVariables) . ", Found " . stringArrayToString($setVariables), E_USER_WARNING);

            return $this;
        }

        public function whereIn($part, $values) {
            $setVariable = false;
            for ($i=0; $i < count($this->rParts); $i++) {
                if ($part == $this->rParts[$i]["part"] && $this->rParts[$i]["type"] == 'var' ) {
                    $this->rParts[$i]["filtertype"] = "list";
                    $this->rParts[$i]["list"] = $values;
                    $setVariable = true;
                    break;
                }                
            }

            if (!$setVariable)
                trigger_error("Error in route: <b>". $this->route . "</b>: The specified route part was not found. Given part: '{$part}'", E_USER_WARNING);

            return $this;
        }

        public function whereNumeric($parameters) 
        {
            if (!is_array($parameters)) $parameters = array($parameters);            
            foreach ($parameters as $parameter) {
                $values[$parameter] = '[0-9]+';
            }

            $this->where($values);
            
            return $this;
        }

        public function whereAlpha($parameters)
        {
            if (!is_array($parameters)) $parameters = array($parameters);
            foreach ($parameters as $parameter) {
                $values[$parameter] = '[a-zA-Z]+';
            }
            $this->where($values);

            return $this;
        }

        public function whereAlphaNumeric($parameters)
        {
            if (!is_array($parameters)) $parameters = array($parameters);
            foreach ($parameters as $parameter) {
                $values[$parameter] = '[a-zA-Z0-9]+';
            }
            $this->where($values);

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

        public static function compareRoutes(Route $tRoute, Route $gRoute)
        {
            // tRoute = template route = The route stored by dev 
            // gRoute = given route = The route entered by user

            $len = $tRoute->length();
            if ($len != $gRoute->length())
                return false;
            
            for ($i = 0; $i < $len; $i++) {
                $tPart = $tRoute->rParts[$i];
                $gPart = $gRoute->rParts[$i];
                $isVar = ($tPart["type"] == 'var') ? 1 : 0;

                if (!$isVar && $tPart["part"] != $gPart["part"])
                    return false;

                if ($isVar && $tPart["filtertype"] == "list" && !in_array($gPart["part"], $tPart["list"]))
                    return false;

                if ($isVar && $tPart["filtertype"] == "regex" && !preg_match($tPart["regex"], $gPart["part"]))
                    return false;

                if ($isVar && $tPart["filtertype"] == "length" && strlen($gPart["part"]) !== $tPart["fLength"])
                    return false;
            }

            return true;
        }

        public function getRoute() {
            return $this->route;
        }
    }
?>
