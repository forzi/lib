<?
namespace stradivari\data_converter {
	class AbstractFileConverter extends Converter {
		protected $fileName;
		public function __get($name) {
			if ( $name == 'fileName' ) {
				return "{$this->fileName}.{$this->type}";
			}
			return parent::__get($name);
		}
	}
}
