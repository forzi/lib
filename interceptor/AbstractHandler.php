<?
namespace stradivari\interceptor {
    abstract class AbstractHandler implements \ArrayAccess {
        protected static $instances = array();
        protected $handlerRegistrator;
        protected $functions = array();
        protected function __construct(callable $handlerRegistrator) {
            $this->handlerRegistrator = $handlerRegistrator;
        }
        protected function __clone() {}
        public function __wakeup() {
            throw new exception\SingletonException();
        }
        public static function getInstance() {
            $handlerRegistrator = func_get_arg(0);
            $key = serialize($handlerRegistrator);
            if ( !array_key_exists($key, self::$instances) ) {
                self::$instances[$key] = new static($handlerRegistrator);
            }
            return self::$instances[$key];
        }
        public function offsetExists($offset) {
            return true;
        }
        public function offsetGet($offset) {
            return isset($this->functions[$offset]) ? $this->functions[$offset]->closure : null;
        }
        public function offsetSet($offset, $value) {
            if ( !isset($this->functions[$offset]) ) {
                $this->functions[$offset] = new RemovableClosureCreator();
            }
            $this->functions[$offset]->closure = $value;
            if ( $value ) {
                call_user_func($this->handlerRegistrator, $this->functions[$offset]->closure);
            }
        }
        public function offsetUnset($offset) {
            $this->offsetSet($offset, null);
        }
        public function everRegistred() {
            return array_keys($this->functions);
        }
        public function registred() {
            $result = array();
            foreach ( $this->functions as $key => $value ) {
                if ( $this[$key] ) {
                    $result[] = $key;
                }
            }
            return $result;
        }
    }
}
