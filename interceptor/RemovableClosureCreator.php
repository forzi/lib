<?
namespace stradivari\interceptor {
    class RemovableClosureCreator {
        private $currentClosure = -1;
        private $closures = array();
        private $isActive = array();
        public static function create(callable $callable, &$isActive) {
            $callback = function() use ($callable, &$isActive) {
                $arguments = func_get_args();
                return $isActive ? call_user_func_array($callable, $arguments) : false;
            };
            return $callback;
        }
        public function __set($name, callable $callable = null) {
            if ( $name != 'closure' ) {
                throw new exception\CanNotSet();
            }
            if ( $this->currentClosure >= 0 ) {
                $this->isActive[$this->currentClosure] = false;
            }
            if ( is_callable($callable) ) {
                $this->currentClosure += 1;
                $this->isActive[$this->currentClosure] = true;
                $this->closures[$this->currentClosure] = self::create($callable, $this->isActive[$this->currentClosure]);
            }
        }
        public function __get($name) {
            if ( $name != 'closure' ) {
                throw new exception\CanNotGet();
            }
            return $this->isActive[$this->currentClosure] ? $this->closures[$this->currentClosure] : null;
        }
    }
}
