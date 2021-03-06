<?php 
			
		 /**
		 * Khan - Component (Router) - A fast, easy and flexible router system for PHP
		 *
		 * @author      PaulaoDev <jskhanframework@gmail.com>
		 * @copyright   (c) PaulaoDev 
		 * @link        https://github.com/PaulaoDev/Router
		 * @license     MIT
		 */
    
      namespace KhanComponent\Router;
			use \KhanComponent\Http\Response as Response;
			use \KhanComponent\Http\Request as Request;

			@session_start();

      class Router {
        
          use \KhanComponent\RegexEngine\RegexEngine;
        
          private static $instance = null,
												 $uses = [],
                         $routes = [],
                         $config = [],
                         $delete, $put;
        
          public static function create($config = ''){
              if(self::$instance === null){
                 self::$instance = new self($config);
              }
              return self::$instance;
          }
        
          protected function __construct($config = null){
              $server = $_SERVER;
              self::$config["uri"] = Router::get_uri();
              self::$config["path"] = (strripos($server["REQUEST_URI"], "?")) ? explode("?", $server["REQUEST_URI"])[0] : $server["REQUEST_URI"];
              if(isset($config['sub_dir'])){ self::$config["path"] = str_replace($config['sub_dir'], '', self::$config["path"]); }
              self::$config["method"] = (isset($server["REQUEST_METHOD"])) ? $server["REQUEST_METHOD"] : "GET";
              if(in_array(self::$config["method"], ["delete","put"])){
                if(self::$config["method"] === "delete"):
									parse_str(file_get_contents('php://input'), self::$delete);
								endif;
								if(self::$config["method"] === "put"):
									parse_str(file_get_contents('php://input'), self::$put);
								endif;
              }
              if(!is_null($config) && gettype($config) == "array"){
                self::$config = array_merge(self::$config, $config);
              }
          }
        
          public static function get_uri(){
              $server = $_SERVER;
              $protocol = (isset($server["REQUEST_SCHEME"])) ? $server["REQUEST_SCHEME"] : ((isset($server["HTTP_X_FORWARDED_PROTO"])) ? $server["HTTP_X_FORWARDED_PROTO"] : "http");
              $domain = (isset($server['HTTP_HOST'])) ? $server['HTTP_HOST'] : $server["SERVER_NAME"];
              $path = (isset($server["REQUEST_URI"])) ? $server["REQUEST_URI"] : "/";
              return "{$protocol}://{$domain}{$path}";
          }
        
          public static function clean_request(){
              $_POST = filter_input_array(INPUT_POST, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			        $_GET = filter_input_array(INPUT_GET, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
          }
        
          public static function has($route, $type){
              return !isset(self::$routes[$type][$route]);
          }
        
          public static function type($type){
              return gettype($type);
          }
				
					public function set($name, $callback){
							if(!isset(self::$uses[$name])){
								self::$uses[$name] = $callback;
							}
					}
				
					private function uses(){
						return self::$uses;
					}
        
          public function class_invoked($string, $data){
              $class = $string;
              $finish = '';
              if(strripos($class, "@")){
                list($className, $fun) = explode('@', $class);
                $finish = new $className;
                call_user_func_array([$finish, $fun], $data);
              }
              elseif(strripos($class, "::")){
                call_user_func_array($class, $data);
              }
              else{
                new $class($data);
              }
          }
        
          private function type_trate($type, $callback, $data){
              if($type == "object"){
                call_user_func_array($callback, $data);
              }
              elseif($type == "string"){
                $this->class_invoked($callback, $data);
              }
          }
        
          private function trate_callback($callback, $data){
              $type = gettype($callback);
              if($type == "object"){
                $this->type_trate($type, $callback, $data);
              }
              elseif($type == "string"){
                $this->type_trate($type, $callback, $data);
              }
              elseif($type == "array"){
                foreach ($callback as $key => $value) {
                  $t = gettype($value);
                  $this->type_trate($t, $value, $data);
                }
              }
          }
        
          public static function get($route, $call = null, $method = 'GET'){
               $scope = Router::create();
               if(Router::has($route, $method)){
                  $type = Router::type($route);
                  if($type === "string"){
                    self::$routes[$method][$route] = $call;
                  } elseif($type === "array"){
                    foreach ($route as $key => $routeName) {
                      if($callback == null){
                        self::$routess[$method][$key] = $routeName;
                      }else{
                        self::$routess[$method][$key] = $callback;
                      }
                    }
                  }
               }
              return $scope;
          }
        
          public static function post($route, $call = null, $method = 'POST'){
              $scope = Router::create();
               if(Router::has($route, $method)){
                  $type = Router::type($route);
                  if($type === "string"){
                    self::$routes[$method][$route] = $call;
                  } elseif($type === "array"){
                    foreach ($route as $key => $routeName) {
                      if($callback == null){
                        self::$routess[$method][$key] = $routeName;
                      }else{
                        self::$routess[$method][$key] = $callback;
                      }
                    }
                  }
               }
              return $scope;
          }
        
          public static function delete($route, $call = null, $method = 'DELETE'){
               if(Router::has($route, $method)){
                  $type = Router::type($route);
                  if($type === "string"){
                    self::$routes[$method][$route] = $call;
                  } elseif($type === "array"){
                    foreach ($route as $key => $routeName) {
                      if($callback == null){
                        self::$routess[$method][$key] = $routeName;
                      }else{
                        self::$routess[$method][$key] = $callback;
                      }
                    }
                  }
               }
              return $scope;
          }
        
          public static function put($route, $call = null, $method = 'PUT'){
              $scope = Router::create();
               if(Router::has($route, $method)){
                  $type = Router::type($route);
                  if($type === "string"){
                    self::$routes[$method][$route] = $call;
                  } elseif($type === "array"){
                    foreach ($route as $key => $routeName) {
                      if($callback == null){
                        self::$routess[$method][$key] = $routeName;
                      }else{
                        self::$routess[$method][$key] = $callback;
                      }
                    }
                  }
               }
              return $scope;
          }
        
          public static function params($route, $call = null, $method = 'PARAMS'){
             $scope = Router::create();
             if(Router::has($route, $method)){
                $type = Router::type($route);
                if($type === "string"){
                  self::$routes[$method][$route] = $call;
                } elseif($type === "array"){
                  foreach ($route as $key => $routeName) {
                    if($callback == null){
                      self::$routess[$method][$key] = $routeName;
                    }else{
                      self::$routess[$method][$key] = $callback;
                    }
                  }
                }
             }
            return $scope;
          }
        
        public function dispatch(){
          
            $uri = self::$config["path"];
            
            $metodo = self::$config["method"];
						$param_receive = false;
					
						if(in_array('PARAMS', array_keys(self::$routes))){
							$param = $this->build(self::$routes["PARAMS"], $uri);
							if(is_array($param)){
								$param_receive = $param;
								$metodo = "PARAMS";
							}
						}
          
            // Limpa Request [ GET & POST]
            if(in_array("clean_request", array_keys(self::$config))){
							if(self::$config["clean_request"]): Router::clean_request(); endif;
            }
          
            // Limpa URL
						if(in_array("url_filter", array_keys(self::$config))){
							if(self::$config["url_filter"]): $uri = strip_tags(addslashes($uri)); endif;
            }
            
            if(in_array($metodo, array_keys(self::$routes))){
							
							if(in_array($uri, array_keys(self::$routes[$metodo])) || in_array($param_receive["rota"], array_keys(self::$routes[$metodo]))){
									
									$data_receive = [
										"post" => $_POST,
										"get" => $_GET,
										"params" => (is_array($param_receive['params'])) ? $param_receive['params'] : [],
										"session" => $_SESSION,
										"files" => $_FILES,
										"put" => (is_array(self::$put)) ? self::$put : [],
										"delete" => (is_array(self::$delete)) ? self::$delete : []
									];
									$fn = '';
									if(is_array($param_receive)){
										$fn = self::$routes[$metodo][$param_receive['rota']];
									}else{
										$fn = self::$routes[$metodo][$uri];
									}
									$this->trate_callback($fn, [
										new Request($data_receive, Router::get_uri()),
										new Response(self::$uses)
									]);
							}
						}
            
        }
        
        
      }