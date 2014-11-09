<?
namespace stradivari\interceptor {
    class ShutdownHandler extends AbstractHandler {
        public static function getInstance() {
            return parent::getInstance('register_shutdown_function');
        }
    }
}