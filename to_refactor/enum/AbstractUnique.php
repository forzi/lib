<?
namespace stradivari\enum {
    abstract class AbstractUnique extends AbstractKey {
        public function offsetGet($key) {
			if ( !isset($this->value[$key]) ) {
				return null;
			}
            $key = $this->value[$key];
            $result = $this->entity[$key];
            return $result;
        }
    }
}
