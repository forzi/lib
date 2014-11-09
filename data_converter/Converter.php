<?
namespace stradivari\data_converter {
	class Converter {
		protected $data = null;
		protected $type = null;
		public static $inPlace = true;
		
		public function __construct($data, $type) {
			$this->data = $data;
			$this->type = mb_strtolower($type);
		}
		public function __call($newType, $params) {
			$obj = self::$inPlace ? $this : clone $this;
			$newType = mb_strtolower($newType);
			if ( $obj->type == $newType ) {
				return $obj;
			}
			if ( $obj->type == 'array' || $newType == 'array' ) {
				$method = "{$obj->type}2{$newType}";
				$obj->data = ConvertionHelper::$method($obj->data);
				$obj->type = $newType;
				return $obj;
			}
			return $obj->array()->$newType();
		}
		public function __get($name) {
			if ( $name == 'data' ) {
				return $this->data;
			}
			return null;
		}
	}
}
