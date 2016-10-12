<?
namespace stradivari\pool {
    class Pool extends \stradivari\class_array\ClassArray {
        public static $instantiators = array(
            'getInstance'
        );
        public function __invoke($key) {
            return $this->offsetGet($key);
        }
        public function offsetGet($key) {
            $closure = parent::offsetGet($key);
            if ( !$closure ) {
                $closure = function() use($key, $obj) {
                    $argument__list = func_get_args();
                    return $this->closureTemplate($key, $argument__list);
                };
                $this[$key] = $closure;
            }
            return $closure;
        }
        private function closureTemplate($calledClass, $argument__list) {
            $className = right_cut($calledClass, '__AxWrapper');
            if ( $calledClass != $className ) {
                $calledClass = '\\' . __NAMESPACE__ . '\\AxWrapper';
                $argument__list = array($className);
            }
            $reflection = new \ReflectionClass($calledClass);
            foreach ( self::$instantiators as $instantiator ) {
                try {
                    $hasInstantiator = $reflection->getMethod($instantiator);
                    return call_user_func_array("{$calledClass}::{$instantiator}", $argument__list);
                } catch ( \ReflectionException $e ) {}
            }
            return $reflection->newInstanceArgs($argument__list);
        }
    }
}
