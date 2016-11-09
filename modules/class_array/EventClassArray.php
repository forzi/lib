<?
namespace stradivari\class_array;

class EventClassArray extends ClassArray {
	protected $parent;
	protected $beforeSet;
	protected $onSet;
	protected $beforeUnset;
	protected $onUnset;

	protected function createSubclass($data) {
		return new static($data, $this, $this->beforeSet, $this->onSet, $this->beforeUnset, $this->onUnset);
	}
	
    public function __construct(
		$data = [], 
		self $parent = null, 
		callable $beforeSet = null, 
		callable $onSet = null, 
		callable $beforeUnset = null, 
		callable $onUnset = null
	) {
		$this->beforeSet = $beforeSet;
        $this->onSet = $onSet;
		$this->beforeUnset = $beforeUnset;
		$this->onUnset = $onUnset;
		$this->init($data);
    }
    public function offsetSet($key, $value) {
        $key = $key === null ? count($this->storage) : $key;
        if ($value === null) {
            $this->offsetUnset($key);
			return;
        } 
		if (is_callable($this->beforeSet)) {
			$beforeSet = $this->beforeSet;
		    $beforeSet($this, $key, $value);
		}
		if (is_array($value) || $value instanceof \Traversable) {			
            $this->storage[$key] = $this->createSubclass($value);
        } else {
            $this->storage[$key] = $value;
        }
		if (is_callable($this->onSet)) {
			$onSet = $this->onSet;
		    $onSet($this, $key, $value);
		}
    }
    public function offsetUnset($key) {
        if (!isset($this->storage[$key])) {
			return;
		}
		if (is_callable($this->beforeUnset)) {
			$beforeUnset = $this->beforeUnset;
			$beforeUnset($this, $key);
		}
		unset($this->storage[$key]);
		if (is_callable($this->onUnset)) {
			$onUnset = $this->onUnset;
			$onUnset($this, $key);
		}
    }
}
