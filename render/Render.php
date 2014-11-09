<?
namespace stradivari\render {
	abstract class Render {
		protected static $locFiles = array();
		public static $phantomPath = null;
		
		public static function getContent(array $params = array()) {
			if ( isset($params['url']) ) {
				return static::shortRender($params);
			}
			if ( isset($params['page']) ) {
				return static::render($params);
			}
			throw new exception\IncorrectArgList();
		}
		protected static function getFromFile($fileName) {
			if ( !is_readable($fileName) ) {
				throw new exception\NoSuchFile();
			}
			return file_get_contents($fileName);
		}
		protected static function shortRender($short) {
			$params = array();
			if ( isset($short['type']) ) {
				$params['type'] = $short['type'];
			}
			if ( isset($short['no_cache']) ) {
				$params['no_cache'] = $short['no_cache'];
			}
			if ( isset($short['url']) ) {
				$params['content']['url'] = $short['url'];
			}
			if ( isset($short['timeout']) ) {
				$params['timeout'] = $short['timeout'];
			}
			if ( isset($short['format']) ) {
				$params['page']['page_setup']['parameters']['paperSize']['format'] = $short['format'];
			}
			if ( isset($short['orientation']) ) {
				$params['page']['page_setup']['parameters']['paperSize']['orientation'] = $short['orientation'];
			}
			return static::render($params);
		}
		protected static function render($params) {
			$defaultParams = new \stradivari\data_converter\FileConverter(__DIR__ . '/default.yaml');
			$params = array_merge_recursive_distinct($defaultParams->array()->data, $params);
			$hash = self::getHash($params);
			$fileName = __DIR__ . "/render/{$hash}.{$params['type']}";
			$lockFileName = __DIR__ . "/tmp/{$hash}.loc";
			
			self::lock_file($lockFileName, true);
			if ( file_exists($fileName) && !$params['no_cache'] ) {
				$result = file_get_contents($fileName);
			} else {
				$result = self::execute($params);
			}
			self::unlock_file($lockFileName);
			return $result;
		}
		protected static function lock_file($fileName, $wait = false) {
			try {
				$locFile = fopen($fileName, 'c');
			} catch ( \Exception $e ) {
				throw new exception\InternalError('Can\'t create lock file!');
			}
			try {
				if ( $wait ) {
					$lock = flock($locFile, LOCK_EX);
				} else {
					$lock = flock($locFile, LOCK_EX | LOCK_NB);
				}
			} catch ( \Exception $e ) {
				throw new exception\InternalError('Can\'t lock file!');
			}
			fprintf($locFile, "%s\n", getmypid());
			self::$locFiles[$fileName] = $locFile;
		}
		protected static function unlock_file($fileName) {
			fclose(self::$locFiles[$fileName]);
			unset(self::$locFiles[$fileName]);
			unlink($fileName);
		}
		protected static function execute($params) {
			$hash = self::getHash($params);
			$paramsFileName = __DIR__ . "/tmp/{$hash}.inf";
			file_put_contents($paramsFileName, json_encode($params));

			$renderFileName = __DIR__ . '/js/render.js';
			$command = self::$phantomPath . " --ignore-ssl-errors=true {$renderFileName} {$paramsFileName}";
			
			$lastStr = exec($command, $response, $returnVar);
			unlink($paramsFileName);
			$returnVar = ( int ) $returnVar;
			
			$logContent = $command . "\n" . implode("\n", $response);
			file_put_contents(__DIR__ . "/render/{$hash}.log", $logContent);
			
			if ( $returnVar ) {
				throw new exception\ExecuteError($lastStr, $returnVar);
			}
			
			$resultFilename = $lastStr;
			$result = file_get_contents($resultFilename);

			if ( $params['no_cache'] ) {
				unlink($resultFilename);
				unlink(__DIR__ . "/render/{$hash}.log");
			}

			return $result;
		}
		protected static function getHash($params) {
			return sha1($params['type'] . json_encode($params['content']) . json_encode($params['page']));
		}
	}
}
