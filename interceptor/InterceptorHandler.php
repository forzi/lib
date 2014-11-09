<?
namespace stradivari\interceptor {
    class InterceptorHandler implements \ArrayAccess {
        private static $instance = null;
        private $defaultSessionHandler = 'files';
        private $closures = array(
            'error' => null,
            'exception' => null,
            'session' => null
        );
        private $strategies = array(
            'tick' => array(),
            'shutdown' => array()
        );
        private $fatalErrorClosure = null;
        private function __construct() {
            $this->defaultSessionHandler = ini_get('session.save_handler');
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
        public function offsetExists($offset) {
            return array_key_exists($offset, $this->closures);
        }
        public function offsetGet($offset) {
            if ( array_key_exists($offset, $this->strategies) ) {
                if ( !$this->strategies[$offset] ) {
                    $className = '\\' . __NAMESPACE__ . '\\' . ucfirst($offset) . 'Handler';
                    $this->strategies[$offset] = $className::getInstance();
                }
                return $this->strategies[$offset];
            }
            return $this->closures[$offset]->closure;
        }
        public function offsetSet($offset, $value) {
            foreach ( array('closures', 'strategies') as $handlerType ) {
                if ( array_key_exists($offset, $this->$handlerType) ) {
                    $method = $handlerType . 'Set';
                    $this->$method($offset, $value);
                    return;
                }
            }
            throw new exception\CanNotSet();
        }
        private function closuresSet($offset, $value) {
            $method = 'set' . ucfirst($offset) . 'Handler';
            $this->$method($value);
        }
        private function strategiesSet($offset, $value) {
            $strategie = $this->strategies[$offset];
            foreach ( $strategie as $key => $closure ) {
                $strategie[$key] = null;
            }
        }
        private function setErrorHandler(callable $value = null) {
            $this->closures['error'] = $value;
            set_error_handler($value);
            $this->setFatalErrorHandler($value);
        }
        private function setFatalErrorHandler($value) {
            if ( !$this->fatalErrorClosure ) {
                $this->fatalErrorClosure = new RemovableClosureCreator();
            }
            if ( $value ) {
                $this->fatalErrorClosure->closure = $this->castFatalErrorHandler($value);
                register_shutdown_function($this->fatalErrorClosure->closure);
            } else {
                $this->fatalErrorClosure->closure = null;
            }
        }
        private function castFatalErrorHandler($value) {
            $result = function() use ( $value ) {
                $error = error_get_last();
				$errorTypes = array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR);
                if (  isset($error) && in_array($error['type'], $errorTypes) ) {
                    return call_user_func($value, $error["type"], $error["message"], $error["file"], $error["line"], array());
                }
            };
            return $result;
        }
        private function setExceptionHandler(callable $value = null) {
            $this->closures['exception'] = $value;
            set_exception_handler($value);
        }
        private function setSessionHandler(\SessionHandlerInterface $value = null) {
            if ( $value ) {
                session_set_save_handler($value);
            } else {
                ini_set('session.save_handler', $this->defaultSessionHandler);
            }
        }
        public function offsetUnset($offset) {
            $this[$offset] = null;
        }
        public function __get($name) {
            return $this->offsetGet($name);
        }
        public function __set($name, $value) {
            return $this->offsetSet($name, $value);
        }
    }
}
