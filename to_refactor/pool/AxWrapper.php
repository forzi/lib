<?
namespace stradivari\pool {
    class AxWrapper {
        private static $singletonePool = array();
        private $className = null;

        public static function getInstance($className) {
            if ( !isset($sinletonePool[$className]) ) {
                $sinletonePool[$className] = new self($className);
            }
            return $sinletonePool[$className];
        }
        private function __construct($className) {
            $this->className = $className;
        }
        private function __clone() {}
        public function __wakeup() {
            throw new exception\NoSuchMethod(__CLASS__ . '::__wakeup');
        }
        public function __set($name, $value) {
            $className = $this->className;
            $className::$$name = $value;
        }
        public function __get($name) {
            $className = $this->className;
            return $className::$$name;
        }
        public function __call($calledMethod, $arguments) {
            return call_user_func_array("{$this->className}::{$calledMethod}", $arguments);
        }
    }
}
