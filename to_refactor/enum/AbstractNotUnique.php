<?
namespace stradivari\enum {
    abstract class AbstractNotUnique extends AbstractKey {
        public function offsetGet($key) {
			if ( !isset($this->value[$key]) ) {
				return null;
			}
            $keys = $this->value[$key];
            $result = array();
            foreach($keys as $key) {
                $result[] = &$this->entity[$key];
            }
            return $result;
        }
    }
}
