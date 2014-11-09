<?
namespace stradivari\enum {
    class Enum extends AbstractUnique {
        private $keys = array();
        public $functions = array();
        
        public function __construct($data = null) {
            if ( !$data || !isset($data['entity']) ) {
                throw new \stradivari\enum\exception\IncorrectArgList();
            }
            parent::__construct($data['entity']);
            $this->value = array_keys($this->entity);
            if ( isset($data['keys']) ) {
                foreach ( $data['keys'] as $key ) {
                    if ( !isset($key['name']) || !$key['name'] ) {
                        throw new \stradivari\enum\exception\IncorrectArgList();
                    }
                    $this->{$key['name']} = $key;
                }
            }
            if ( isset($data['functions']) ) {
                if ( !(is_array($data['functions']) || $data['functions'] instanceof \Traversable) ) {
                    throw new \stradivari\enum\exception\IncorrectArgList();
                }
                $this->functions = $data['functions'];
            }
        }
        public function offsetGet($key) {
            $result = parent::offsetGet($key);
            if ( $this->functions ) {
                $data = array(
                    'entity' => $result,
                    'functions' => $this->functions,
                );
                return new Extended($data);
            }
            return $result;
        }
        public function __set($name, $key) {
            $key['type'] = isset($key['type']) ? $key['type'] : '';
            $key['type'] = strtolower($key['type']);
            if ( $key['type'] == 'sort' ) {
                $this->keys[$name] = new SortKey($this, $key);
            } else if ( $key['type'] == 'unique' ) {
                $this->keys[$name] = new FieldUnique($this, $key);
            } else {
                $this->keys[$name] = new FieldKey($this, $key);
            }
        }
        public function __get($name) {
            return $this->keys[$name];
        }
    }
}
