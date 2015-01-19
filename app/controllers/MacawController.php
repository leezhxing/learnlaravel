<?php namespace NoahBuscher\Macaw;

/**
 * method static Macaw get(string $route, Callable $callback)
 * method static Macaw post(string $route, Callable $callback)
 * method static Macaw put(string $route, Callable $callback)
 * method static Macaw delete(string $route, Callable $callback)
 * method static Macaw options(string $route, Callable $callback)
 * method static Macaw head(string $route, Callable $callback)
 */
class Macaw {
	public static $halts = false;
	public static $routes = array();
	public static $methods = array();
	public static $callbacks = array();
	public static $patterns = array(
		':any' => '[^/]+',
		':num' => '[0-9]+',
		':all' => '.*',
	);
	public static $error_callback;
	/**
	 * Defines a route w/ callback and method
	 * 注册路由，把注册的方法(GET,POST..)，uri，closure分别push到对应的数组中
	 */
	public static function __callstatic( $method, $params ) {
		$uri = $params[0];
		$callback = $params[1];
		array_push( self::$routes, $uri );
		array_push( self::$methods, strtoupper( $method ) );
		array_push( self::$callbacks, $callback );
	}
	/**
	 * Defines callback if route is not found
	 * 可以自定义没找到路由时执行的方法
	 */
	public static function error( $callback ) {
		self::$error_callback = $callback;
	}

	/**
	 * 自定义是否匹配到一次就停止，true停止，false不停止即可以定义多个同名路由，通过foreach全部执行
	 * @param  boolean $flag true / false
	 * @return none        
	 */
	public static function haltOnMatch( $flag = true ) {
		self::$halts = $flag;
	}
	/**
	 * Runs the callback for the given request
	 * 根据当前的uri匹配对应的路由并执行
	 */
	public static function dispatch() {
		$uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ); //路径部分(包括前边的/)，不包括参数
		$method = $_SERVER['REQUEST_METHOD'];   //方法 GET / POST / PUT / DELETE
		$searchs = array_keys( static::$patterns );
		$replaces = array_values( static::$patterns );
		$found_route = false;

		//check if route is defined without regex,检查是否定义了路由(非:any,:all形式的)
		if ( in_array( $uri, self::$routes ) ) {
			$route_pos = array_keys( self::$routes, $uri ); //返回匹配路由的键值，可能多个(同名路由)
			foreach ( $route_pos as $route ) {
				if ( self::$methods[$route] == $method ) {  //寻找路由对应的方法名(GET,POST...),确定是否注册。
					$found_route = true;
					//if route is not an object,检测对应闭包函数是function还是controller route
					if ( !is_object( self::$callbacks[$route] ) ) {
						//grab all parts based on a / separator 控制器路由
						$parts = explode( '/', self::$callbacks[$route] );
						//collect the last index of the array
						$last = end( $parts );
						//grab the controller name and method call
						$segments = explode( '@', $last );
						//instanitate controller
						$controller = new $segments[0]();
						//call method
						$controller->$segments[1]();
						if ( self::$halts )return;   //匹配一次就停止?

					}else {
						//call closure
						call_user_func( self::$callbacks[$route] );  
						if ( self::$halts )return;
					}

				}

			}
		}else {
			//check if defined with regex 是否注册了正则路由(:any,:num..)
			$pos = 0;
			foreach ( self::$routes as $route ) {
				if ( strpos( $route, ':' ) !== false ) {
					$route = str_replace( $searchs, $replaces, $route );
				}
				if ( preg_match( '#^'.$route.'$#', $uri, $matched ) ) {
					if ( self::$methods[$pos] == $method ) {
						$found_route = true;
						array_shift( $matched );
						if ( !is_object( self::$callbacks[$pos] ) ) {
							//grab all parts based on a / separator
							$parts = explode( '/', self::$callbacks[$pos] );
							//collect the last index of the array
							$last = end( $parts );
							//grab the controller name and method call
							$segments = explode( '@', $last );
							//instanitate controller
							$controller = new $segments[0]();
							//call method and pass any extra parameters to the method
							$controller->$segments[1]( implode( ",", $matched ) );
							if ( self::$halts ) {
								return;
							}else {
								call_user_func_array( self::$callbacks[$pos], $matched );
								if ( self::$halts ) return;
							}

						}
					}
				}
				$pos++;
			}
		}

		//return the error callback if the route was not found
		if ( $found_route == false ) {
			if ( !self::$error_callback ) {
				self::$error_callback = function() {
					header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found" ); //请求页面时通信协议的名称和版本。例如，“HTTP/1.0”。
					echo '404';
				}
			}
			call_user_func( self::$error_callback );

		}
	}
}
