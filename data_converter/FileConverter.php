<?
namespace stradivari\data_converter {
	class FileConverter extends AbstractFileConverter {
		public function __construct($file) {
			if ( !is_readable($file) ) {
				throw new exception\FileNotReadable($file);
			}
			$inputFileInfo = pathinfo($file);
			$this->fileName = $inputFileInfo['filename'];
			$type = $inputFileInfo['extension'];
			$data = file_get_contents($file);
			parent::__construct($data, $type);
		}
	}
}
