<?
namespace stradivari\class_array;

class ClassArray implements \ArrayAccess, \Iterator {
    protected $storage = array();

    public function __construct($data = null) {
        self::init($data);
    }
    public function init($data = []) {
        $this->storage = [];
        if (is_array($data) || $data instanceof \Traversable) {
            foreach ($data as $key => &$subData) {
                if (is_array($subData) || $subData instanceof \Traversable) {
                    $subData = new ClassArray($subData);
                }
                $this->storage[$key] = $subData;
            }
        }
        return $this;
    }
    public function current() {
        return current($this->storage);
    }
    public function next() {
        return next($this->storage);
    }
    public function rewind() {
        reset($this->storage);
    }
    public function valid() {
        return current($this->storage);
    }
    public function key() {
        return key($this->storage);
    }
    public function offsetExists($key) {
        return true;
    }
    public function offsetGet($key) {
        if ( !isset($this->storage[$key]) ) {
            if ( strpos($key, '__list') === false ) {
                return null;
            } else {
                return array();
            }
        }
        return $this->storage[$key];
    }
    public function offsetSet($key, $value) {
        $key = $key === null ? count($this->storage) : $key;
        if ($value === null) {
            $this->offsetUnset($key);
        } else if ($value instanceof ClassArray) {
            $this->storage[$key] = clone $value;
        } else if (is_array($value) || $value instanceof \Traversable) {
            $this->storage[$key] = new ClassArray($value);
        } else {
            $this->storage[$key] = $value;
        }
    }
    public function offsetUnset($key) {
        if ( isset($this->storage[$key]) ) {
            unset($this->storage[$key]);
        }
    }
    public function toArray() {
        $array = [];
        foreach ($this->storage as $key => $val) {
            if ($val instanceof self) {
                $val = $val->toArray();
            }
            $array[$key] = $val;
        }
        return $array;
    }
}
