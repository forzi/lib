<?
namespace stradivari\data_converter {
	class UploadedConverter extends AbstractFileConverter {
		public function __construct($fileId) {
			if ( !isset($_FILES['inputFile']) || $_FILES['inputFile']['error'] || !isset($_FILES['inputFile']['tmp_name']) || !is_readable($_FILES['inputFile']['tmp_name']) ) {
				throw new exception\FileNotReadable($_FILES['inputFile']['tmp_name']);
			}
			$inputFileInfo = pathinfo($_FILES['inputFile']['name']);
			$this->fileName = $inputFileInfo['filename'];
			$type = $inputFileInfo['extension'];
			$data = file_get_contents($_FILES['inputFile']['tmp_name']);
			parent::__construct($data, $type);
		}
	}
}
