<?
namespace stradivari\controller {
    abstract class AbstractRouter {
        public static function __callStatic($calledMethod, $arguments = array()) {
            $calledClass = get_called_class();
            $reflection = new \ReflectionClass($calledClass);
            $staticMethods = $reflection->getMethods(\ReflectionMethod::IS_STATIC);
            $protectedMethods = $reflection->getMethods(\ReflectionMethod::IS_PROTECTED);

            $methods = array_intersect($staticMethods, $protectedMethods);
            $methods = array_map(function($value) {
                return (array) $value;
            }, $methods);

            $methodsEnum = new \stradivari\enum\Enum(
                array(
                    'entity' => &$methods,
                    'functions' => array(
                        'method' => function($value) {
                            $cur_method = explode('__', $value['name']);
                            return $cur_method[0];
                        }
                    )
                )
            );
            $methodsEnum->methods = array('field' => 'method');
            if ( !$methodsEnum->methods[$calledMethod] ) {
                throw new exception\NoSuchRequestMethod($calledMethod);
            }

            $httpMethodsEnum = new \stradivari\enum\Enum(
                array(
                    'entity' => $methodsEnum->methods[$calledMethod],
                    'functions' => array(
                        'http_method' => function($value) {
                            $cur_method = explode('__', $value['name']);
                            return isset($cur_method[1]) ? $cur_method[1] : false;
                        }
                    )
                )
            );
            $httpMethodsEnum->httpMethods = array('type' =>'unique', 'field' => 'http_method');
            $currentMethod = strtolower($_SERVER['REQUEST_METHOD']);
            if ( $currentMethod == 'options' ) {
                throw new exception\AllowedMethods($httpMethodsEnum->httpMethods->keys());
            }
            foreach (array($currentMethod, false) as $httpMethod) {
                if ( $httpMethodsEnum->httpMethods[$httpMethod]  ) {
                    $calledMethod = $httpMethodsEnum->httpMethods[$httpMethod]['name'];
                    call_user_func_array("{$calledClass}::{$calledMethod}", $arguments);
                    return;
                }
            }
            $httpMethodsEnum->httpMethods->ksort('asc');
            throw new exception\HttpMethodNotAllowed($httpMethodsEnum->httpMethods->keys());
        }
    }
}
