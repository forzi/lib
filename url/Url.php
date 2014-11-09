<?
namespace stradivari\url {
	class Url implements \ArrayAccess, \Iterator {
		protected $arguments = array(
			'scheme' => 'http',
			'host' => '',
			'port' => '',
			'user' => '',
			'pass' => '',
			'path' => '',
			'query' => '',
			'fragment' => '',
			'part' => '',
			'url' => ''
		);
		protected $defaultPorts = array(
			'http' => 80,
			'https' => 443,
		);
		
		public function __construct($argument) {
			$argument = is_string($argument) ? parse_url($argument) : $argument;
			$this->unparseUrl($argument);
		}
		protected function parseUrl($url) {
			$this->arguments = array_merge( $this->arguments, parse_url($url));
			$this->arguments['part'] = $this->arguments['path'] . '?' . $this->arguments['query'];
			$this->arguments['url'] = $url;
		}
		protected function unparseUrl(array $arguments) {
			$scheme   = isset($arguments['scheme']) && $arguments['scheme'] ? $arguments['scheme'] . '://' : '';
			$host     = isset($arguments['host']) && $arguments['host'] ? $arguments['host'] : '';
			$port     = isset($arguments['port']) && $arguments['port'] ? ':' . $arguments['port'] : '';
			$user     = isset($arguments['user']) && $arguments['user'] ? $arguments['user'] : '';
			$pass     = isset($arguments['pass']) && $arguments['pass'] ? ':' . $arguments['pass']  : '';
			$pass     = ($user || $pass) ? "{$pass}@" : '';
			$path     = isset($arguments['path']) && $arguments['path'] ? $arguments['path'] : '';
			$query    = isset($arguments['query']) && $arguments['query'] ? '?' . $arguments['query'] : '';
			$fragment = isset($arguments['fragment']) && $arguments['fragment'] ? '#' . $arguments['fragment'] : '';
			$url = $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
			$this->parseUrl($url);
		}
		public function offsetExists($key) {
			return isset($this->arguments[$key]);
		}
		public function offsetGet($key) {
			return $this->arguments[$key];
		}
		public function offsetSet($key, $value) {
			if ( !array_key_exists($key, $this->arguments) ) {
				throw new exception\CanNotSet();
			}
			if ( $key == 'url' ) {
				$arguments = parse_url($value);
			} else {
				if ( $key == 'part' ) {
					$questionPosition = strpos($value, '?');
					if ( $questionPosition === false ) {
						$this->arguments['path'] = $value;
						$this->arguments['query'] = '';
					} else {
						$this->arguments['path'] = substr($value, 0, $questionPosition);
						$this->arguments['query'] = substr($value, $questionPosition+1);
					}
				} else if ( isset($this->arguments[$key]) ) {
					$this->arguments[$key] = $value;
				}
				$arguments = $this->arguments;
				$this->unparseUrl($arguments);
			}
		}
		public function offsetUnset($key) {
			throw new exception\CanNotUnset();
		}
		public function current() {
			return current($this->arguments);
		}
		public function next() {
			return next($this->arguments);
		}
		public function key() {
			return key($this->arguments);
		}
		public function rewind() {
			reset($this->arguments);
		}
		public function valid() {
			return key($this->arguments) !== null;
		}
		public function __toString() {
			return $this->arguments['url'];
		}
		public function __toArray() {
			return toArray($this);
		}
	}
}
