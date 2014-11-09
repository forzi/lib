<?
namespace stradivari\enum {
    class FieldUnique extends AbstractUnique {
        protected $field = null;
        
        public function __construct(&$entity, $attributes = array()) {
			$field = isset($attributes['field']) ? $attributes['field'] : null;
            if ( !$field ) {
                throw new \stradivari\enum\exception\IncorrectArgList();
            }
            $this->field = $field;
            parent::__construct($entity);
            foreach ( $entity as $key => $value ) {
                $currValue = isset($value[$field]) ? $value[$field] : null;
                $currValue = is_bool($currValue) ? (int) $currValue : $currValue;
                $currValue = is_string($currValue) || is_numeric($currValue) ? $currValue : null;
                if ( $currValue !== null && !isset($this->value[$currValue]) ) {
                    $this->value[$currValue] = $key;
                }
            }
        }
    }
}
