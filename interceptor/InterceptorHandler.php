<?
namespace stradivari\interceptor {
    class InterceptorHandler {
        private static $instance = null;
        private $defaultSessionHandler = 'files';
        private $fatalErrorClosure = null;
        private $closures = array(
			'tick' => null,
			'shutdown' => null,
			'error' => null,
			'exception' => null
        );
        private function __construct() {
            $this->defaultSessionHandler = ini_get('session.save_handler');
            register_tick_function(array($this, 'tickHandler'));
            register_shutdown_function(array($this, 'shutdownHandler'));
        }
        private function __clone() {}
        public function __wakeup() {
            throw new exception\SingletonException();
        }
        public static function getInstance() {
            if ( !self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        private function isAnyHandler($name) {
			return $this->closures[$name] && $this->closures[$name]->count();
		}
		private function executeAllHandlers($name) {
			if ( !$this->isAnyHandler($name) ) {
				return;
			}
			foreach($this->closures[$name] as $function) {
				call_user_func($function);
			}
		}
        private function tickHandler() {
			$this->executeAllHandlers('tick');
		}
        public function shutdownHandler() {
			$error = error_get_last();
			$errorTypes = array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR);
			if ( isset($error['type']) && in_array($error['type'], $errorTypes) && $this->isAnyHandler('error') ) {
				call_user_func(array($this, 'errorHandler'), $error['type'], $error['message'], $error['file'], $error['line']);
			}
			$this->executeAllHandlers('shutdown');
		}
		private function errorHandler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array()) {
			foreach($this->closures['error'] as $function) {
				call_user_func($function, $errno, $errstr, $errfile, $errline, $errcontext);
			}
		}
        private function exceptionHandler(\Exception $exception) {
			foreach($this->closures['exception'] as $function) {
				call_user_func($function, $exception);
			}
		}
		public function __get($name) {
			if ( !$this->closures[$name] ) {
				$this->closures[$name] = new Closures($this, $name);
			}
            return $this->closures[$name];
        }
        public function __set($name, $value) {
			if ( $name == 'session' ) {
				$this->setSessionHandler($value);
				return;
			}
			if ( $value == null ) {
				$this->closures[$name] = null;
				$this->changeHandler($name);
				return;
			}
            throw new exception\CanNotSet();
        }
        public function changeHandler($name) {
			if ( $name == 'error' || $name == 'exception' ) {
				if ( $this->isAnyHandler($name) ) {
					$handler = function() use($name) {
						call_user_func_array(array($this, "{$name}Handler"), func_get_args());
					};
				} else {
					$handler = null;
				}
				call_user_func("set_{$name}_handler", $handler);
			}
		}
        private function setSessionHandler(\SessionHandlerInterface $value = null) {
            if ( $value ) {
                session_set_save_handler($value);
            } else {
                ini_set('session.save_handler', $this->defaultSessionHandler);
            }
        }
        
    }
}
