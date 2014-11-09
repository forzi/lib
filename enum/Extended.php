<?
namespace stradivari\enum {
    class Extended extends AbstractUnique {
        public $functions = array();
        
        public function __construct($data) {
            if ( !isset($data['entity']) ) {
                throw new \stradivari\enum\exception\IncorrectArgList();
            }
            parent::__construct($data['entity']);
            $keys = array();
            foreach ($data['entity'] as $key=>$val) {
                $keys[$key] = $key;
            }
            $this->value = $keys;
            if ( isset($data['functions']) ) {
                if ( !(is_array($data['functions']) || $data['functions'] instanceof \Traversable) ) {
                    throw new \stradivari\enum\exception\IncorrectArgList();
                }
                $this->functions = $data['functions'];
            }
        }
        public function offsetExists($key) {
            if ( isset($this->functions[$key]) ) {
                return true;
            }
            return parent::offsetExists($key);
        }
        public function offsetGet($key) {
            if ( isset($this->functions[$key]) ) {
                $function = $this->functions[$key];
                return $function($this->entity);
            }
            return parent::offsetGet($key);
        }
    }
}
