<?
namespace stradivari\curl {
	abstract class Curl {
		public static $httpsStrict = false; // Выдавать ли ошибку в случае несоответствия сертификата сервера
		public static $maxLen = 1024;
		protected static $defaultPorts = array(
			'http' => 80,
			'https' => 443,
		);
		protected static $arguments = array(
			'url' => '',
			'version' => '1.0',
			'params' => array(),
			'headers' => array(),
			'timeout' => 3 // Timeout in seconds
		);
		protected static $url = array(
			'scheme' => 'http',
			'host' => '',
			'port' => '',
			'user' => '',
			'pass' => '',
			'path' => '',
			'query' => '',
			'fragment' => ''
		);
		protected function createCurl($url, $method, $version, $headers, $params, $timeout) {
			$curl = curl_init();
			
			curl_setopt($curl, CURLOPT_AUTOREFERER, false);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($curl, CURLOPT_COOKIESESSION, false);
			curl_setopt($curl, CURLOPT_CERTINFO, true);
			curl_setopt($curl, CURLOPT_CRLF, false);
			curl_setopt($curl, CURLOPT_FAILONERROR, false);
			curl_setopt($curl, CURLOPT_FILETIME, false);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, false);
			curl_setopt($curl, CURLOPT_FTP_USE_EPRT, true);
			curl_setopt($curl, CURLOPT_FTP_USE_EPSV, true);
			curl_setopt($curl, CURLOPT_FTPAPPEND, false);
			curl_setopt($curl, CURLOPT_FTPLISTONLY, false);
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_NETRC, false);
			curl_setopt($curl, CURLOPT_HTTPGET, false);
			curl_setopt($curl, CURLOPT_NOBODY, false);
			curl_setopt($curl, CURLOPT_POST, false);
			curl_setopt($curl, CURLOPT_PUT, false);
			curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); # Сертификат клиента
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, self::$httpsStrict); # Сертификат сервера
			curl_setopt($curl, CURLOPT_TRANSFERTEXT, false);
			curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, false);
			curl_setopt($curl, CURLOPT_UPLOAD, false);
			curl_setopt($curl, CURLOPT_VERBOSE, false);
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
			
			curl_setopt($curl, CURLOPT_BUFFERSIZE, self::$maxLen); # Макс. буферизация
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout); # timeout соединения
			curl_setopt($curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_DEFAULT);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, constant('CURL_HTTP_VERSION_' . str_replace('.', '_', $version))); #версия протокола
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($curl, CURLOPT_URL, $url['url']); # Полный url
			curl_setopt($curl, CURLOPT_PORT, $url['port']); # порт
			curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); # timeout исполнения
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); # timeout исполнения
			
			$httpHeaders = array();
			foreach ( $headers as $key => &$val ) {
				$httpHeaders[] = $key . ': ' . $val;
			}
			
			curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeaders); # headers
			
			if ( $params ) {
				# параметры post-запроса. Все данные, передаваемые в HTTP POST-запросе. Для передачи файла, укажите перед именем файла @, а также используйте полный путь к файлу. Тип файла также может быть указан с помощью формата ';type=mimetype', следующим за именем файла. Этот параметр может быть передан как в качестве url-закодированной строки, наподобие 'para1=val1&para2=val2&...', так и в виде массива, ключами которого будут имена полей, а значениями - их содержимое. Если value является массивом, заголовок Content-Type будет установлен в значение multipart/form-data. Начиная с версии PHP 5.2.0, при передаче файлов с префиксом @, value должен быть массивом.
				# The full data to post in a HTTP "POST" operation. To post a file, prepend a filename with @ and use the full path. The filetype can be explicitly specified by following the filename with the type in the format ';type=mimetype'. This parameter can either be passed as a urlencoded string like 'para1=val1&para2=val2&...' or as an array with the field name as key and field data as value. If value is an array, the Content-Type header will be set to multipart/form-data. As of PHP 5.2.0, value must be an array if files are passed to this option with the @ prefix. As of PHP 5.5.0, the @ prefix is deprecated and files can be sent using CURLFile.
				curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			}
			if ( $url['user'] && $url['pass'] ) {
				curl_setopt($curl, CURLOPT_USERPWD, "[{$url['user']}]:[{$url['pass']}]"); # Логин и пароль, используемые при соединении, указанные в формате "[username]:[password]"
			}
			
			return $curl;
		}
		protected static function prepareArguments($arguments) {
			$arguments += self::$arguments;
			$arguments['url'] = self::parse_url($arguments['url']);
			$arguments['url'] += self::$url;
			if ( !$arguments['url']['host'] ) {
				$arguments['url']['path'] = explode('/', $arguments['url']['path']);
				$arguments['url']['host'] = array_shift($arguments['url']['path']);
				$arguments['url']['path'] = '/' . implode('/', $arguments['url']['path']);
			}
			if ( !$arguments['url']['port'] ) {
				$arguments['url']['port'] = isset(self::$defaultPorts[$arguments['url']['scheme']]) ? self::$defaultPorts[$arguments['url']['scheme']] : '';
			}
			$arguments['url']['url'] = self::unparse_url($arguments['url']);
			return $arguments;
		}
		protected static function parse_url($url) {
			return parse_url($url);
		}
		protected static function unparse_url($parsed_url) {
			$scheme   = isset($parsed_url['scheme']) && $parsed_url['scheme'] ? $parsed_url['scheme'] . '://' : '';
			$host     = isset($parsed_url['host']) && $parsed_url['host'] ? $parsed_url['host'] : '';
			$port     = isset($parsed_url['port']) && $parsed_url['port'] ? ':' . $parsed_url['port'] : '';
			$user     = isset($parsed_url['user']) && $parsed_url['user'] ? $parsed_url['user'] : '';
			$pass     = isset($parsed_url['pass']) && $parsed_url['pass'] ? ':' . $parsed_url['pass']  : '';
			$pass     = ($user || $pass) ? "{$pass}@" : '';
			$path     = isset($parsed_url['path']) && $parsed_url['path'] ? $parsed_url['path'] : '';
			$query    = isset($parsed_url['query']) && $parsed_url['query'] ? '?' . $parsed_url['query'] : '';
			$fragment = isset($parsed_url['fragment']) && $parsed_url['fragment'] ? '#' . $parsed_url['fragment'] : '';
			return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
		}
		protected static function parseInfo($out, $type = 'response') {
			$parse = explode("\r\n\r\n", $out, 2);
			$parse = count($parse) == 2 ? $parse : explode("\n\n", $out, 2);
			
			if ( count($parse) < 2 ) {
				return false;
			}
			
			$parse[0] = str_replace("\r\n", "\n", $parse[0]);
			$parse[0] = explode("\n", $parse[0]);
			
			$response = array('scheme', 'status_code', 'status_text');
			$request = array('method', 'url', 'scheme');
			$response = array_combine($$type, explode(' ', array_shift($parse[0]), 3));
			
			$response['headers'] = array();
			foreach ( $parse[0] as &$headder ) {
				$splitHead = explode(': ', $headder);
				$response['headers'][$splitHead[0]] = $splitHead[1];
			}
			$response['body'] = isset($parse[1]) ? $parse[1] : '';
			
			return $response;
		}
		public static function __callStatic($name, $arguments) {
			$arguments = isset($arguments[0]) ? $arguments[0] : array();
			$arguments = self::prepareArguments($arguments);
			$arguments['method'] = strtoupper($name);
			$curl = self::createCurl($arguments['url'], $arguments['method'], $arguments['version'], $arguments['headers'], $arguments['params'], $arguments['timeout']);
			$curl_result = curl_exec($curl);
			$curl_info = curl_getinfo($curl);
			$result = array(
				'request' => array(
					'scheme' => $arguments['url']['scheme'] . '/' . $arguments['version'],
					'method' => $arguments['method'],
					'url' => $arguments['url']['url'],
					'headers' => $arguments['headers']
				),
				'response' => array(
					'status_code' => $curl_info['http_code'],
				),
			);
			
			if ( isset($curl_info['certinfo']) ) {
				$result['certinfo'] = $curl_info['certinfo'];
			}
			
			if ( isset($curl_info['request_header']) ) {
				$parseInfo = self::parseInfo($curl_info['request_header'], 'request');
				if ( $parseInfo ) {
					$result['request'] = $parseInfo;
				}
			}
			
			$result['request']['params'] = $arguments['params'];
			
			if ( !$curl_result ) {
				$result['error'] = array('code' => curl_errno($curl), 'text' => curl_error($curl));
				return $result;
			}
			
			$parseOut = self::parseInfo($curl_result);
			if ( !$parseOut ) {
				$result['error'] = array('code' => 0, 'text' => 'can\'t parse');
				$result['response']['out'] = $curl_result;
				return $result;
			}
			
			$result['response'] = $parseOut;
			return $result;
		}
	}
}
