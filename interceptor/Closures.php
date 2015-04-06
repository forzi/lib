<?
namespace stradivari\interceptor {
    class Closures implements \ArrayAccess, \Iterator {
		private $closures = array();
		private $interceptor = null;
		private $name = '';
		
		public function __construct(InterceptorHandler $interceptor, $name) {
			$this->interceptor = $interceptor;
			$this->name = $name;
		}
        public function offsetExists($offset) {
            return isset($this->closures[$offset]);
        }
        public function offsetGet($offset) {
            return isset($this->closures[$offset]) ? $this->closures[$offset] : null;
        }
        public function offsetSet($offset, $value) {
			$this->extendedOffsetSet($offset, $value);
        }
        private function extendedOffsetSet($offset, callable $value = null) {
			if ( $value === null ) {
				unset($this->closures[$offset]);
			} else {
				$this->closures[$offset] = $value;
			}
			$this->interceptor->changeHandler($this->name);
		}
        public function offsetUnset($offset) {
            unset($this->closures[$offset]);
        }
        public function current() {
			return current($this->closures);
		}
		public function key() {
			return key($this->closures);
		}
		public function next() {
			return next($this->closures);
		}	
		public function valid() {
			return key($this->closures) !== null;
		}
		public function rewind() {
			reset($this->closures);
		}
        public function count() {
            return count($this->closures);
        }
        public function registred() {
            return array_keys($this->closures);
        }
    }
}
