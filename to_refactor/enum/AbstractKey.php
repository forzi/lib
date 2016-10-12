<?
namespace stradivari\enum {
    abstract class AbstractKey implements \ArrayAccess, \Iterator {
        protected $entity = null;
        protected $value = array();
        
        public function __construct(&$entity) {
            if ( !(is_array($entity) || $entity instanceof \Traversable) ) {
                throw new \stradivari\enum\exception\IncorrectArgList();
            }
            $this->entity = &$entity;
        }        
        public function offsetExists($key) {
            return isset($this->value[$key]);
        }
        public function offsetGet($key) {}
        public function offsetSet($key, $value) {
            throw new \stradivari\enum\exception\CanNotSet();
        }
        public function offsetUnset($key) {
            throw new \stradivari\enum\exception\CanNotUnset();
        }
        public function current() {
            return $this[$this->key()];
        }
        public function key() {
            return key($this->value);
        }
        public function next() {
            return next($this->value);
        }
        public function rewind() {
            reset($this->value);
        }
        public function valid() {
            return isset($this->value[$this->key()]);
        }
        public function ksort($order = 'asc') {
            $this->rewind();
            $order = strtolower($order);
            $sortFunction = function(&$first, &$second) use (&$order) {
                $result = AbstractSort::compareArguments($first, $second);
                return $order == 'asc' ? $result : -$result;
            };
            return uksort($this->value, $sortFunction);
        }
		public function count() {
			return count($this->value);
		}
        public function keys() {
            return array_keys($this->value);
        }
    }
}
