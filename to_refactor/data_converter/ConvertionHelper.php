<?
namespace stradivari\data_converter {
	ConvertionHelper::$tmpFolder = __DIR__ . '/tmp';
	abstract class ConvertionHelper {
		public static $tmpFolder = 'tmp';
		public static function yaml2array($yaml) {
			try {
				$array = \Symfony\Component\Yaml\Yaml::parse($yaml);
			} catch (\Exception $e) {
				throw new exception\IncorrectFormat();
			}
			return $array;
		}
		public static function array2yaml($array) {
			return \Symfony\Component\Yaml\Yaml::dump($array);
		}
		public static function xml2array($xml) {
			try {
				$array = \LSS\XML2Array::createArray($xml);
			} catch (\Exception $e) {
				throw new exception\IncorrectFormat();
			}
			return $array;
		}
		public static function array2xml($array) {
			if ( count($array) == 1 ) {
				$parentNode = key($array);
				$data = current($array);
			} else {
				$parentNode = 'parent_node';
				$data = $array;
			}
			try {
				$xml = \LSS\Array2XML::createXML($parentNode, $data);
			} catch (\Exception $e) {
				throw new exception\IncorrectFormat();
			}
			return $xml->saveXML();
		}
		public static function json2array($data) {
			return json_decode($data, true);
		}
		public static function array2json($array) {
			return  json_encode($array);
		}
		public static function csv2array($data) {
			$fileName = self::$tmpFolder . '/in.csv';
			file_put_contents($fileName, $data);
			try {
				$csvFile = new \Keboola\Csv\CsvFile($fileName);
				$csvFile->rewind(); // :( WTF, Keboola? Why pointer is not on the first element? :(
				$array = self::parseRows($csvFile);
			} catch (\Exception $e) {
				throw new exception\IncorrectFormat();
			}
			unlink($fileName);
			return $array;
		}
		public static function array2csv($array) {
			$fileName = self::$tmpFolder . '/out.csv';
			$csvFile = new \Keboola\Csv\CsvFile($fileName);
			$rows = self::makeRows($array);
			foreach ($rows as &$row) {
				$csvFile->writeRow($row);
			}
			$data = file_get_contents($fileName);
			unlink($fileName);
			return $data;
		}
		private static function makeRows($array, $lvl = 0) {
			$result = array();
			$row = 0;
			foreach ( $array as $key => &$val ) {
				$result[$row] = $lvl ? array_fill(0, $lvl * 2, '') : array();
				
				$key = (string) $key;
				if ( is_array($val) ) {
					$result[$row][] = $key;
					$result[$row][] = '@array@';
					$subresult = self::makeRows($val, $lvl+1);
					foreach ( $subresult as $subVal ) {
						$row += 1;
						$result[$row] = $subVal;
					}
				} else {
					$result[$row][] = $key;
					$subVal = (string) $val;
					$subVal = str_replace("\n", '\n', $subVal); // :( ugly, but csv will be correct :(
					$result[$row][] = (string) $subVal;
				}
				$row += 1;
			}
			
			return $result;
		}
		private static function parseRows(&$rows, $lvl = 0) {
			$countElements = ($lvl + 1) * 2;
			$array = array();
			for ( ; $current = $rows->current(); ) {
				if ( count($current) == $countElements ) {
					$val = array_pop($current);
					$key = array_pop($current);
					$rows->next();
					if ( $val == '@array@' ) {
						$val = self::parseRows($rows, $lvl+1);
					} else {
						$val = str_replace('\n', "\n", $val);
					}
					$array[$key] = $val;
				} else if ( count($rows) > $countElements ) {
					throw new exception\IncorrectFormat();
				} else {
					return $array;
				}
			}
			return $array;
		}
		public static function __callstatic($name, $params) {
			throw new exception\ConvertionDoesntSupports();
		}
	}
}
